<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoobsheniyaTicketov extends Model
{
    use HasFactory;

    protected $table = 'soobsheniya_ticketov';

    protected $fillable = ['ticket_id', 'polzovatel_id', 'tekst', 'ot_personala'];

    public function ticket()
    {
        return $this->belongsTo(TiketiPodderzhki::class, 'ticket_id');
    }

    public function polzovatel()
    {
        return $this->belongsTo(Polzovateli::class, 'polzovatel_id');
    }
}