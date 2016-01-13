<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Value extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'currency_id','value','date'
    ];



}