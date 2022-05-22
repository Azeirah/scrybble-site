<?php

namespace App\Services;

use App\DataClasses\RemarksConfig;
use Eloquent\Pathogen\AbsolutePathInterface;
use Eloquent\Pathogen\RelativePathInterface;

class RemarksService {

    public function extractNotesAndHighlights(AbsolutePathInterface $sourceDirectory, AbsolutePathInterface
    $targetDirectory, RemarksConfig $config) {
        // docker run -v "$PWD/files":/store laauurraaa/remarks-bin /store /store --targets md
        $srcDir = $sourceDirectory->string();
        $targetDir = $targetDirectory->string();
        $strCommand = "docker run -v \"$srcDir/\":/in -v \"$targetDir\":/out laauurraaa/remarks-bin /in /out --targets md";
//        dd($strCommand);
        exec($strCommand);
    }

}
