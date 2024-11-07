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
        Schema::create('header_transaction', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->timestamp('date');
            $table->integer('total_amount')->default(0);
            $table->integer('total_tax')->default(0);
            $table->integer('total_disc')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_transaction');
    }
};
