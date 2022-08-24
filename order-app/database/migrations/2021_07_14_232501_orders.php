<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Orders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained();
            $table->decimal('price', 5, 2);
            $table->string('academic_level');
            $table->string('subject_name')->nullable();
            $table->string('service');
            $table->string('type_of_paper');
            $table->text('description');
            $table->string('reference_style');
            $table->integer('pages');
            $table->integer('hours');
            $table->string('spacing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
