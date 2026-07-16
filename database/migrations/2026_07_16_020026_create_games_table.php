<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('igdb_id')->nullable(); // this links to the igdb api
            $table->string('cover_art')->nullable();
            $table->boolean('is_custom')->default(false);
            $table->timestamps();
        });
    }
};
