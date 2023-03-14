<?php

declare(strict_types=1);

namespace Hyde\Support\Models;

use DateTime;
use Stringable;

/**
 * Parse a date string and create normalized formats.
 */
class DateString implements Stringable
{
    /** Date format constants */
    final public const DATETIME_FORMAT = 'c';
    final public const SENTENCE_FORMAT = 'l M jS, Y, \a\t g:ia';
    final public const SHORT_FORMAT = 'M jS, Y';

    /** The original date string. */
    public readonly string $string;

    /** The parsed date object. */
    public readonly DateTime $dateTimeObject;

    /** The machine-readable datetime string. */
    public readonly string $datetime;

    /** The human-readable sentence string. */
    public readonly string $sentence;

    /** Shorter version of the sentence string. */
    public readonly string $short;

    public function __construct(string $string)
    {
        $this->string = $string;
        $this->dateTimeObject = new DateTime($this->string);

        $this->datetime = $this->dateTimeObject->format(self::DATETIME_FORMAT);
        $this->sentence = $this->dateTimeObject->format(self::SENTENCE_FORMAT);
        $this->short = $this->dateTimeObject->format(self::SHORT_FORMAT);
    }

    public function __toString(): string
    {
        return $this->short;
    }
}
