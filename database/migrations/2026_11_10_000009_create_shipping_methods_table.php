<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('courier', 50);
            $table->string('service', 50);
            $table->unsignedBigInteger('cost');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['courier', 'service']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
