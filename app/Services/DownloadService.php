<?php

namespace App\Services;

use App\Helpers\FileManipulations;
use App\Models\User;
use Eloquent\Pathogen\RelativePath;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use UnexpectedValueException;

class DownloadService
{
    public function downloadReMarkableInputZip(int $user_id, string $sync_id)
    {
        $storage = Storage::disk('efs');

        FileManipulations::ensureDirectoryTreeExists($storage, new RelativePath(["user-{$user_id}", "input_documents"]));
        $rmNotebookPath = new RelativePath(["user-{$user_id}", "input_documents", "$sync_id-input.zip"]);

        if (!$storage->exists($rmNotebookPath->string())) {
            try {
                FileManipulations::zipDirectory($storage, new RelativePath(["user-{$user_id}", 'jobs', $sync_id, 'extractedFiles']), $rmNotebookPath);
            } catch (UnexpectedValueException) {
                throw new GoneHttpException("Input files do not exist anymore");
            }
        }
        return $storage->temporaryUrl($rmNotebookPath->string(), now()->addMinutes(5));
    }

    /**
     * @param int $user_id
     * @param string $sync_id
     * @return string
     */
    public function downloadProcessedRemarksZip(int $user_id, string $sync_id): string
    {
        $storage = Storage::disk('efs');

        $path = "user-{$user_id}/processed/${sync_id}.zip";
        if ($storage->exists($path)) {
            return $storage->temporaryUrl($path, now()->addMinutes(5));
        }
        throw new GoneHttpException("File has been deleted");
    }
}
