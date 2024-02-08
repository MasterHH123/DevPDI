<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProceedingRecordAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proceeding_record_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proceeding_record_id');
            $table->string('name');
            $table->string('file_ext');
            $table->string('path');
            $table->timestamps();

            $table->foreign('proceeding_record_id', 'pr_prat_id_foreign')
            ->references('id')
            ->on('proceeding_records')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proceeding_record_attachments');
    }
}
