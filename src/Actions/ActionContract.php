<?php

namespace Hyde\Framework\Actions;

/**
 * Interface ActionContract
 * @package Hyde\Framework\Actions
 *
 * An Action is a class that handles a single responsibility.
 * Actions are useful to separate business logic from controllers.
 *
 * If the Action is part a Service it should be in the ServiceActions directory.
 * See Services which are used to house the logic of a larger system.
 */
interface ActionContract
{
    /**
     * Execute the action.
     */
    public function execute();
}