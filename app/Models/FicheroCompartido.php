<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FicheroCompartido extends Model
{
    protected $fillable = ['fichero_id', 'user_id'];

    public function fichero(): BelongsTo
    {
        return $this->belongsTo(Fichero::class, 'fichero_id');
    }
}
