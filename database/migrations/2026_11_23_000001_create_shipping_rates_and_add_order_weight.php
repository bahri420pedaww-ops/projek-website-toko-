<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tarif per kg untuk tiap layanan kurir di tiap zona tujuan (1 sampai 4)
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_method_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('zone');
            $table->unsignedInteger('price_per_kg');
            $table->string('estimated_days', 30)->nullable();
            $table->timestamps();

            $table->unique(['shipping_method_id', 'zone']);
        });

        // Pesanan menyimpan berat dan estimasi saat checkout (data historis)
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('total_weight_gram')->default(0)->after('postal_code');
            $table->string('shipping_etd', 30)->nullable()->after('shipping_cost');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['total_weight_gram', 'shipping_etd']);
        });

        Schema::dropIfExists('shipping_rates');
    }
};
