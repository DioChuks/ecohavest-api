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
        Schema::create('kycs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('id_card_front')->nullable();
            $table->string('id_card_back')->nullable();
            $table->string('photo')->nullable();
            $table->string('proof_of_address')->nullable();
            $table->string('tax_id_number')->nullable();
            $table->string('ssn')->nullable();
            $table->string('dob')->nullable();
            $table->string('business_license')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kycs');
    }
};
