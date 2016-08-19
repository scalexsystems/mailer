<?php namespace Scalex\Mailer\Eloquent;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    public function __construct(array $attributes = []) {
        Encapsulator::init();

        parent::__construct($attributes);
    }
}
