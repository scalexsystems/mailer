<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Scalex\Mailer\Eloquent\Migrator;

class CreateTracksTable extends Migrator
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!$this->schema->hasTable('tracks')) {
            $this->schema->create(
                'tracks',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->unsignedInteger('project_id');
                    $table->unsignedInteger('member_id');
                    $table->string('label');
                    $table->text('data');
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
        $this->schema->drop('tracks');
    }
}
