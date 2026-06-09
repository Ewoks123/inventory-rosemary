<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE produksis MODIFY id_material BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE produksis MODIFY material_digunakan INT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE produksis MODIFY id_material BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE produksis MODIFY material_digunakan INT NOT NULL');
    }
};
