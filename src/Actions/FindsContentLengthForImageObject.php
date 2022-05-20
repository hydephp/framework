<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Contracts\ActionContract;
use Hyde\Framework\Models\Image;

/**
 * @see \Tests\Feature\FindsContentLengthForImageObjectTest
 */
class FindsContentLengthForImageObject implements ActionContract
{
    protected Image $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function execute(): int
    {
        return 0;
    }
}