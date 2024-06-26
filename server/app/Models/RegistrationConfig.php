<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationConfig extends Model
{
    use HasFactory;
    protected $primaryKey = "reg_config_id";
    protected $casts = [
        'deadline_list' => 'array',
        'options' => 'array',
    ];
}
