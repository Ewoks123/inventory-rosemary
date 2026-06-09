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
        Schema::table('produksis', function (Blueprint $table) {
            $table->dropForeign(['id_material']);
            $table->dropColumn(['id_material', 'material_digunakan', 'keterangan']);
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('kode_produk');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['kode_material', 'tanggal_masuk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksis', function (Blueprint $table) {
            $table->unsignedBigInteger('id_material')->nullable();
            $table->foreign('id_material')->references('id')->on('materials');
            $table->string('material_digunakan')->nullable();
            $table->string('keterangan')->nullable();
        });

        Schema::table('produks', function (Blueprint $table) {
            $table->string('kode_produk')->nullable();
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->string('kode_material')->nullable();
            $table->date('tanggal_masuk')->nullable();
        });
    }
};
