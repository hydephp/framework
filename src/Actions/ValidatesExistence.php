<?php

namespace Hyde\Framework\Actions;

use Exception;
use Hyde\Framework\Hyde;

trait ValidatesExistence
{
    /**
     * Check if a supplied source file exists or throw an exception.
     *
     * @throws Exception If the file does not exist.
     */
    public function validateExistence(string $model, string $slug): void
    {
        $filepath = $model::$sourceDirectory.'/'.
            $slug.$model::$fileExtension;

        if (! file_exists(Hyde::path($filepath))) {
            throw new Exception("File $filepath not found.", 404);
        }
    }
}
