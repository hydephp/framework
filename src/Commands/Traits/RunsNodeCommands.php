<?php

namespace Hyde\Framework\Commands\Traits;

use Exception;

trait RunsNodeCommands
{
    /**
     * @internal
     */
    private function runNodeCommand(string $commandKey, string $message, ?string $actionMessage = null): void
    {
        $this->info($message . ' This may take a second.');
        try {
            $command = $this->parseNodeCommandKey($commandKey);
        }  catch (Exception) {
            $this->error('Command ' . $commandKey . ' not allowed.');
            return;
        }
        try {
            if (app()->environment() !== 'testing') {
                $this->line(shell_exec($command));
            }
        } catch (Exception) {
            $this->warn('Could not '.($actionMessage ?? 'run script').'! Is NPM installed?');
        }
    }

    /**
     * @internal
     * @throws Exception if a key that is not on the whitelist is supplied.
     */
    private function parseNodeCommandKey(string $commandKey): string
    {
        if ($commandKey === 'pretty') {
            return 'npx prettier _site/ --write --bracket-same-line';
        }
        if ($commandKey === 'dev') {
            return 'npm run dev';
        }
        if ($commandKey === 'prod') {
            return 'npm run prod';
        }
        throw new Exception('Invalid command key', 403);
    }
}