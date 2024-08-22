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
        Schema::create('activity_log', static function (Blueprint $table): void {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id', 'activity_log_fk_user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')->onDelete('SET NULL');

            $table->string('model')->nullable();
            $table->string('model_id')->nullable();

            $table->index(['model', 'model_id'], 'activity_log_ix_model');

            $table->string('type', 40);
            $table->text('meta')->nullable();

            $table->dateTime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
