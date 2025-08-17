<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $fillable = [
        'transaction_id','user_id','entry_type','amount','prev_hash','hash'
    ];

    public function transaction(){ return $this->belongsTo(Transaction::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
