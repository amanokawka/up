<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bani extends Model
{
    use HasFactory;

    protected $table = 'bani';

    protected $fillable = ['polzovatel_id', 'moderator_id', 'prichina', 'aktiven'];

    public function polzovatel()
    {
        return $this->belongsTo(Polzovateli::class, 'polzovatel_id');
    }

    public function moderator()
    {
        return $this->belongsTo(Polzovateli::class, 'moderator_id');
    }
}