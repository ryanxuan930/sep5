<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDepartment extends Model
{
    use HasFactory;
    protected $primaryKey = "admin_dept_id";
    protected $casts = [
        'sport_management_list' => 'array',
    ];
}
