<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Scalex\Mailer\Eloquent\Migrator;

class CreateMailingMembersTable extends Migrator
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!$this->schema->hasTable('members')) {
            $this->schema->create(
                'members',
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('first_name');
                    $table->string('last_name');
                    $table->string('email');
                    $table->unsignedInteger('mailing_list_id');
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
        $this->schema->drop('members');
    }
}
