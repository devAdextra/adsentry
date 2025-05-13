<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movement extends Model
{
    use HasFactory;

    protected $table = 'movements';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'macro',
        'micro',
        'nano',
        'extra',
        'timeActionOpen',
        'timeActionClose'
        // e così via, in base alle tue colonne
    ];

    // Relazione inversa: un movimento appartiene a un lead
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'user_id');
    }
}
