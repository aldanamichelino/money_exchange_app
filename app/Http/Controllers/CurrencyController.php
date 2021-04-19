<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function index(){
        $currencies = Currency::all();
    }

    public function LatestRates(){
        try {
            Currency::getLatestExchangeRate();
        } catch(Throwable $e){
            echo 'Algo salió mal: '.$e;
        }
    }
}
