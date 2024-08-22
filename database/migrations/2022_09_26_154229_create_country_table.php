<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('country', static function (Blueprint $table): void {
            $table->string('id', 3);
            $table->primary('id');

            $table->string('alpha2', 2)->nullable();
            $table->string('dial_code', 20)->nullable();

            $table->string('language_definition_id', 3)->nullable();
            $table->foreign('language_definition_id', 'country_definition_fk_language_definition_id')
                ->references('id')
                ->on('language_definition')
                ->onUpdate('cascade')->onDelete('set null');

            $table->string('currency_definition_id', 3)->nullable();
            $table->foreign('currency_definition_id', 'country_definition_fk_currency_definition_id')
                ->references('id')
                ->on('currency_definition')
                ->onUpdate('cascade')->onDelete('set null');

            $table->string('long_date_format', 20)->nullable();
            $table->string('short_date_format', 20)->nullable();
            $table->string('time_format', 20)->nullable();

            $table->string('original_name', 100)->nullable();

            $table->unsignedSmallInteger('numeric_code')->nullable();
            $table->integer('order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country');
    }
};
