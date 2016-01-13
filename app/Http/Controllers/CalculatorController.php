<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalculateRequest;
use App\Currency;
use App\Helpers;
class CalculatorController extends Controller
{
    public function __construct()
    {


    }
    public function index()
    {
        //krabab tänase päeva feedi
        $date = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
        if (Helpers::CheckDB($date) == 0) {
            Helpers::StoreAll($date);
        }
        $dbcurrency =  Currency::lists('currency_name','id');
        return view('index', compact('dbcurrency'));
    }
    public function calculate(CalculateRequest $request)
    {
        $date = $request->input('kuup2ev');
        $fromCur = $request->input('l2htevaluuta');
        $toCur = $request->input('sihtvaluuta');
        $amount = $request->input('l2htesumma');
        //kontrollib, et kasutaja tuleviku aegu ei sooviks
        if(strtotime($date) <= strtotime('today'))
        {
            $checkDB = Helpers::CheckDB($date);
            if ($checkDB == 0) {
                Helpers::StoreAll($date);
            }
            $checkDB = Helpers::CheckDB($date);
            if($checkDB == 2){
                echo "Vabandame ei suutnud otsitud andmeid leida!";
            }
            else
            {
                $answer = Helpers::calculate($date, $fromCur, $toCur, $amount);
                echo $answer;
            }
        }
        else
        {
            echo "Infot pole veel avaldatud! Proovi mõnda varasemat kuupäeva";
        }



    }

}
