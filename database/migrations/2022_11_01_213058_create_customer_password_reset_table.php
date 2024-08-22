<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateCustomerPasswordResetTable
 */
final class CreateCustomerPasswordResetTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_password_reset', static function (Blueprint $table): void {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id', 'customer_password_reset_fk_customer_id')
                ->references('id')
                ->on('customer')
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
        Schema::dropIfExists('customer_password_reset');
    }
}
