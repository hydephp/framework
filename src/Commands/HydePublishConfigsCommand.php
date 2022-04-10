<?php

namespace Hyde\Framework\Commands;

use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Facades\File;
use Hyde\Framework\Hyde;

/**
 * Publish the Hyde Config Files.
 *
 * @uses HydeBasePublishingCommand
 */
class HydePublishConfigsCommand extends Command
{
    protected $signature = 'update:configs';
    protected $description = 'Publish the default configuration files';

    public function handle(): int
    {
        File::copyDirectory(Hyde::vendorPath('config'), Hyde::path('config'));

        $this->line('<info>Published config files to</info> <comment>' . Hyde::path('config') . '</comment>');

        return 0;
    }
}
