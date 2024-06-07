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
        Schema::create('room_numbers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('roomtype_id');

            $table->unsignedBigInteger('room_id');

            $table->string('status');

            $table->string('room_no');

            $table->foreign('room_id')
            ->references('id')
            ->on('rooms')
            ->onDelete('cascade');

            $table->foreign('roomtype_id')
            ->references('id')
            ->on('room_types')
            ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_numbers');
    }
};
