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
        Schema::create('fines_history', function (Blueprint $table) {
            $table->id();
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
        Schema::dropIfExists('fines_history');
    }
    
};
Schema::create('fines_history', function (Blueprint $table) {
    $table->id();
    $table->string('type'); // e.g., 'Late Fine (Member)'
    $table->integer('amount'); // Stored as whole number (no decimals)
    $table->unsignedBigInteger('updated_by')->nullable(); // User ID
    $table->timestamp('changed_at');
    $table->timestamps();
});

