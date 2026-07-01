<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('yupoo_album_id')->unique();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->string('portada_url')->nullable();
            $table->string('yupoo_url');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
