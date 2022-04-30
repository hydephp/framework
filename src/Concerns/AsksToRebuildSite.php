<?php

namespace Hyde\Framework\Concerns;

use Illuminate\Support\Facades\Artisan;

trait AsksToRebuildSite
{
    protected function askToRebuildSite()
    {
        if ($this->option('no-interaction')) {
            return;
        }

        if ($this->confirm('Would you like to rebuild the site?', 'Yes')) {
            $this->line('Okay, building site!');
            Artisan::call('build');
            $this->info('Site is built!');
        } else {
            $this->line('Okay, you can always run the build later!');
        }
    }
}
