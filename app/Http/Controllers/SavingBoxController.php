<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\SavingBox;
use App\Models\Currency;
use App\Models\Account;

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
}
