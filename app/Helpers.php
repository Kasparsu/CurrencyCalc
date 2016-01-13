<?php


namespace App;

use Curl;
use Parser;
class Helpers
{
    // Tõlgendab feedi kujule
    //array([date], array{[currency],[value]), array{[currency],[value]), .... )


    public static function parseEestiPank($date)
    {
        $datearray = explode("-", $date);
        $formatedDate = date("d.m.Y", mktime(0, 0, 0, $datearray[1], $datearray[2], $datearray[0]));
        //echo $formatedDate;
        $response = Curl::to('https://www.eestipank.ee/valuutakursid/export/xml/latest?imported_at=' . $formatedDate)->get();
        $parsed = Parser::xml($response);
        $i=0;
        $data["date"] = $parsed['Cube']['Cube']['@attributes']['time'];
        if($data["date"] == $date) {
            foreach ($parsed['Cube']['Cube']['Cube'] as $currency) {
                $data[$i]['currency'] = $currency['@attributes']['currency'];
                $data[$i]['value'] = $currency['@attributes']['rate'];
                $i++;
            }
        }
        else{
            $data['date']=$date;
        }

        return $data;
    }
    // Tõlgendab feedi kujule
    //array([date], array{[currency],[value]), array{[currency],[value]), .... )
    public static function parseLeeduPank($date)
    {

        $response = Curl::to('https://www.lb.lt/fxrates_csv.lb?tp=EU&rs=1&dte=' . $date)->get();
        $parsed = explode("\n", $response);
        $temp = explode(",", $parsed[0]);
        $data['date'] = substr($temp[3], 0, -1);
        $i=0;
        if($data['date'] == $date)
        {
            foreach ($parsed as $parse) {
                $temp = explode(",", $parse);
                $data[$i]['currency'] = $temp[1];
                $data[$i]['value'] = $temp[2];
                $i++;
            }
        }
        else{
            $data['date']=$date;
        }
        return $data;
    }
    //kutsub välja kõigi parsimiste salvestamise
    //Siia lisada uus parsimis funktsoon
    public static function StoreAll($date)
    {
        self::storeData(self::parseLeeduPank($date));
        self::storeData(self::parseEestiPank($date));

    }

    //salvestab parsimisest saadud array andmebaasi juhul, kui juba seda infot pole seal. kui parsimise tulemus on tühi siis sisestab ühe rea nulle, et jäta märk, et see kuupäev on läbi vaadtatud juba.
    public static function storeData($data)
    {
        $date = $data['date'];
        unset($data['date']);
        if(!empty($data)) {
            foreach ($data as $tempdata) {
                $dbcurrency = Currency::where('currency_name', $tempdata['currency'])->get();
                if (empty($dbcurrency->all())) {
                    $newcurrency = new Currency;
                    $newcurrency->currency_name = $tempdata['currency'];
                    $newcurrency->save();
                }
                $currencyid = Currency::where('currency_name', $tempdata['currency'])->first()->id;
                $search = ['date' => $date, 'currency_id' => $currencyid];
                $dbvalues = Value::where($search)->get();
                if (empty($dbvalues->all())) {
                    $newvalue = new Value;
                    $newvalue->currency_id = $currencyid;
                    $newvalue->value = $tempdata['value'];
                    $newvalue->date = $date;
                    $newvalue->save();
                }
            }
        }
        else
        {
            $search = ['date' => $date, 'currency_id' => '0'];
            $dbvalues = Value::where($search)->get();
            if (empty($dbvalues->all())) {
                $newvalue = new Value;
                $newvalue->currency_id = '0';
                $newvalue->value = '0';
                $newvalue->date = $date;
                $newvalue->save();
            }

        }

    }
    //kontrollib andmebaasist väärtusi. Kas on väärtusi olemas, kas on väärtus olemas et selle päeva kohta väärtusi pole ja või siis pole väärtusi
    public static function checkDB($date)
    {
        $dbvalues = Value::where('date', $date)->first();
          if(empty( $dbvalues ))
          {
              return 0;
          }
        elseif($dbvalues->currency_id == 0)
        {
            return 2;
        }
        else
        {
            return 1;
        }
    }

    // teeb arvutused kalkulaatorile
    public static function calculate($date, $fromCur, $toCur, $amount)
    {
        $search = ['date' => $date, 'currency_id' => $fromCur];
        $fromCurVal = Value::where($search)->first()->value;
        $search = ['date' => $date, 'currency_id' => $toCur];
        $toCurVal = Value::where($search)->first()->value;

        return ($amount * $fromCurVal) / $toCurVal;
    }
}