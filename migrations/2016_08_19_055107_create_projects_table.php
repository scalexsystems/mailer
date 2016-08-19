<?php

use Illuminate\Database\Schema\Blueprint;
use Scalex\Mailer\Eloquent\Migrator;

class CreateProjectsTable extends Migrator
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!$this->schema->hasTable('projects')) {
            $this->schema->create(
                'projects',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name');
                    $table->string('directory');
                    $table->softDeletes();
                    $table->timestamps();
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $this->schema->drop('projects');
    }
}
