<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // the host
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('skill_rank')->nullable();
            $table->string('region')->nullable();
            $table->string('timezone')->nullable();
            $table->string('availability')->nullable();
            $table->enum('comm_preference', ['mic_required', 'mic_optional', 'text_only'])->default('mic_optional');
            $table->enum('join_mode', ['auto_accept', 'manual_review'])->default('manual_review');
            $table->unsignedTinyInteger('party_size')->default(5); // total slots
            $table->unsignedTinyInteger('current_members')->default(1); // host counts as 1
            $table->enum('status', ['open', 'filled', 'closed'])->default('open');
            $table->boolean('is_priority')->default(false); // premium boost
            $table->timestamps();
        });
    }
};
