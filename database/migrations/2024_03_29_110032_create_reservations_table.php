<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('annoucement_id');
                $table->unsignedBigInteger('benevole_id');
                // $table->text('required_skills');
                $table->text('message')->nullable();
                $table->enum('status', ['waiting', 'accepted', 'rejected'])->default('waiting');
                $table->timestamps();
                $table->foreign('annoucement_id')->references('id')->on('annoucements')->onDelete('cascade');
                $table->foreign('benevole_id')->references('id')->on('users')->onDelete('cascade');
    
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};