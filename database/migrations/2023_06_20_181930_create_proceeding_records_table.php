<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProceedingRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proceeding_records', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proceeding_id');
            $table->unsignedBigInteger('proceeding_template_id');
            $table->unsignedTinyInteger('proceeding_template_tag_version');
            $table->unsignedBigInteger('citizen_id');
            $table->unsignedBigInteger('user_id');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proceeding_id', 'p_pr_id_foreign')
            ->references('id')
            ->on('proceedings')
            ->onDelete('cascade');
            
            $table->foreign('proceeding_template_id', 'pt_pr_id_foreign')
            ->references('id')
            ->on('proceeding_templates')
            ->onDelete('cascade');

            $table->foreign('citizen_id', 'c_pr_id_foreign')
            ->references('id')
            ->on('citizens');

            $table->foreign('user_id', 'u_pr_id_foreign')
            ->references('id')
            ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proceeding_records');
    }
}
