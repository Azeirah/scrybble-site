<?php
declare(strict_types=1);

namespace App\Services;

use Eloquent\Pathogen\RelativePathInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use JsonException;
use Str;

/**
 * Functionality directly related to the ReMarkable itself
 * Primarily deals with extracting information out of rm notebook metadata
 */
class RemarkableService {
    /**
     * The name of the Remarkable file
     * @param Filesystem $userStorage
     * @param RelativePathInterface $to
     * @return string
     * @throws JsonException
     */
    public function filename(Filesystem $userStorage, RelativePathInterface $to): string {
        return $this->metadata($userStorage, $to)['visibleName'];
    }

    /**
     * @param Filesystem $userStorage
     * @param RelativePathInterface $to
     * @return mixed
     * @throws JsonException
     */
    public function metadata(Filesystem $userStorage, RelativePathInterface $to): array {
        $metadata_filename = collect($userStorage->files($to))->first(static function (string $filename) {
            return Str::contains($filename, '.metadata');
        });

        return json_decode($userStorage->get($metadata_filename), true, 512, JSON_THROW_ON_ERROR);
    }
}
