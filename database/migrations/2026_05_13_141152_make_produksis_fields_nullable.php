<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menggunakan raw SQL PostgreSQL (kompatibel tanpa doctrine/dbal)
     */
    public function up()
    {
        DB::statement('ALTER TABLE produksis ALTER COLUMN id_material DROP NOT NULL');
        DB::statement('ALTER TABLE produksis ALTER COLUMN material_digunakan DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::statement('ALTER TABLE produksis ALTER COLUMN id_material SET NOT NULL');
        DB::statement('ALTER TABLE produksis ALTER COLUMN material_digunakan SET NOT NULL');
    }
};
