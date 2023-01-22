<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminOrganization extends Model
{
    use HasFactory;
    protected $table = "admin_departments";
    protected $primaryKey = "admin_dept_id";
}
