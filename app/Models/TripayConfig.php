<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripayConfig extends Model
{
    use HasFactory;
    protected $table = 'tripay_config';
    protected $fillable = ['is_enabled', 'api_key', 'private_key', 'merchant_code', 'payment_channel_url', 'transaction_create_url', 'transaction_detail_url'];
}
