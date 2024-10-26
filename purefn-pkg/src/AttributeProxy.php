<?php

namespace Laura\PureFn;

use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionAttribute;

class AttributeProxy
{
    private object $target;
    private array $methodCache = [];

    public function __construct(object $target)
    {
        $this->target = $target;
        Log::info("Created AttributeProxy for " . get_class($target));
    }

    public function __call(string $method, array $arguments)
    {
        return $this->target->$method(...$arguments);
        // Check if we've already processed this method
        if (!isset($this->methodCache[$method])) {
            $reflection = new ReflectionClass($this->target);

            if (!$reflection->hasMethod($method)) {
                return $this->target->$method(...$arguments);
            }

            $methodReflection = $reflection->getMethod($method);
            $attributes = $methodReflection->getAttributes(PureFn::class, ReflectionAttribute::IS_INSTANCEOF);

            if (empty($attributes)) {
                // No PureFn attribute, store that fact and just return direct method
                $this->methodCache[$method] = fn($args) => $this->target->$method(...$args);
            } else {
                // Get the PureFn attribute instance
                $pureFn = $attributes[0]->newInstance();

                // Create wrapped version of the method
                $this->methodCache[$method] = function($args) use ($method, $pureFn) {
                    return $pureFn->wrap(
                        fn() => $this->target->$method(...$args),
                        $args
                    );
                };
            }
        }

        // Execute the cached version (either wrapped or direct)
        return ($this->methodCache[$method])($arguments);
    }

    public static function wrap(object $target): self
    {
        return new self($target);
    }
}
