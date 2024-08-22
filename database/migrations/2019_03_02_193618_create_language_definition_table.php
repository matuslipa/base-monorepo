<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateLanguageDefinitionTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('language_definition', static function (Blueprint $table): void {
            $table->string('id', 3);
            $table->primary('id');

            $table->string('alpha2', 2)->nullable();
            $table->string('original_name', 100)->nullable();

            $table->string('date_format', 20)->nullable();
            $table->string('time_format', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_definition');
    }
}
