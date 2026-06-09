<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('produks', function (Blueprint $table) {
        $table->id();

        $table->string('kode_produk')->unique();
        $table->string('nama_produk');

        // kategori langsung di tabel produk
        $table->string('kategori');

        $table->integer('stok_produk')->default(0);

        $table->decimal('harga_produk', 12, 2)
              ->default(0);

        $table->string('satuan')->nullable();

        $table->date('tanggal_input')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produks');
    }
};
