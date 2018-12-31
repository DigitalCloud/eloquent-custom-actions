<?php

namespace DigitalCloud\Eventable;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use BadMethodCallException;

trait Eventable {

    public function __call($method, $parameters) {
        $name = "action" . ucwords($method);
        if(method_exists ($this, $name)) {
            $this->fireModelEvent("before" . ucwords($method));
            $this->$name(...$parameters);
            $this->fireModelEvent("after" . ucwords($method));
            return;
        }
        return parent::__call($method, $parameters);
    }
}
