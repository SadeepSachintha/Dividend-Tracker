<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dividend extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'amount_per_share',
        'type',
        'xd_date',
        'payment_date',
    ];
}
