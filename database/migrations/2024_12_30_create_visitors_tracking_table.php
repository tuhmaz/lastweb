<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the visitors tracking tables
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('visitors_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('user_agent');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('url')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('status_code')->nullable(); // Add this line
            $table->timestamp('last_activity');
            $table->decimal('response_time', 8, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_id')->constrained('visitors_tracking')->onDelete('cascade');
            $table->string('page_url');
            $table->timestamps();
        });

        Schema::create('database_metrics', function (Blueprint $table) {
            $table->id();
            $table->integer('active_connections');
            $table->integer('total_queries');
            $table->float('average_query_time');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('database_metrics');
        Schema::dropIfExists('page_visits');
        Schema::dropIfExists('visitors_tracking');
    }
};
