<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_id';
    protected $fillable = [
        'amount',
        'description',
        'currentDate',
        'income',
        'user_id'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'transaction_products', 'transaction_id_foreign', 'product_id_foreign');
    }
}
