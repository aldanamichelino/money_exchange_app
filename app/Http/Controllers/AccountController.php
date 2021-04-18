<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Account;

class AccountController extends Controller
{
    public function create(){
        if(auth()->user()->account_id != null){
            Alert::error('¡Atención!', 'Ya tienes una cuenta creada.');

        } else {

            try {
                Account::createAccount();

                Alert::success('¡Listo!', 'Creaste tu cuenta principal.');

                return redirect('/dashboard');

            } catch(Throwable $e){

                Alert::error('¡Atención!', 'Hubo un problema para crear la cuenta: '.$e);

                return redirect('/dashboard');
            }
        }
    }
}
