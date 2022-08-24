<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompletionFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('completion_files', function (Blueprint $table) {
            $table->string('id')->unique()->index()->primary();
            $table->timestamps();
            $table->string('FileName');
            $table->string('StoragePath');
            $table->foreignId('order_id')->constrained();
            $table->text('information')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('completion_files');
    }
}
