<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $primaryKey = 'symbol';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'symbol',
        'name',
        'sector',
        'last_price',
        'is_dividend_paying',
        'last_dividend_amount',
        'last_dividend_date',
    ];

    public function dividends(): HasMany
    {
        return $this->hasMany(Dividend::class, 'symbol', 'symbol');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'symbol', 'symbol');
    }
}
