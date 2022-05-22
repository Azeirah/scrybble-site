<?php

namespace App\Services;

use App\DataClasses\RemarksConfig;
use Eloquent\Pathogen\AbsolutePathInterface;
use RuntimeException;

class RemarksService {

    public function extractNotesAndHighlights(AbsolutePathInterface $sourceDirectory, AbsolutePathInterface
    $targetDirectory, RemarksConfig $config): void {
        // docker run -v "$PWD/files":/store laauurraaa/remarks-bin /store /store --targets md
        $srcDir = $sourceDirectory->string();
        $targetDir = $targetDirectory->string();
        $strCommand = "docker run -v \"$srcDir/\":/in -v \"$targetDir\":/out laauurraaa/remarks-bin /in /out --modified_pdf";
        exec($strCommand, $output, $result_code);

        if ($result_code !== 0) {
            throw new RuntimeException("remarks-bin docker failed with error_code: `$result_code`: `" . implode
                ("\n", $output) . "`");
        }
    }

}
