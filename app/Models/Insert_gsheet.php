<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insert_gsheet extends Model
{
    use HasFactory;
    protected $table = "insert_data";
    protected $guarded = ['id'];
}