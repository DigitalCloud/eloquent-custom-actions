<?php

namespace DigitalCloud\Eventable;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use BadMethodCallException;

trait Eventable {

    public function __call($method, $parameters) {
        $name = "action" . ucwords($method);
//        dump($method);
        if(method_exists ($this, $name)) {
//            dump("before" . ucwords($method));
//            $this->addObservableEvents(["before" . ucwords($method), "after" . ucwords($method)]);
//            dump($this->getObservableEvents());
//            dump("before" . ucwords($method));
            $this->fireModelEvent("before" . ucwords($method));
//            $this->fireActionEvent("before" . ucwords($method));
            $this->$name(...$parameters);
//            $this->fireActionEvent("after" . ucwords($method));
            $this->fireModelEvent("after" . ucwords($method));
            return;
        }
        return parent::__call($method, $parameters);
    }

    public function fireActionEvent($event, $halt = true) {
        $method = $halt ? 'until' : 'dispatch';
        return  static::$dispatcher->{$method}(
            "{$event}", $this
        );
    }

    public static function bootEventable() {
//        dump('bootEventable');
    }
}
