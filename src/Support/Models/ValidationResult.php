<?php

declare(strict_types=1);

namespace Hyde\Support\Models;

class ValidationResult
{
    final public const PASSED = 0;
    final public const SKIPPED = 1;
    final public const FAILED = 2;

    protected string $message;
    protected string $tip;

    protected bool $passed = false;
    protected bool $skipped = false;

    public function __construct(string $defaultMessage = 'Generic check')
    {
        $this->message = $defaultMessage;
    }

    public function pass(?string $withMessage = null): static
    {
        $this->passed = true;

        return $this->withMessage($withMessage);
    }

    public function fail(?string $withMessage = null): static
    {
        $this->passed = false;

        return $this->withMessage($withMessage);
    }

    public function skip(?string $withMessage = null): static
    {
        $this->skipped = true;

        return $this->withMessage($withMessage);
    }

    public function withTip(string $withTip): static
    {
        $this->tip = $withTip;

        return $this;
    }

    public function tip(): string|false
    {
        return $this->tip ?? false;
    }

    public function skipped(): bool
    {
        return $this->skipped;
    }

    public function passed(): bool
    {
        return $this->passed;
    }

    public function failed(): bool
    {
        return ! $this->passed;
    }

    public function statusCode(): int
    {
        if ($this->skipped()) {
            return self::SKIPPED;
        }
        if ($this->passed()) {
            return self::PASSED;
        }

        return self::FAILED;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function formattedMessage(?string $withTimeString = null): string
    {
        $string = "  {$this->formatResult($this->message)} {$this->formatTimeString($withTimeString)}";
        if ($this->tip()) {
            $string .= "\n".str_repeat(' ', 9).$this->formatTip($this->tip);
        }

        return $string;
    }

    protected function formatResult(string $message): string
    {
        return match ($this->statusCode()) {
            self::PASSED => $this->formatPassed($message),
            self::FAILED => $this->formatFailed($message),
            default => $this->formatSkipped($message),
        };
    }

    protected function formatPassed(string $message): string
    {
        return "<fg=white;bg=green> PASS <fg=green> $message</></>";
    }

    protected function formatFailed(string $message): string
    {
        return "<fg=gray;bg=yellow> FAIL <fg=yellow> $message</></>";
    }

    protected function formatSkipped(string $message): string
    {
        return "<fg=white;bg=gray> SKIP <fg=gray> $message</></>";
    }

    protected function formatTimeString(string $time): string
    {
        return "<fg=gray>({$time}ms)</>";
    }

    protected function formatTip(string $tip): string
    {
        return "<fg=gray>$tip</>";
    }

    protected function withMessage(?string $withMessage): static
    {
        if ($withMessage) {
            $this->message = $withMessage;
        }

        return $this;
    }
}
