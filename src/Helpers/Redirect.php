<?php

namespace Hyde\Framework\Helpers;

use Hyde\Framework\Hyde;

/**
 * A basic redirect page. Is not discoverable by Hyde, instead you manually need to create the pages.
 * Typically, you'll do this in a build task. Pass data a new object, then call the store method.
 *
 * @example `Redirect::make('foo', 'bar')->store();`
 */
class Redirect
{
    public string $path;
    public string $destination;

    public function __construct(string $path, string $destination)
    {
        $this->path = $path;
        $this->destination = $destination;
    }

    public static function make(string $path, string $destination): static
    {
        return new static($path, $destination);
    }

    public function render(): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url='$this->destination'" />

        <title>Redirecting to $this->destination</title>
    </head>
    <body>
        Redirecting to <a href="$this->destination">$this->destination</a>.
    </body>
</html>
HTML;
    }

    public function store(): static
    {
        file_put_contents(Hyde::sitePath("$this->path.html"), $this->render());

        return $this;
    }
}
