<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Database\Eloquent\Model;
use App\Models\SavingBox;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Spatie\Async\Pool;
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

        $currency_to_buy = Currency::where('id', $data->currency)->value('currency_code');
        $buying_currency = Currency::where('id', $data->buying_currency)->value('currency_code');

        $data->balance = str_replace(',', '.', $data->balance);
        $balance = floatval($data->balance);

        if(str_contains($data->targetAmount, ',')){
            $data->targetAmount = str_replace(',', '.', $data->targetAmount);
        }

        $targetAmount = floatval($data->targetAmount);
        $targetAmount = round($targetAmount, 2);

        $mypool = Pool::create();

        $mypool[] = async(){

            $latestRates = Fixerio::latest();
            $latestRates = $latestRates['rates'];

            //trae las cotizaciones con base en el euro
            $selected_currency_exchange_to_euro = $latestRates[$currency_to_buy];
            $original_currency_exchange_to_euro = $latestRates[$buying_currency];

            //tasa de cambio entre moneda origen y moneda meta
            $exchangeRate = round($original_currency_exchange_to_euro, 2) / round($selected_currency_exchange_to_euro, 2);

            //costo en moneda origen de la transacción
            $transaction_price = $targetAmount * round($exchangeRate, 2);

        }




            if($transaction_price > $balance){

                Alert::error('¡Error!', 'No tienes saldo suficiente. Costo de la operación: '.$transaction_price.'.');

                return redirect('/dashboard/formularioCompra');

            } else {
                $new_origin_balance = $balance - $transaction_price;

            //descontar el costo de la caja origen
            $updated_origin_box_balance = SavingBox::where(['currency_id' => $data->buying_currency, 'account_id' => app('user_account')->id])
            ->update([
                'balance' => $new_origin_balance,
            ]);

            $target_saving_box = DB::table('currencies')
            ->where('currency_code', $currency_to_buy)
            ->join('saving_boxes', 'saving_boxes.currency_id', '=', 'currencies.id')
            ->select('saving_boxes.*')
            ->first();

            $new_target_balance = $target_saving_box->balance + $targetAmount;

            //sumar monto target a la caja target
            $updated_target_saving_box = SavingBox::where(['currency_id' => $data->currency, 'account_id' => app('user_account')->id])
            ->update([
                'balance' => $new_target_balance,
            ]);
        }
    }
}
