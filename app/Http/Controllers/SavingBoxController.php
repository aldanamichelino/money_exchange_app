<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;
use App\Models\SavingBox;
use App\Models\Currency;
use App\Models\Account;
use Fixerio;

class SavingBoxController extends Controller
{
    public function depositPesos(Request $request){

        try {

            SavingBox::depositPesosInBox($request);

            Alert::success('¡Listo!', 'Depositaste '.$request->deposit.' pesos argentinos en tu caja.');

            return redirect('/dashboard');

        } catch(Throwable $e) {

            Alert::error('¡Atención!', 'Hubo un problema para depositar el dinero: '.$e);

            return redirect('/dashboard');

        }
    }

    public function store(Request $request){

        $box = Account::where(['user_id' => auth()->id()])
            ->join('saving_boxes', 'saving_boxes.account_id', '=', 'accounts.id')
            ->where('saving_boxes.currency_id', '=', $request->currency)
            ->first();

        if($box){

            Alert::error('¡Vaya!', 'Ya tenés una cuenta en esta moneda.');

            return redirect('/dashboard/crearCaja');

        } else {

            try{
                SavingBox::createSavingBox($request);

                Alert::success('¡Listo!', 'Creaste una nueva caja en '.$request->currency.'.');

                return redirect('/dashboard');

            } catch (Throwable $e){

                Alert::error('¡Atención!', 'Hubo un problema para crear esta cuenta: '.$e);

                return redirect('/dashboard');
            }
        }

    }

    public function showInfo(){

            try {

                $list_of_currencies = DB::table('currencies')
                ->join('saving_boxes', 'saving_boxes.currency_id', '=', 'currencies.id')
                ->where('saving_boxes.account_id', app('user_account')->id)
                ->get();

                //devuelve la vista según la ruta elegida en dashboard
                if(strpos($_SERVER['PATH_INFO'], 'formularioCompra')){

                    return view('buyCurrency', compact(
                        'list_of_currencies',
                    ));

                } else {

                    return view('sellCurrency', compact(
                        'list_of_currencies',
                    ));

                }



            } catch(Throwable $e){

                Alert::error('¡Atención!', 'Hubo un problema para mostrar esta información: '.$e.'.');
        }


    }

    public function buyCurrency(Request $request){

        if($request->currency == null){
            Alert::error('¡Atención!', 'Elegí una moneda para comprar.');
        } else if($request->currency == $request->buying_currency){
            Alert::error('¡Atención!', 'La moneda origen y la moneda meta no pueden ser iguales.');
        } else if($request->targetAmount == null){
            Alert::error('¡Atención!', 'Ingresá una cantidad.');
        } else if(!preg_match("/^[0-9]{1,}([,.]{1}[0-9]{1,2})?$/", $request->targetAmount)){
            Alert::error('¡Atención!', 'Ingresá una cantidad válida.');
        } else if($request->balance == null){
            Alert::error('¡Atención!', 'Elegí con qué moneda comprar.');
        } else {

            try{

                $balance = str_replace(',', '.', $request->balance);
                $balance = floatval($request->balance);

                $transaction_price = SavingBox::getTransactionPrice($request);

                if($transaction_price > $balance){

                    $currency = Currency::where('id', $request->buying_currency)->value('currency_code');

                    Alert::error('¡Atención!', 'No tenés suficiente saldo. Costo de la operación: '.$transaction_price.' '.$currency.'. Ingresá más dinero en la cuenta con la que querés comprar.');
                    return redirect('/dashboard/formularioCompra');

                } else {

                    $new_origin_balance = $balance - $transaction_price;

                    //descontar el costo de la caja origen
                    $updated_origin_box_balance = SavingBox::where(['currency_id' => $request->buying_currency, 'account_id' => app('user_account')->id])
                    ->update([
                        'balance' => $new_origin_balance,
                    ]);

                    $target_saving_box = DB::table('currencies')
                    ->where('currencies.id', $request->currency)
                    ->join('saving_boxes', 'saving_boxes.currency_id', '=', 'currencies.id')
                    ->where('saving_boxes.account_id', app('user_account')->id)
                    ->select('saving_boxes.*')
                    ->first();

                    $new_target_balance = $target_saving_box->balance + $request->targetAmount;

                    //sumar monto target a la caja target
                    $updated_target_saving_box = SavingBox::where(['currency_id' => $request->currency, 'account_id' => app('user_account')->id])
                    ->update([
                        'balance' => $new_target_balance,
                    ]);

                    Alert::success('¡Listo!', 'Operación exitosa.');
                    return redirect('/dashboard');
                }



            } catch(Throwable $e){

                Alert::error('¡Atención!', 'Hubo un problema con esta transacción: '.$e.'.');
                return redirect('/dashboard');
            }
        }

    }


    public function sellCurrency(Request $request){

            if($request->currency == null){
                Alert::error('¡Atención!', 'Elegí una moneda para vender.');
            } else if($request->currency == $request->buying_currency){
                Alert::error('¡Atención!', 'La moneda origen y la moneda meta no pueden ser iguales.');
            } else if($request->targetAmount == null){
                Alert::error('¡Atención!', 'Ingresá una cantidad.');
            } else if(!preg_match("/^[0-9]{1,}([,.]{1}[0-9]{1,2})?$/", $request->targetAmount)){
                Alert::error('¡Atención!', 'Ingresá una cantidad válida.');
            } else if($request->buying_currency == null){
                Alert::error('¡Atención!', 'Elegí a qué moneda vender.');
            } else if($request->balance == null){
                Alert::error('¡Atención!', 'Elegí con qué moneda comprar.');
            } else {

                try{

                    $balance = str_replace(',', '.', $request->balance);
                    $balance = floatval($request->balance);

                    $targetAmount = floatval($request->targetAmount);
                    $targetAmount = round($targetAmount, 2);

                    $transaction_price = SavingBox::getTransactionPrice($request);


                    if($transaction_price > $balance){

                        Alert::error('¡Atención!', 'La cantidad a comprar no puede ser mayor que el saldo.');

                        return redirect('/dashboard/formularioVenta');

                    } else {

                        $new_origin_balance = $balance - $targetAmount;

                        //descontar el costo de la caja origen
                        $updated_origin_box_balance = SavingBox::where(['currency_id' => $request->currency, 'account_id' => app('user_account')->id])
                        ->update([
                            'balance' => $new_origin_balance,
                        ]);

                        $target_saving_box = DB::table('currencies')
                        ->where('currencies.id', $request->buying_currency)
                        ->join('saving_boxes', 'saving_boxes.currency_id', '=', 'currencies.id')
                        ->where('saving_boxes.account_id', app('user_account')->id)
                        ->select('saving_boxes.*')
                        ->first();

                        $new_target_balance = $target_saving_box->balance + $transaction_price;

                        //sumar monto target a la caja target
                        $updated_target_saving_box = SavingBox::where(['currency_id' => $request->buying_currency, 'account_id' => app('user_account')->id])
                        ->update([
                            'balance' => $new_target_balance,
                        ]);

                        Alert::success('¡Listo!', 'Operación exitosa.');
                        return redirect('/dashboard');

                    }

                } catch(Throwable $e){

                    Alert::error('¡Atención!', 'Hubo un problema con esta transacción: '.$e.'.');
                    return redirect('/dashboard');
                }
            }


    }

}
