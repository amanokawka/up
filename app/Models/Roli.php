<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roli extends Model
{
    use HasFactory;

    protected $table = 'roli';

    protected $fillable = ['code', 'nazvanie'];

    public function polzovateli()
    {
        return $this->hasMany(Polzovateli::class, 'rol_id');
    }
}