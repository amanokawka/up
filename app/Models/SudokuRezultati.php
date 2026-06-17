<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SudokuRezultati extends Model
{
    use HasFactory;

    protected $table = 'sudoku_rezultati';

    protected $fillable = ['polzovatel_id', 'slozhnost', 'ochki', 'vremya_sek', 'zaversheno'];

    public function polzovatel()
    {
        return $this->belongsTo(Polzovateli::class, 'polzovatel_id');
    }
}