<?php

namespace App\DataClasses;

use JsonSerializable;

/**
 * @property $modified_only Skips pages without scribbles or annotations
 */
class RemarksConfig implements JsonSerializable {

    public function __construct(private bool $modified_only = false, private bool $export_highlights_to_md = false) {}

    public function jsonSerialize() {
        return [
            'modified_only' => $this->modified_only
        ];
    }

    public function toRemarksParams(): string
    {
        $params = collect();
        if ($this->modified_only) {
            $params->add("--modified_pdf");
        }
        if (!$this->export_highlights_to_md) {
            $params->add("--skip_combined_md");
        }
        return $params->implode(' ');
    }
}
