<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketiPodderzhki extends Model
{
    use HasFactory;

    protected $table = 'ticketi_podderzhki';

    protected $fillable = ['polzovatel_id', 'tema', 'status', 'moderator_id'];

    public function polzovatel()
    {
        return $this->belongsTo(Polzovateli::class, 'polzovatel_id');
    }

    public function moderator()
    {
        return $this->belongsTo(Polzovateli::class, 'moderator_id');
    }

    public function soobsheniya()
    {
        return $this->hasMany(SoobsheniyaTicketov::class, 'ticket_id');
    }
}
