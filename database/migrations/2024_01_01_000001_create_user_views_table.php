<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advanced_table_user_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('resource');            // Fully-qualified resource class
            $table->string('name');
            $table->json('state')->nullable();     // Filters, sort, columns, search
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_global_favorite')->default(false);
            $table->boolean('is_public')->default(false);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_approved')->default(true);
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['resource', 'user_id']);
            $table->index(['resource', 'is_global_favorite']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advanced_table_user_views');
    }
};
