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
  Schema::create('news', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->string('slug')->unique();
    $table->longText('content');
    $table->string('meta_description')->nullable();
    $table->string('keywords')->nullable();
    $table->string('image');
    $table->string('alt')->nullable();

    // تحديد اسم قاعدة البيانات الرئيسية مع جدول users
    $table->unsignedBigInteger('author_id')->nullable();
    $table->foreign('author_id')
          ->references('id')
          ->on(config('database.connections.jo.database') . '.users') // تحديد قاعدة البيانات الرئيسية
          ->onDelete('set null');

    $table->boolean('is_active')->default(true);
    $table->boolean('is_featured')->default(false);
    $table->unsignedInteger('views')->default(0);
    $table->string('country');
    $table->timestamps();
});

}

public function down(): void
{
    Schema::dropIfExists('news');
}

};
