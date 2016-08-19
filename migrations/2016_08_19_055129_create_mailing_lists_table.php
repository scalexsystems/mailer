<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Scalex\Mailer\Eloquent\Migrator;

class CreateMailingListsTable extends Migrator
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!$this->schema->hasTable('mailing_lists')) {
            $this->schema->create(
                'mailing_lists',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name');
                    $table->string('description')->nullable();
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
        $this->schema->drop('mailing_lists');
    }
}
