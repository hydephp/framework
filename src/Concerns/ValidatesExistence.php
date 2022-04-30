<?php

namespace Hyde\Framework\Concerns;

use Exception;
use Hyde\Framework\Hyde;

/**
 * Validate the existance of a Page model's source file.
 */
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
