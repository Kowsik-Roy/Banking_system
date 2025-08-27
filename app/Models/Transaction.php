<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'sender_id','receiver_id','amount','status','session_id','hmac','shared_key','encrypted_payload'
    ];

    public function sender(){ return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver(){ return $this->belongsTo(User::class, 'receiver_id'); }
    public function ledgerEntries(){ return $this->hasMany(LedgerEntry::class); }
}
