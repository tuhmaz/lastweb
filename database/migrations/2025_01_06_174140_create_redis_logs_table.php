<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedisLogsTable extends Migration
{
    public function up()
    {
        Schema::create('redis_logs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index(); // اسم المفتاح
            $table->text('value')->nullable(); // القيمة
            $table->integer('ttl')->nullable(); // الوقت المتبقي (TTL)
            $table->string('action')->nullable(); // نوع العملية (إضافة/تحديث/حذف)
            $table->string('user')->nullable(); // المستخدم الذي قام بالعملية
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('redis_logs');
    }
}
