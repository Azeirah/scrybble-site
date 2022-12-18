<?php

namespace App\DataClasses;

use JsonException;
use JsonSerializable;

/**
 * @property $modified_only Skips pages without scribbles or annotations
 */
class RemarksConfig implements JsonSerializable {

    public function __construct(public bool $modified_only = false) {}

    /**
     * @throws JsonException
     */
    public function jsonSerialize() {
        return json_encode([
            'modified_only' => $this->modified_only
        ], JSON_THROW_ON_ERROR);
    }

    public function toRemarksParams() {
        $params = collect();
        if ($this->modified_only) {
            $params->add("--modified_pdf");
        }
        return $params->implode(' ');
    }
}
