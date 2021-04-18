<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\SavingBox;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public static function createAccount(){

        //crea nueva cuenta para el usuario
        $newAccount = Account::create([
            'user_id' => auth()->id()
        ]);

        //crea una caja en pesos por default asociada a la cuenta creada
        $newPesosBox = SavingBox::create([
            'currency_id' => 1,
            'account_id' => $newAccount->id,
        ]);

    }

}
