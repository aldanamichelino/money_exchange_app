<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SavingBox;
use Fixerio;

class SavingBox extends Model
{
    use HasFactory;

    protected $fillable = ['currency_id', 'account_id', 'balance'];

    public function account(){
        return $this->belongsTo(Account::class);
    }

    public static function depositPesosInBox($data){
        $newDeposit = SavingBox::where(['account_id' => $data->account, 'currency_id' => 1])->first();

        $newDeposit->update([
            'balance' => $newDeposit->balance + $data->deposit,
        ]);

        return $newDeposit;

    }

    public static function createSavingBox($data){

        $newBox = SavingBox::create([
            'currency_id' => $data->currency,
            'account_id' => $data->account,
            ]);

            return $newBox;
    }

    public static function buyCurrency($data){


        $latestRates = Fixerio::latest();
        $latestRates = $latestRates['rates'];

        //trae las cotizaciones con base en el euro
        $selected_currency_exchange_to_euro = $latestRates[$data->currency];
        $original_currency_exchange_to_euro = $latestRates[$data->originCurrency];

        //tasa de cambio entre moneda origen y moneda meta
        $exchangeRate = $original_currency_exchange_to_euro / $selected_currency_exchange_to_euro;


        $transaction_price = $data->targetAmount * number_format($exchangeRate, 2, ',', '');

        $transaction_amount = $data->balance - $transaction_price;

        dd($transaction_amount);

    }
}
