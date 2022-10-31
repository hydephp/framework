<?php

declare(strict_types=1);

namespace Hyde\Console\Commands;

use Hyde\Framework\Actions\CreatesNewPageSourceFile;
use Hyde\Framework\Exceptions\UnsupportedPageTypeException;
use Hyde\Pages\BladePage;
use Hyde\Pages\DocumentationPage;
use Hyde\Pages\MarkdownPage;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to scaffold a new Markdown or Blade page file.
 *
 * @see \Hyde\Framework\Testing\Feature\Commands\MakePageCommandTest
 */
class MakePageCommand extends Command
{
    /** @var string */
    protected $signature = 'make:page 
		{title? : The name of the page file to create. Will be used to generate the slug}
		{--type=markdown : The type of page to create (markdown, blade, or docs)}
        {--blade : Create a Blade page}
        {--docs : Create a Documentation page}
		{--force : Overwrite any existing files}';

    /** @var string */
    protected $description = 'Scaffold a new Markdown, Blade, or documentation page file';

    /**
     * The page title.
     */
    public string $title;

    /**
     * The selected page type.
     */
    public string $selectedType;

    /**
     * The page type.
     */
    public string $type;

    /**
     * Can the file be overwritten?
     */
    public bool $force;

    public function handle(): int
    {
        $this->title('Creating a new page!');

        $this->title = $this->argument('title')
            ?? $this->ask('What is the title of the page?')
            ?? 'My New Page';

        $this->validateOptions();

        $this->line('<info>Creating a new '.ucwords($this->selectedType).' page with title:</> '.$this->title."\n");

        $this->force = $this->option('force') ?? false;

        $creator = new CreatesNewPageSourceFile($this->title, $this->type, $this->force);

        $this->info("Created file $creator->outputPath");

        return Command::SUCCESS;
    }

    protected function validateOptions(): void
    {
        $type = $this->getSelectedType();

        // Set the type to the fully qualified class name
        if ($type === 'markdown') {
            $this->type = MarkdownPage::class;

            return;
        }
        if ($type === 'blade') {
            $this->type = BladePage::class;

            return;
        }
        if ($type === 'docs' || $type === 'documentation') {
            $this->type = DocumentationPage::class;

            return;
        }

        throw new UnsupportedPageTypeException("Invalid page type: $type");
    }

    protected function getSelectedType(): string
    {
        $type = 'markdown';

        if ($this->option('type') !== null) {
            $type = strtolower($this->option('type'));
        }

        if ($this->option('blade')) {
            $type = 'blade';
        } elseif ($this->option('docs')) {
            $type = 'documentation';
        }

        return $this->selectedType = $type;
    }
}
