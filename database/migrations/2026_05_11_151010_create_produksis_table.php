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
    Schema::create('produksis', function (Blueprint $table) {
        $table->id();

        $table->foreignId('id_produk')
              ->constrained('produks')
              ->cascadeOnDelete();

        $table->foreignId('id_material')
              ->nullable()
              ->constrained('materials')
              ->cascadeOnDelete();

        $table->integer('jumlah_produksi');
        $table->integer('material_digunakan')->nullable();
        $table->date('tanggal_produksi')->nullable();
        $table->text('keterangan')->nullable();

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
        Schema::dropIfExists('produksis');
    }
};
