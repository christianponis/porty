<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'porty_auth';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::connection('porty_auth')->hasTable('users')) {
            Schema::connection('porty_auth')->create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('phone', 20)->nullable();
                $table->string('role', 20)->default('guest');
                $table->string('avatar')->nullable();
                $table->boolean('is_active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (! Schema::connection('porty_auth')->hasTable('password_reset_tokens')) {
            Schema::connection('porty_auth')->create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::connection('porty_auth')->hasTable('sessions')) {
            Schema::connection('porty_auth')->create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('porty_auth')->dropIfExists('sessions');
        Schema::connection('porty_auth')->dropIfExists('password_reset_tokens');
        Schema::connection('porty_auth')->dropIfExists('users');
    }
};
