<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_imagenes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('url', 2048);
            $table->unsignedInteger('orden')->default(0);
            $table->string('alt')->nullable();
            $table->timestamps();

            $table->index(['producto_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_imagenes');
    }
};
