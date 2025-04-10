<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تشغيل الهجرة
     */
    public function up(): void
    {
        Schema::create('rate_limit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('route')->nullable();
            $table->string('method', 10);
            $table->text('user_agent')->nullable();
            $table->integer('attempts')->default(1);
            $table->integer('limit')->default(0);
            $table->timestamp('blocked_until')->nullable();
            $table->timestamps();
            
            // إنشاء فهارس لتحسين الأداء
            $table->index('ip_address');
            $table->index('route');
            $table->index('blocked_until');
        });
    }

    /**
     * التراجع عن الهجرة
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_limit_logs');
    }
};
