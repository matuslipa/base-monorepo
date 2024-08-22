<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUserPasswordResetTable
 */
final class CreateUserPasswordResetTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_password_reset', static function (Blueprint $table): void {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'user_password_reset_fk_user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');

            $table->string('token_hash')->unique();
            $table->dateTime('expiration_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_password_reset');
    }
}
