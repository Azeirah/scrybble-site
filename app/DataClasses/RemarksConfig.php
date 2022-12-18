<?php

namespace App\DataClasses;

use JsonSerializable;

/**
 * @property $modified_only Skips pages without scribbles or annotations
 */
class RemarksConfig implements JsonSerializable {

    public function __construct(public bool $modified_only = false) {}

    public function jsonSerialize() {
        return [
            'modified_only' => $this->modified_only
        ];
    }

    public function toRemarksParams() {
        $params = collect();
        if ($this->modified_only) {
            $params->add("--modified_pdf");
        }
        return $params->implode(' ');
    }
}
