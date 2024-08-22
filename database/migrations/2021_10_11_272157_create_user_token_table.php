<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateUserTokenTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_token', static function (Blueprint $table): void {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_session_id')->nullable();
            $table->foreign('user_session_id', 'user_token_fk_user_session_id')
                ->references('id')
                ->on('user_session')
                ->onDelete('cascade');

            $table->boolean('is_refresh');

            $table->boolean('is_invalidated');

            $table->dateTime('expiration_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_token');
    }
}
