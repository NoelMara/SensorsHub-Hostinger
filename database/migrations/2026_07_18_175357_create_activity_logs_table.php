<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_name');
            $table->string('user_role'); // student, instructor, faculty_head
            $table->string('action');    // created, deleted, changed, updated
            $table->string('type');      // user, class, sensor, project, product, video, suggestion, profile, password
            $table->string('description'); // "created a new sensor 'DHT22'"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};