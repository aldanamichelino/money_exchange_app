<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SavingBox;

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
}
