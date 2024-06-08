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
        Schema::table('room_bookeds', function (Blueprint $table) {
            $table->unsignedBigInteger('roomnumber_id')->nullable();
            $table->foreign('roomnumber_id')
            ->references('id')
            ->on('room_numbers')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_bookeds', function (Blueprint $table) {
            //
        });
    }
};
