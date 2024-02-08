<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProceedingTemplateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proceeding_template_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proceeding_template_id');
            $table->unsignedTinyInteger('order_index')->nullable();
            $table->string('label');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('input_type');
            $table->boolean('is_required')->default(true);
            $table->boolean('is_disabled')->default(false);
            $table->boolean('is_read_only')->default(false);
            $table->string('max_value')->nullable();
            $table->string('min_value')->nullable();
            $table->string('def_value')->nullable();
            $table->json('option_values')->nullable();
            $table->string('ajax_source')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proceeding_template_id', 'pt_ptf_id_foreign')
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
        Schema::dropIfExists('proceeding_template_fields');
    }
}
