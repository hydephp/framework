<?php

declare(strict_types=1);

namespace Hyde\Framework\Actions;

use Hyde\Facades\Filesystem;
use Hyde\Framework\Exceptions\FileNotFoundException;
use Illuminate\Support\Facades\Blade;

/**
 * Compile any Blade file using the Blade facade as it allows us to render
 * it without having to register the directory with the view finder.
 */
class AnonymousViewCompiler
{
    protected string $viewPath;
    protected array $data;

    public static function call(string $viewPath, array $data = []): string
    {
        return (new self($viewPath, $data))->__invoke();
    }

    public function __construct(string $viewPath, array $data = [])
    {
        $this->viewPath = $viewPath;
        $this->data = $data;
    }

    public function __invoke(): string
    {
        if (Filesystem::missing($this->viewPath)) {
            throw new FileNotFoundException($this->viewPath);
        }

        return Blade::render(
            Filesystem::getContents($this->viewPath),
            $this->data
        );
    }
}
