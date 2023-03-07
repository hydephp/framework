<?php

declare(strict_types=1);

namespace Hyde\Console\Commands;

use Exception;
use Hyde\Console\Concerns\Command;
use Hyde\Framework\Actions\CreatesNewMarkdownPostFile;
use function is_string;
use function sprintf;
use function ucwords;

/**
 * Hyde Command to scaffold a new Markdown Post.
 */
class MakePostCommand extends Command
{
    /** @var string */
    protected $signature = 'make:post
                            {title? : The title for the Post. Will also be used to generate the filename}
                            {--force : Should the generated file overwrite existing posts with the same filename?}';

    /** @var string */
    protected $description = 'Scaffold a new Markdown blog post file';

    public function handle(): int
    {
        $this->title('Creating a new post!');

        $title = $this->getTitle();

        [$description, $author, $category] = $this->getSelections();

        $creator = new CreatesNewMarkdownPostFile($title, $description, $category, $author);

        $this->displaySelections($creator);

        if (! $this->confirm('Do you wish to continue?', true)) {
            $this->info('Aborting.');

            return Command::USER_EXIT;
        }

        return $this->createPostFile($creator);
    }

    protected function getTitle(): string
    {
        $this->line($this->argument('title')
            ? '<info>Selected title: '.$this->argument('title')."</info>\n"
            : 'Please enter the title of the post, it will be used to generate the filename.'
        );

        return $this->argument('title')
            ?? $this->askForString('What is the title of the post?')
            ?? 'My New Post';
    }

    /** @return array<?string> */
    protected function getSelections(): array
    {
        $this->line('Tip: You can just hit return to use the defaults.');

        $description = $this->askForString('Write a short post excerpt/description');
        $author = $this->askForString('What is your (the author\'s) name?');
        $category = $this->askForString('What is the primary category of the post?');

        return [$description, $author, $category];
    }

    protected function displaySelections(CreatesNewMarkdownPostFile $creator): void
    {
        $this->info('Creating a post with the following details:');

        foreach ($creator->toArray() as $key => $value) {
            $this->line(sprintf('%s: %s', ucwords($key), $value));
        }

        $this->line("Identifier: {$creator->getIdentifier()}");
    }

    protected function createPostFile(CreatesNewMarkdownPostFile $creator): int
    {
        try {
            $path = $creator->save($this->option('force'));
            $this->info("Post created! File is saved to $path");

            return Command::SUCCESS;
        } catch (Exception $exception) {
            $this->error('Something went wrong when trying to save the file!');
            $this->warn($exception->getMessage());

            if ($exception->getCode() === 409) {
                $this->comment('If you want to overwrite the file supply the --force flag.');
            }

            return (int) $exception->getCode();
        }
    }

    protected function askForString(string $question, ?string $default = null): ?string
    {
        return is_string($answer = $this->output->ask($question, $default)) ? $answer : null;
    }
}
