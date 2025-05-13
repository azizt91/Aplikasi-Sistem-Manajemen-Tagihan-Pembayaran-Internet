<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fonnte extends Model
{
    use HasFactory;
    protected $table = 'fonnte'; 
    protected $fillable = ['token'];
    public $timestamps = false;
}
