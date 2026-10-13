<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suggestion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->boolean('flagged')->default(false);
            $table->string('flag_reason')->nullable();
            $table->timestamps();

            // Each user can only comment once per suggestion
            $table->unique(['suggestion_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};