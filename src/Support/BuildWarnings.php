<?php

declare(strict_types=1);

namespace Hyde\Support;

use Hyde\Facades\Config;
use Hyde\Framework\Exceptions\BuildWarning;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Console\Style\OutputStyle;
use function app;
use function sprintf;

/**
 * @experimental
 *
 * @see \Hyde\Framework\Testing\Unit\BuildWarningsTest
 */
class BuildWarnings
{
    /** @var array<\Hyde\Framework\Exceptions\BuildWarning> */
    protected array $warnings = [];

    public static function getInstance(): static
    {
        if (! app()->bound(self::class)) {
            app()->singleton(self::class);
        }

        return app(self::class);
    }

    public static function report(BuildWarning|string $warning): void
    {
        static::getInstance()->warnings[] = $warning instanceof BuildWarning ? $warning : new BuildWarning($warning);
    }

    /** @return array<\Hyde\Framework\Exceptions\BuildWarning> */
    public static function getWarnings(): array
    {
        return static::getInstance()->warnings;
    }

    public static function hasWarnings(): bool
    {
        return count(static::getInstance()->warnings) > 0;
    }

    public static function reportsWarnings(): bool
    {
        return Config::getBool('hyde.log_warnings', true);
    }

    public static function reportsWarningsAsExceptions(): bool
    {
        return Config::getBool('hyde.convert_build_warnings_to_exceptions', false);
    }

    public static function writeWarningsToOutput(OutputStyle $output, bool $verbose = false): void
    {
        if (static::reportsWarningsAsExceptions()) {
            self::renderWarningsAsExceptions($output);
        } else {
            self::renderWarnings($output, $verbose);
        }
    }

    protected static function renderWarnings(OutputStyle $output, bool $verbose): void
    {
        foreach (static::getWarnings() as $number => $warning) {
            $output->writeln(sprintf(' %s. <comment>%s</comment>', $number + 1, $warning->getMessage()));
            if ($verbose) {
                $output->writeln(sprintf('    <fg=gray>%s:%s</>', $warning->getFile(), $warning->getLine()));
            }
        }
    }

    protected static function renderWarningsAsExceptions(OutputStyle $output): void
    {
        foreach (static::getWarnings() as $warning) {
            app(ExceptionHandler::class)->renderForConsole($output, $warning);
        }
    }
}
