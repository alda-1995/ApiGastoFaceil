<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];

    public function gastos(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'transaction_products', 'transaction_id_foreign', 'product_id_foreign');
    }
}
