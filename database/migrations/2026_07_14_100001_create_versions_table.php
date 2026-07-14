<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versions', static function (Blueprint $table): void {
            $table->id();
            $table->morphs('versionable');
            $table->unsignedInteger('version');
            $table->json('old_data')->nullable();
            $table->json('new_data');
            $table->json('changes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['versionable_type', 'versionable_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versions');
    }
};
