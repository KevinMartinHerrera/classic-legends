<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table): void {
            if (! Schema::hasColumn('categorias', 'nombre_original')) {
                $table->string('nombre_original')->nullable()->after('titulo');
            }

            if (! Schema::hasColumn('categorias', 'nombre_es')) {
                $table->string('nombre_es')->nullable()->after('nombre_original');
            }
        });

        Schema::table('productos', function (Blueprint $table): void {
            if (! Schema::hasColumn('productos', 'nombre_original')) {
                $table->string('nombre_original')->nullable()->after('titulo');
            }

            if (! Schema::hasColumn('productos', 'nombre_es')) {
                $table->string('nombre_es')->nullable()->after('nombre_original');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table): void {
            if (Schema::hasColumn('productos', 'nombre_es')) {
                $table->dropColumn('nombre_es');
            }

            if (Schema::hasColumn('productos', 'nombre_original')) {
                $table->dropColumn('nombre_original');
            }
        });

        Schema::table('categorias', function (Blueprint $table): void {
            if (Schema::hasColumn('categorias', 'nombre_es')) {
                $table->dropColumn('nombre_es');
            }

            if (Schema::hasColumn('categorias', 'nombre_original')) {
                $table->dropColumn('nombre_original');
            }
        });
    }
};
