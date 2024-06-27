<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionProduct extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_products_id';
    protected $fillable = [
        'product_id_foreign',
        'transaction_id_foreign'
    ];
}
