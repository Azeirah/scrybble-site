<?php
declare(strict_types=1);

namespace App\Services\Remarks;

use App\DataClasses\RemarksConfig;
use Eloquent\Pathogen\AbsolutePathInterface;
use RuntimeException;

/**
 *
 */
class RemarksRunDockerContainer implements RemarksService
{
    private string $remarks_version = "0.3.16";

    /**
     * @param AbsolutePathInterface $sourceDirectory
     * @param AbsolutePathInterface $targetDirectory
     * @param RemarksConfig $config
     * @return void
     */
    public function extractNotesAndHighlights(AbsolutePathInterface $sourceDirectory, AbsolutePathInterface $targetDirectory, RemarksConfig $config): void
    {
        // docker run -v "$PWD/files":/store laauurraaa/remarks-bin /store /store --targets md
        $source_dir = $sourceDirectory->string();
        $target_dir = $targetDirectory->string();
        $params = $config->toRemarksParams();
        $command =

            "docker run -v \"$source_dir/\":/in -v \"$target_dir\":/out laauurraaa/remarks-bin:{$this->remarks_version} /in /out $params 2>&1";
        exec($command, $output, $result_code);

        if ($result_code !== 0) {
            throw new RuntimeException("remarks-bin docker failed with error_code: `$result_code`: `" . implode
                ("\n", $output) . '`');
        }
    }

}
