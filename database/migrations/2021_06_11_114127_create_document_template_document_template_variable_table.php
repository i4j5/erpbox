<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTemplateDocumentTemplateVariableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_template_document_template_variable', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_template_variable_id');
            $table->unsignedBigInteger('document_template_id');
            // $table->foreign('document_template_variable_id')->references('id')->on('document_template_variables');
            // $table->foreign('document_template_id')->references('id')->on('document_templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_template_document_template_variable');
    }
}
