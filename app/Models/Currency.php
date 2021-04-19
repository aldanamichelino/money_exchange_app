<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Fixerio;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['currency_name', 'currency_code', 'currency_symbol'];

    public static function getLatestExchangeRate(){

        $latestRates = Fixerio::latest();

    }

}
