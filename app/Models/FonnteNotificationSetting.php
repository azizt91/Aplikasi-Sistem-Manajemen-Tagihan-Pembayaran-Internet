<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FonnteNotificationSetting extends Model
{
    use HasFactory;
    protected $fillable = ['custom_message', 'delay_seconds'];
    public $timestamps = false;
}
