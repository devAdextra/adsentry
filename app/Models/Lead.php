<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';
    protected $primaryKey = 'id';

    // Se la tabella ha colonne come "created_at" e "updated_at" puoi lasciare
    // la gestione di default di Eloquent. Altrimenti, se non esistono, disabilita i timestamp:
    public $timestamps = false;

    // Se serve protezione per mass assignment:
    protected $fillable = [
        'email',
        'nome',
        'cognome',
        'citta',
        'cap'
    ];

    // Relazione con i movimenti (uno a molti)
    public function movements()
    {
        return $this->hasMany(Movement::class, 'user_id');
    }
}
