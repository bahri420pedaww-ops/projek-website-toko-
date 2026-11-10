<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 30)->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('shipping_method_id')->constrained()->restrictOnDelete();

            // Salinan alamat saat checkout (data historis)
            $table->string('receiver_name');
            $table->string('receiver_phone', 20);
            $table->text('shipping_address');
            $table->string('city_name');
            $table->string('province_name');
            $table->string('postal_code', 10)->nullable();

            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('shipping_cost');
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->unsignedBigInteger('grand_total');

            $table->enum('status', [
                'pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled',
            ])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
