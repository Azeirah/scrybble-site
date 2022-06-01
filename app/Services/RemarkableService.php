<?php

namespace App\Services;

use Eloquent\Pathogen\RelativePathInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use JsonException;
use Str;

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

        return json_decode($userStorage->get($metadata_filename), JSON_OBJECT_AS_ARRAY, 512, JSON_THROW_ON_ERROR);
    }

}
