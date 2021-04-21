<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Database\Eloquent\Model;
use App\Models\SavingBox;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Fixerio;
use Carbon\Carbon;

class SavingBox extends Model
{
    use HasFactory;

    protected $fillable = ['currency_id', 'account_id', 'balance'];

    protected $dates = ['created_at', 'updated_at'];

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

    public static function getTransactionPrice($data){

        $currency_to_buy = Currency::where('id', $data->currency)->value('currency_code');
        $buying_currency = Currency::where('id', $data->buying_currency)->value('currency_code');

        $data->balance = str_replace(',', '.', $data->balance);
        $balance = floatval($data->balance);

        if(str_contains($data->targetAmount, ',')){
            $data->targetAmount = str_replace(',', '.', $data->targetAmount);
        }

        $targetAmount = floatval($data->targetAmount);
        $targetAmount = round($targetAmount, 2);

        $latestRates = Fixerio::latest();
        $latestRates = $latestRates['rates'];

        //trae las cotizaciones con base en el euro
        $selected_currency_exchange_to_euro = $latestRates[$currency_to_buy];
        $original_currency_exchange_to_euro = $latestRates[$buying_currency];

        //tasa de cambio entre moneda origen y moneda meta
        $exchangeRate = round($original_currency_exchange_to_euro, 2) / round($selected_currency_exchange_to_euro, 2);

        //costo en moneda origen de la transacción
        $transaction_price = $targetAmount * round($exchangeRate, 2);

        return $transaction_price;

    }
}
