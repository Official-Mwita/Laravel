<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupportFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_files', function (Blueprint $table) {
            $table->string('id')->unique()->index()->primary();
            $table->timestamps();
            $table->string('FileName')->nullable();
            $table->string('StoragePath')->nullable();
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
        Schema::dropIfExists('support_files');
    }
}
