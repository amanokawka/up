<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Polzovateli extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'polzovateli';

    protected $fillable = [
        'login',
        'parol',
        'imya',
        'avatar',
        'rol_id',
    ];

    protected $hidden = [
        'parol',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->parol;
    }

    public function getAuthIdentifierName()
    {
        return 'login';
    }

    public function rol()
    {
        return $this->belongsTo(Roli::class, 'rol_id');
    }

    public function bani()
    {
        return $this->hasMany(Bani::class, 'polzovatel_id');
    }

    public function sudokuRezultati()
    {
        return $this->hasMany(SudokuRezultati::class, 'polzovatel_id');
    }

    public function naidiParuRezultati()
    {
        return $this->hasMany(NaidiParuRezultati::class, 'polzovatel_id');
    }

    public function zmeykaRezultati()
    {
        return $this->hasMany(ZmeykaRezultati::class, 'polzovatel_id');
    }

    public function sozdannyeTiketi()
    {
        return $this->hasMany(TiketiPodderzhki::class, 'polzovatel_id');
    }

    public function naznachennyeTiketi()
    {
        return $this->hasMany(TiketiPodderzhki::class, 'moderator_id');
    }

    public function soobsheniya()
    {
        return $this->hasMany(SoobsheniyaTicketov::class, 'polzovatel_id');
    }
}