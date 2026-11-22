<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kolom alamat berbentuk teks bebas + titik peta
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('province', 100)->nullable()->after('receiver_phone');
            $table->string('city', 100)->nullable()->after('province');
            $table->string('district', 100)->nullable()->after('city');
            $table->string('postal_code', 10)->nullable()->after('district');
            $table->decimal('latitude', 10, 7)->nullable()->after('street_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        // 2. Pindahkan data lama (kota dan provinsi dari relasi) ke kolom teks
        DB::statement('
            UPDATE addresses a
            JOIN cities c ON c.id = a.city_id
            JOIN provinces p ON p.id = c.province_id
            SET a.city = c.name, a.province = p.name, a.postal_code = c.postal_code
        ');

        // 3. Hapus relasi ke tabel cities
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('receiver_phone')
                ->constrained()->nullOnDelete();
            $table->dropColumn(['province', 'city', 'district', 'postal_code', 'latitude', 'longitude']);
        });
    }
};
