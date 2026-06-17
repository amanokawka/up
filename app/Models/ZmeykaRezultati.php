<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZmeykaRezultati extends Model
{
    use HasFactory;

    protected $table = 'zmeyka_rezultati';

    protected $fillable = ['polzovatel_id', 'dlina', 'ochki', 'vremya_sek'];

    public function polzovatel()
    {
        return $this->belongsTo(Polzovateli::class, 'polzovatel_id');
    }
}