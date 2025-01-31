<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // Titre de l'événement
        $table->text('description'); // Description de l'événement
        $table->string('image')->nullable(); // URL ou chemin de l'image
        $table->string('location'); // Lieu de l'événement
        $table->dateTime('date'); // Date de l'événement
        $table->unsignedInteger('max_participants'); // Nombre max de participants
        $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade'); // Organisateur de l'événement
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
