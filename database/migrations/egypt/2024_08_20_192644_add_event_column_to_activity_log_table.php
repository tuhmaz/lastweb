<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventColumnToActivityLogTable extends Migration
{
    public function up()
    {
        Schema::connection('sa')->table('activity_log', function (Blueprint $table) {
            $table->string('event')->nullable()->after('log_name');
        });
    }

    public function down()
    {
        Schema::connection('sa')->table('activity_log', function (Blueprint $table) {
            $table->dropColumn('event');
        });
    }
}
