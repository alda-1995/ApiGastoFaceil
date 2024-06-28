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
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('transaction_id');
            $table->decimal('amount', 10, 2);
            $table->mediumText('description')->nullable();
            $table->date("currentDate");
            $table->boolean('income');
            $table->timestamps();

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
