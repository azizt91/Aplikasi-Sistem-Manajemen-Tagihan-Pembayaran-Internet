<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FonnteNotificationSetting extends Model
{
    use HasFactory;
    protected $fillable = ['is_active', 'send_date_option', 'custom_message'];
    public $timestamps = false;
}
