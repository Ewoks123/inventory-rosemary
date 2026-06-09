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
    Schema::create('materials', function (Blueprint $table) {
        $table->id();

        $table->string('kode_material')->unique();
        $table->string('nama_material');
        $table->string('jenis_material')->nullable();
        $table->integer('stok_material')->default(0);
        $table->string('satuan')->nullable();
        $table->string('supplier')->nullable();
        $table->date('tanggal_masuk')->nullable();

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
        Schema::dropIfExists('materials');
    }
};
