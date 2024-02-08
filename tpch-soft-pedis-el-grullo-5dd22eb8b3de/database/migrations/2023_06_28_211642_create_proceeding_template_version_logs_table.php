<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProceedingTemplateVersionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proceeding_template_version_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proceeding_template_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedTinyInteger('tag_version');
            $table->json('fields');
            $table->timestamps();

            $table->foreign('proceeding_template_id', 'pt_ptfvl_id_foreign')
            ->references('id')
            ->on('proceeding_templates')
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
        Schema::dropIfExists('proceeding_template_version_logs');
    }
}
