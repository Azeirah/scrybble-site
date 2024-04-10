<?php

namespace App\Services\interfaces;


use App\DataClasses\RemarksConfig;
use Eloquent\Pathogen\AbsolutePathInterface;

/**
 *
 */
interface RemarksService
{
    /**
     * @param AbsolutePathInterface $sourceDirectory
     * @param AbsolutePathInterface $targetDirectory
     * @param RemarksConfig $config
     * @return void
     */
    public function extractNotesAndHighlights(AbsolutePathInterface $sourceDirectory, AbsolutePathInterface $targetDirectory, RemarksConfig $config): void;
}
