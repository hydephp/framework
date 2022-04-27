<?php

namespace Hyde\Framework;

use Hyde\Framework\Contracts\AbstractPage;

interface PageParserContract
{
    /**
     * Handle the parsing job.
     *
     * @return void
     */
    public function execute(): void;

    /**
     * Get the parsed page object.
     *
     * @return AbstractPage
     */
    public function get(): AbstractPage;
}
