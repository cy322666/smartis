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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->timestamp('datetime');
            $table->timestamp('date');
            $table->integer('person_id');
            $table->integer('smartis_id');
            $table->integer('lead_id')->unique();
            $table->string('first_click')->nullable();
            $table->string('last_click')->nullable();
            $table->boolean('send')->default(false);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
