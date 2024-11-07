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
        Schema::create('detail_transaction', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code');
            $table->string('item_code');
            $table->integer('qty')->default(0);
            $table->integer('tax')->default(0);
            $table->integer('tax_amount')->default(0);
            $table->integer('disc_amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaction');
    }
};
