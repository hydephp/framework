<?php

namespace Hyde\Framework\Models;

trait HasDateString
{
    public ?DateString $date = null;

    public function constructDateString(): void
    {
        if (isset($this->matter['date'])) {
            $this->date = new DateString($this->matter['date']);
        }
    }
}
