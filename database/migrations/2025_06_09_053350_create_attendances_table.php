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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // no foreign key constraint
            $table->unsignedBigInteger('event_id');   // no foreign key constraint
            $table->date('date');
            $table->time('time_in1')->nullable();
            $table->time('time_out1')->nullable();
            $table->time('time_in2')->nullable();   // for whole-day
            $table->time('time_out2')->nullable();  // for whole-day
            $table->string('status')->nullable();   // On Time, Late
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
