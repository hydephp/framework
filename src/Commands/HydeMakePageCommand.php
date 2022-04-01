<?php

namespace Hyde\Framework\Commands;

use Exception;
use LaravelZero\Framework\Commands\Command;

/**
 * Hyde Command to scaffold a new Markdown or Blade page file.
 */
class HydeMakePageCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
    protected $signature = 'make:page 
		{title : The name of the page file to create. Will be used to generate the slug}
		{--type=markdown : The type of page to create (markdown or blade)}
		{--force : Overwrite any existing files}';

	/**
	 * The console command description.
	 * 
	 * @var string
	 */
    protected $description = 'Scaffold a new Markdown or Blade page file';

	/**
	 * The page title.
	 */
	public string $title;

	/**
	 * The page type.
	 */
	public string $type;

	/**
	 * Execute the console command.
	 * 
	 * @return int
	 */
	public function handle(): int
	{
		$this->title('Creating a new page!');

		$this->validateOptions();

		$creator = new \Hyde\Framework\Actions\CreatesNewPageSourceFile($this->title, $this->type);

		$this->line("Created file $creator->path");

		return 0;
	}

	/**
	 * Validate the options passed to the command.
	 * 
	 * @return void
	 */
	protected function validateOptions(): void
	{
		$this->title = $this->argument('title');
		
		$type = strtolower($this->option('type') ?? 'markdown');

		if (!in_array($type, ['markdown', 'blade'])) {
			throw new Exception("Invalid page type: $type", 400);
		}

		// Set the type to the fully qualified class name
		if ($type === 'markdown') {
			$this->type = \Hyde\Framework\Models\MarkdownPage::class;
		} else {
			$this->type = \Hyde\Framework\Models\BladePage::class;
		}
	}
}