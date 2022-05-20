<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Contracts\ActionContract;
use Hyde\Framework\Models\Image;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @see \Tests\Feature\FindsContentLengthForImageObjectTest
 */
class FindsContentLengthForImageObject implements ActionContract
{
    protected Image $image;

    /**
     * Testing adding console debug output
     */
    protected OutputInterface $output;

    public function __construct(Image $image, bool $withOutput = false)
    {
        $this->image = $image;
     
        if ($withOutput) {
            $this->output = new \Symfony\Component\Console\Output\ConsoleOutput();
        }
    }

    public function execute(): int
    {
        $this->write('Attempting to find content length for image object...');

        if ($this->isImageStoredRemotely()) {
            return $this->fetchRemoteImageInformation();
        }

        return $this->fetchLocalImageInformation();
    }

    protected function isImageStoredRemotely(): bool
    {
        return str_starts_with($this->image->getSource(), 'http');
    }

    protected function fetchRemoteImageInformation(): int
    {
        $this->write('Fetching remote image information...');

        $response = Http::withHeaders([
            'User-Agent' => config('hyde.http_user_agent', 'RSS Request Client'),
        ])->head($this->image->getSource());


        $headers = $response->headers();

        if (array_key_exists('Content-Length', $headers)) {
            $this->write('Found content length in headers.');
            return (int) key(array_flip($headers['Content-Length']));
        }

        $this->write('<comment>Warning</comment> Could not find content length in headers.');

        return 0;
    }   

    protected function fetchLocalImageInformation(): int
    {
        $this->write('Fetching local image information...');

        if (! file_exists($this->image->getSource())) {
            $this->write('<comment>Warning</comment> Could not find image file.');

            return 0;
        }

        $this->write('Found image file.');

        return filesize($this->image->getSource());
    }

    protected function write(string $string): void
    {
        if (isset($this->output)) {
            $this->output->writeln($string);
        }
    }
}