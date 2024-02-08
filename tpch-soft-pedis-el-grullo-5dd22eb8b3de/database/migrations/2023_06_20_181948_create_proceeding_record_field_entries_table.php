<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProceedingRecordFieldEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proceeding_record_field_entries', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('proceeding_record_id');
            $table->unsignedBigInteger('proceeding_template_field_id');
            $table->string('field_value')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('proceeding_record_id', 'pr_prfe_id_foreign')
            ->references('id')
            ->on('proceeding_records');

            $table->foreign('proceeding_template_field_id', 'ptf_prfe_id_foreign')
            ->references('id')
            ->on('proceeding_template_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proceeding_record_field_entries');
    }
}
