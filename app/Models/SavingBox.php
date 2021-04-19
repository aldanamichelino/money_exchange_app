<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SavingBox;
use Illuminate\Support\Facades\DB;
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
        $selected_currency_exchange_to_euro = $latestRates[$data->currency];
        $original_currency_exchange_to_euro = $latestRates[$data->originCurrency];

        //tasa de cambio entre moneda origen y moneda meta
        $exchangeRate = round($original_currency_exchange_to_euro, 2) / round($selected_currency_exchange_to_euro, 2);

        //costo en moneda origen de la transacción
        $transaction_price = $targetAmount * round($exchangeRate, 2);

        $new_origin_balance = $balance - $transaction_price;

        //descontar el costo de la caja origen
        $updated_origin_box_balance = SavingBox::where('id', $data->origin_sb)
        ->update([
            'balance' => $new_origin_balance,
        ]);

        $target_saving_box = DB::table('currencies')
        ->where('currency_code', $data->currency)
        ->join('saving_boxes', 'saving_boxes.currency_id', '=', 'currencies.id')
        ->select('saving_boxes.*')
        ->first();

        //sumar monto target a la caja target
        $updated_target_saving_box = SavingBox::where('id', $target_saving_box->id)
        ->update([
            'balance' => $targetAmount,
        ]);
    }
}
