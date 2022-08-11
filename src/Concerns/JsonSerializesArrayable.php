<?php

namespace Hyde\Framework\Concerns;

/**
 * Automatically serializes an Arrayable interface when JSON is requested.
 *
 * @see \JsonSerializable
 * @see \Illuminate\Contracts\Support\Arrayable
 * @see \Hyde\Framework\Testing\Unit\JsonSerializesArrayableTest
 */
trait JsonSerializesArrayable
{
    /** @inheritDoc */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /** @inheritDoc */
    abstract public function toArray();
}
