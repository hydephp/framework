<?php

namespace Hyde\Framework\Models;

trait HasFeaturedImage
{
    public Image $image;

    public function constructFeaturedImage(): void
    {
        if (isset($this->matter['image'])) {
            if (is_string($this->matter['image'])) {
                $this->image = $this->constructBaseImage($this->matter['image']);
            }
            if (is_array($this->matter['image'])) {
                $this->image = $this->constructFullImage($this->matter['image']);
            }
        }
    }

    public function constructBaseImage(string $image): Image
    {
        if (str_starts_with($image, 'http')) {
            return new Image([
                'uri' => $image,
            ]);
        }

        return new Image([
            'path' => $image,
        ]);
    }

    public function constructFullImage(array $image): Image
    {
        return new Image($image);
    }
}