<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
        {
            Schema::create('offices', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('latitude');
                $table->string('longitude');
                $table->integer('radius');
                $table->timestamps();
            });
        }

    public function down()
    {
        Schema::dropIfExists('offices');
    }
};