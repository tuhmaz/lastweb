<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('event_type'); 
            $table->text('description')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('route')->nullable();
            $table->string('method')->nullable();
            $table->json('request_data')->nullable();
            $table->unsignedInteger('risk_score')->default(0);
            $table->string('country_code')->nullable();
            $table->string('city')->nullable();
            $table->string('attack_type')->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->boolean('is_trusted')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->string('severity')->default('info'); // info, warning, danger, critical
            $table->unsignedInteger('occurrence_count')->default(1);
            $table->timestamps();

            $table->index('ip_address');
            $table->index('event_type');
            $table->index('is_blocked');
            $table->index('is_trusted');
            $table->index('is_resolved');
            $table->index('severity');
            $table->index('risk_score');
            $table->index('occurrence_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
