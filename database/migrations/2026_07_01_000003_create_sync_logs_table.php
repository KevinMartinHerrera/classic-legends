<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->string('status')->default('running');
            $table->unsignedTinyInteger('pages_requested')->nullable();
            $table->unsignedTinyInteger('pages_scanned')->default(0);
            $table->unsignedInteger('albums_found')->default(0);
            $table->unsignedInteger('products_created')->default(0);
            $table->unsignedInteger('products_updated')->default(0);
            $table->unsignedInteger('images_synced')->default(0);
            $table->unsignedInteger('errors_count')->default(0);
            $table->text('summary')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
