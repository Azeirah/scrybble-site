<?php

namespace Laura\PureFn;

use Attribute;
use Closure;
use Illuminate\Support\Facades\Storage;
use ReflectionFunction;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
class PureFn
{
    public function wrap(Closure $original, array $params = []): mixed
    {
        // Only record in non-production environments
        if (!app()->environment('local', 'testing')) {
            return $original(...$params);
        }

        // Execute the function
        $result = $original(...$params);

        // Store the test case
        $this->record([
            'function' => $this->getFunctionIdentifier($original),
            'input' => $this->serialize($params),
            'output' => $this->serialize($result),
            'timestamp' => time()
        ]);

        return $result;
    }

    private function getFunctionIdentifier(Closure $closure): string
    {
        $reflection = new ReflectionFunction($closure);
        return sprintf(
            '%s::%s',
            $reflection->getClosureScopeClass()->getName(),
            $reflection->getName()
        );
    }

    private function serialize($data): string
    {
        return serialize($data);
    }

    private function record(array $data): void
    {
        $storage = Storage::disk('local');
        $path = 'purefn/' . md5($data['function']) . '.log';

        // Append to file, one JSON object per line for easy streaming
        $storage->append(
            $path,
            json_encode($data)
        );
    }
}
