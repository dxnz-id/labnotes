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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event');
            $table->date('date');
            $table->json('responsible_person'); // Storing multiple responsible persons as JSON
            $table->json('participants'); // Storing multiple participants as JSON
            $table->json('speaker')->nullable(); // Storing multiple speakers as JSON
            $table->json('photo'); // Storing multiple photos as JSON
            $table->json('video')->nullable(); // Storing multiple videos as JSON
            $table->json('document')->nullable(); // Storing multiple documents as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
