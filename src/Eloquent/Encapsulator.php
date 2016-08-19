<?php namespace Scalex\Mailer\Eloquent;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;


class Encapsulator
{
    /**
     * @var Capsule
     */
    public static $capsule;

    private function __construct() {}
    /**
     * Initialize capsule and store reference to connection
     */
    static public function init()
    {
        if (is_null(self::$capsule)) {
            self::$capsule = new Capsule;
            self::$capsule->addConnection([
                'driver'   => 'sqlite',
                'database' => realpath(__DIR__ . '/../../storage/database.sqlite'),
                'prefix'   => '',
            ]);
            self::$capsule->setEventDispatcher(new Dispatcher(new Container));
            // Set the cache manager instance used by connections... (optional)
            // $capsule->setCacheManager(...);
            // Make this Capsule instance available globally via static methods... (optional)
            self::$capsule->setAsGlobal();
            // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
            self::$capsule->bootEloquent();
        }
    }
}
