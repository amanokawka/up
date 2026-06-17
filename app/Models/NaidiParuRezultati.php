<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NaidiParuRezultati extends Model
{
    use HasFactory;

    protected $table = 'naidi_paru_rezultati';

    protected $fillable = ['polzovatel_id', 'kolichestvo_par', 'hodi', 'vremya_sek', 'ochki'];

    public function polzovatel()
    {
        return $this->belongsTo(Polzovateli::class, 'polzovatel_id');
    }
}