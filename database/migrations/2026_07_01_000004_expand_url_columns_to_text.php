<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE producto_imagenes ADD INDEX producto_imagenes_producto_id_index (producto_id)');
        DB::statement('ALTER TABLE producto_imagenes DROP INDEX producto_imagenes_producto_id_url_unique');
        DB::statement('ALTER TABLE producto_imagenes MODIFY url VARCHAR(2048) NOT NULL');
        DB::statement('ALTER TABLE productos MODIFY portada_url VARCHAR(2048) NULL');
        DB::statement('ALTER TABLE productos MODIFY yupoo_url VARCHAR(2048) NOT NULL');
        DB::statement('ALTER TABLE categorias MODIFY yupoo_url VARCHAR(2048) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE producto_imagenes DROP INDEX producto_imagenes_producto_id_index');
        DB::statement('ALTER TABLE producto_imagenes MODIFY url VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE productos MODIFY portada_url VARCHAR(255) NULL');
        DB::statement('ALTER TABLE productos MODIFY yupoo_url VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE categorias MODIFY yupoo_url VARCHAR(255) NULL');
        DB::statement('ALTER TABLE producto_imagenes ADD UNIQUE KEY producto_imagenes_producto_id_url_unique (producto_id, url(255))');
    }
};
