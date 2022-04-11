<?php

namespace Hyde\Framework\Models;

trait HasDateString
{
    public ?Datestring $date = null;

    public function constructDateString(): void
    {
        if (isset($this->matter['date'])) {
            $this->date = new Datestring($this->matter['date']);
        }
    }
}
