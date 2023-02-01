<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class Game extends Model
{
    use HasFactory;
    protected $primaryKey = "game_id";
    protected $casts = [
        'host_list' => 'array',
        'tags' => 'array',
        'selected_list' => AsCollection::class,
    ];
}
