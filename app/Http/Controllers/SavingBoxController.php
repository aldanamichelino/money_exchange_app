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

    public function showBuyInfo($currency_id){

        $box = SavingBox::where(['currency_id' => $currency_id, 'account_id' => app('user_account')->id])->first();

        if($box->balance <= 0){

            Alert::error('¡Atención!', 'No tienes dinero en esta caja. Compra divisas primero.');

            return redirect('/dashboard');

        } else {

            try {

                $latestRates = Fixerio::latest();
                $latestRates = $latestRates['rates'];

                $originCurrency = Currency::where('id', $currency_id)->value('currency_name');

                $originCurrency_code = Currency::where('id', $currency_id)->value('currency_code');


                $list_of_currencies = DB::table('currencies')
                ->join('saving_boxes', 'saving_boxes.currency_id', '=', 'currencies.id')
                ->where('saving_boxes.account_id', app('user_account')->id)
                ->get();

                $balance = $box->balance;

                return view('buyCurrency', compact(
                    'currency_id',
                    'originCurrency',
                    'list_of_currencies',
                    'balance',
                    'latestRates',
                    'originCurrency_code',
                    'box'

                ));

            } catch(Throwable $e){

                Alert::error('¡Atención!', 'Hubo un problema con esta transacción: '.$e.'.');
            }

        }

    }

    public function buyNewCurrency(Request $request){

        try{

            SavingBox::buyCurrency($request);

            Alert::success('¡Listo!', 'Compraste '.$request->targetAmount.' '.$request->currency.'.');
            return redirect('/dashboard');

        } catch(Throwable $e){

            Alert::error('¡Atención!', 'Hubo un problema con esta transacción: '.$e.'.');
            return redirect('/dashboard');
        }

    }

}
