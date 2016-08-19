<?php namespace Scalex\Mailer\Eloquent;


class Migrator
{

    /**
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $capsule;

    /**
     * Migrator constructor.
     */
    public function __construct() {
        Encapsulator::init();

        $this->capsule = Encapsulator::$capsule;
        $this->schema = Encapsulator::$capsule->schema();
    }
}
