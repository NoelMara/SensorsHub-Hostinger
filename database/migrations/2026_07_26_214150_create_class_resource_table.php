<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_resource', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('resource_type'); // sensor, project, video
            $table->unsignedBigInteger('resource_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_resource');
    }
};
