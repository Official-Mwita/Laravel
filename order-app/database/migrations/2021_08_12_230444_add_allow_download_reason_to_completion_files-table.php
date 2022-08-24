<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowDownloadReasonToCompletionFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('completion_files', function (Blueprint $table) {
            $table->boolean('allow_download')->default(false);
            $table->text('reason_denied');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('completion_files', function (Blueprint $table) {
            $table->dropColumn('allow_download');
            $table->dropColumn('reason');
        });
    }
}
