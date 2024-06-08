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
        Schema::create('room_bookeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->date('book_date')->nullable();

            $table->foreign('booking_id')
            ->references('id')
            ->on('book_details')
            ->onDelete('cascade');

            
            $table->foreign('room_id')
            ->references('id')
            ->on('rooms')
            ->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_bookeds');
    }
};
