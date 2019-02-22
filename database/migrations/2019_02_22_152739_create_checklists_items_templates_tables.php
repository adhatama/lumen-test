<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChecklistsItemsTemplatesTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('object_domain');
            $table->string('object_id');
            $table->string('description');
            $table->boolean('is_completed');
            $table->datetime('completed_at')->nullable();
            $table->datetime('due')->nullable();
            $table->smallInteger('urgency')->nullable();
            $table->string('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('object_domain');
            $table->string('object_id');
            $table->string('description');
            $table->boolean('is_completed');
            $table->datetime('completed_at')->nullable();
            $table->datetime('due')->nullable();
            $table->smallInteger('urgency')->nullable();
            $table->string('updated_by')->nullable();

            $table->unsignedInteger('checklist_id');
            $table->foreign('checklist_id')->references('id')->on('checklists');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->integer('due_interval')->nullable();
            $table->string('due_unit')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('template_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->smallInteger('urgency')->nullable();
            $table->integer('due_interval')->nullable();
            $table->string('due_unit')->nullable();

            $table->unsignedInteger('template_id');
            $table->foreign('template_id')->references('id')->on('templates');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('checklists');

        Schema::dropIfExists('template_items');
        Schema::dropIfExists('templates');
    }
}
