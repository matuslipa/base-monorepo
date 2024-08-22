<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateImageTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('image', static function (Blueprint $table): void {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('extension')->nullable();
            $table->string('disk');
            $table->string('filename');

            $table->string('mime_type');
            $table->unsignedInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            $table->string('model')->nullable();
            $table->string('model_id', 40)->nullable();

            $table->string('status', 10);
            $table->string('access_type', 10);

            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->foreign('created_by_user_id', 'image_fk_created_by_user_id')
                ->references('id')
                ->on('user')
                ->onUpdate('cascade')->onDelete('SET NULL');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image');
    }
}
