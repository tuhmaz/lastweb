<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('onesignal_app_id')->nullable();
            $table->string('onesignal_rest_api_key')->nullable();
            $table->string('onesignal_user_auth_key')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'onesignal_app_id',
                'onesignal_rest_api_key',
                'onesignal_user_auth_key'
            ]);
        });
    }
};
