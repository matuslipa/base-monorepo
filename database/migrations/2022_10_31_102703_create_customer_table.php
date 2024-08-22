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
        Schema::create('customer', static function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 70)->nullable();
            $table->string('last_name', 70)->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('last_logged_at')->nullable();
            $table->string('token', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('customer_session', static function (Blueprint $table): void {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id', 'customer_token_fk_customer_id')
                ->references('id')
                ->on('customer')
                ->onUpdate('cascade')->onDelete('SET NULL');

            $table->ipAddress('client_ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });

        Schema::create('customer_token', static function (Blueprint $table): void {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('customer_session_id')->nullable();
            $table->foreign('customer_session_id', 'customer_token_fk_customer_session_id')
                ->references('id')
                ->on('customer_session')
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
        Schema::dropIfExists('customer_token');
        Schema::dropIfExists('customer_session');
        Schema::dropIfExists('customer');
    }
};
