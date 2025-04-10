<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $defaultSettings = [
            [
                'key' => 'site_name',
                'value' => 'My Website'
            ],
            [
                'key' => 'site_description',
                'value' => 'Welcome to my website'
            ],
            [
                'key' => 'site_logo',
                'value' => null
            ],
            [
                'key' => 'site_favicon',
                'value' => null
            ],
            [
                'key' => 'primary_color',
                'value' => '#696cff'
            ],
            [
                'key' => 'secondary_color',
                'value' => '#8592a3'
            ],
            [
                'key' => 'footer_text',
                'value' => ' ' . date('Y') . ' All rights reserved.'
            ],
            [
                'key' => 'google_analytics_id',
                'value' => null
            ],
            [
                'key' => 'meta_keywords',
                'value' => null
            ],
            [
                'key' => 'meta_description',
                'value' => null
            ],
            [
                'key' => 'social_facebook',
                'value' => null
            ],
            [
                'key' => 'social_twitter',
                'value' => null
            ],
            [
                'key' => 'social_instagram',
                'value' => null
            ],
            [
                'key' => 'social_linkedin',
                'value' => null
            ],
            [
                'key' => 'contact_email',
                'value' => null
            ],
            [
                'key' => 'contact_phone',
                'value' => null
            ],
            [
                'key' => 'contact_address',
                'value' => null
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0'
            ],
            [
                'key' => 'enable_registration',
                'value' => '1'
            ],
            [
                'key' => 'enable_notifications',
                'value' => '1'
            ],
            [
                'key' => 'timezone',
                'value' => 'UTC'
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d'
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i'
            ],
            [
              'key' => 'site_language',
              'value' => 'ar'
          ]
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
