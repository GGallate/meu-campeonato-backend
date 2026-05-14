<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campeonato extends Model
{
    use HasFactory;

    protected $fillable = ['campeao', 'vice_campeao', 'terceiro_lugar', 'chaves'];

    protected $casts = [
        'chaves' => 'array'
    ];
}