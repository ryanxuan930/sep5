<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUnion extends Model
{
    use HasFactory;
    protected $table = "admin_unions";
    protected $primaryKey = "admin_union_id";
}
