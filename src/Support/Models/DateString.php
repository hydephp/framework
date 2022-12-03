<?php

declare(strict_types=1);

namespace Hyde\Support\Models;

use DateTime;
use Stringable;

/**
 * Parse a date string and create normalized formats.
 *
 * @see \Hyde\Framework\Testing\Unit\DateStringTest
 */
class DateString implements Stringable
{
    /** Date format constants */
    public const DATETIME_FORMAT = 'c';
    public const SENTENCE_FORMAT = 'l M jS, Y, \a\t g:ia';
    public const SHORT_FORMAT = 'M jS, Y';

    /** The original date string. */
    public string $string;

    /** The parsed date object. */
    public DateTime $dateTimeObject;

    /** The machine-readable datetime string. */
    public string $datetime;

    /** The human-readable sentence string. */
    public string $sentence;

    /** Shorter version of the sentence string. */
    public string $short;

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