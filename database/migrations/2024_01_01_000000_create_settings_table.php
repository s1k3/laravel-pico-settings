<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public string $after = 'CreateUsersTable';

    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('model')->nullable()->index();
            $table->string('key')->index();
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on(config('pico-settings.user_table', 'users'))->cascadeOnDelete();

            $table->unique(['user_id', 'model', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
