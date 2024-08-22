<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public const TABLE_NAME = 'user';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(self::TABLE_NAME, static function (Blueprint $table): void {
            $table->boolean('two_factor_auth_enabled')
                ->default(false);
            $table->timestamp('two_factor_auth_timestamp')
                ->nullable();
            $table->string('two_factor_auth_secret')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(self::TABLE_NAME, static function (Blueprint $table): void {
            $table->dropColumn('two_factor_auth_enabled');
            $table->dropColumn('two_factor_auth_timestamp');
            $table->dropColumn('two_factor_auth_secret');
        });
    }
};
