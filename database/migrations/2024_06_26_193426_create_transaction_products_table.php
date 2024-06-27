<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_products', function (Blueprint $table) {
            $table->increments('transaction_products_id');
            $table->integer('product_id_foreign')->unsigned();
            $table->integer('transaction_id_foreign')->unsigned();
            $table->foreign('product_id_foreign')->references('product_id')->on('products');
            $table->foreign('transaction_id_foreign')->references('transaction_id')->on('transactions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_products');
    }
};
