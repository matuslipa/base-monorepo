<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateRoleTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('type');
            $table->boolean('is_active');
            $table->boolean('is_protected');
            $table->timestamps();
        });

        Schema::create('role_permission', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')
                ->references('id')->on('role')
                ->onDelete('cascade');
            $table->string('module_id', 50);
            $table->foreign('module_id')
                ->references('id')->on('module')
                ->onDelete('cascade');
            $table->string('permission');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('user_role', static function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('user')
                ->onDelete('cascade');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')
                ->references('id')->on('role')
                ->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('role');
    }
}
