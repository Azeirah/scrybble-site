<?php

namespace App\Services\Remarks;

use App\DataClasses\RemarksConfig;
use App\Services\Remarks;
use Eloquent\Pathogen\AbsolutePath;
use Eloquent\Pathogen\AbsolutePathInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class RemarksDockerServer implements Remarks\RemarksService
{

    /**
     * @inheritDoc
     */
    public function extractNotesAndHighlights(AbsolutePathInterface $sourceDirectory, AbsolutePathInterface $targetDirectory, RemarksConfig $config): void
    {
        $efsRoot = AbsolutePath::fromString(Storage::disk('efs')->path("."));
        $res = Http::post("remarks:5000/process", [
            "in_path" => "/efs/" . $sourceDirectory->relativeTo($efsRoot)->string(),
            "out_path" => "/efs/" . $sourceDirectory->relativeTo($efsRoot)->string()
        ]);
        if ($res->status() !== 200) {
            throw new RuntimeException("Failed to convert notes and highlights.");
        }
    }
}
