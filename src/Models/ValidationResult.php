<?php

namespace Hyde\Framework\Models;

/**
 * @see \Hyde\Testing\Feature\Services\ValidationServiceTest
 * @see \Hyde\Testing\Feature\Commands\HydeValidateCommandTest
 */
class ValidationResult
{
    public string $message;
    public string $tip;

    public bool $passed;
    public bool $skipped = false;

    public function __construct(string $defaultMessage = 'Generic check')
    {
        $this->message = $defaultMessage;
    }

    public function pass(?string $withMessage = null): self
    {
        $this->passed = true;
        if ($withMessage) {
            $this->message = $withMessage;
        }

        return $this;
    }

    public function fail(?string $withMessage = null): self
    {
        $this->passed = false;
        if ($withMessage) {
            $this->message = $withMessage;
        }

        return $this;
    }

    public function skip(?string $withMessage = null): self
    {
        $this->skipped = true;
        if ($withMessage) {
            $this->message = $withMessage;
        }

        return $this;
    }

    public function withTip(string $withTip): self
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
        if ($this->skipped) {
            return 1;
        }
        if ($this->passed()) {
            return 0;
        }

        return 2;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function formattedMessage(?string $withTimeString = null): string
    {
        $string = '  '.$this->formatResult($this->message).$this->formatTimeString($withTimeString);
        if ($this->tip()) {
            $string .= "\n".str_repeat(' ', 9).$this->formatTip($this->tip);
        }

        return $string;
    }

    protected function formatResult(string $message): string
    {
        if ($this->statusCode() === 0) {
            return $this->formatPassed($message);
        }
        if ($this->statusCode() === 2) {
            return $this->formatFailed($message);
        }

        return $this->formatSkipped($message);
    }

    protected function formatPassed(string $message): string
    {
        return '<fg=white;bg=green> PASS <fg=green> '.$message.'</></>';
    }

    protected function formatFailed(string $message): string
    {
        return '<fg=gray;bg=yellow> FAIL <fg=yellow> '.$message.'</></>';
    }

    protected function formatSkipped(string $message): string
    {
        return '<fg=white;bg=gray> SKIP <fg=gray> '.$message.'</></>';
    }

    protected function formatTimeString(string $time): string
    {
        return '<fg=gray> ('.$time.'ms)</>';
    }

    protected function formatTip(string $tip): string
    {
        return '<fg=gray>'.$tip.'</>';
    }
}
