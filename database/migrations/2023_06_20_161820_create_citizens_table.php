<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitizensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('citizens', function (Blueprint $table) {
            $table->id();

            // Identification
            $table->string('first_name');
            $table->string('last_name');
            $table->string('second_last_name');
            $table->string('avatar')->nullable();
            $table->enum('gender', ['M', 'F', 'O']);
            $table->string('phone')->unique()->nullable();
            $table->string('address_street');
            $table->string('address_zipcode')->nullable();
            $table->string('address_lat', 32)->nullable();
            $table->string('address_lng', 32)->nullable();
            $table->date('birth_date');
            $table->smallInteger('age');
            $table->string('curp')->unique()->nullable();
            $table->string('rfc')->unique()->nullable();

            // Tracking
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('citizens');
    }
}
