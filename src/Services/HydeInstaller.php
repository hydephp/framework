<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Hyde;

/**
 * Updates the .env file with the properties set in the object
 *
 * Intended to be used as part of an interactive CLI process
 */
class HydeInstaller
{
    public string $name;
    public string $site_url;

    public string|null $homepage;

    public array $warnings = [];
    public bool $allowFileOverwrites = false;

    public function __construct()
    {
        $this->loadDefaults();
        
    }

    /**
     * Format the site url to ensure it is the proper format
     * @param string $site_url
     * @return string
     */
    public function setSiteUrl(string $site_url): string
    {
        if (filter_var($site_url, FILTER_VALIDATE_URL) === false) {
            $this->warnings['site_url_warning'] = [
                'level' => 'notice',
                'message' => "Supplied url $site_url is malformed. Attempting to fix it.",
            ];
            if (!str_starts_with($site_url, 'http')) {
                $site_url = "https://$site_url";
                $site_url = rtrim($site_url, '/');
                $this->warnings['site_url_warning'] = [
                    'level' => 'notice',
                    'message' => "It seems that a domain was supplied, so an URI prefix was added.",
                ];
            }
        }

        return $site_url;
    }

    /**
     * Save the current object state to file
     * @return $this
     */
    public function save(): static
    {
        // Save dotenv parameters
        $this->saveToDotEnv();

        // Publish homepage if not null
        if (($this->homepage !== null) && ($this->homepage !== 'current')) {
            $this->publishHomepage();
        }

        return $this;
    }

    /**
     * Load the default values.
     * If a setting has previously been set in the dotenv it will be used.
     * @return void
     */
    private function loadDefaults(): void
    {
        $this->name = config('hyde.name');
        $this->site_url = config('hyde.site_url') ?? 'https://example.org';

        $this->homepage = null;
    }

    /**
     * Save the parameters to the dotenv file
     * @return void
     */
    private function saveToDotEnv()
    {
        $this->ensureDotEnvIsSetup();

        $this->saveEnv('SITE_NAME', $this->name);
        $this->saveEnv('SITE_URL', $this->site_url);
    }

    /**
     * Save or update a dotenv parameter
     * @param string $property
     * @param string $value
     * @return void
     */
    private function saveEnv(string $property, string $value)
    {
        $stream = explode("\n", file_get_contents(Hyde::path('.env')));

        $hasExistingProperty = false;
        foreach ($stream as $index => $line) {
            if (str_starts_with($line, $property)) {
                $hasExistingProperty = true;
                $stream[$index] = "$property=$value";
                break;
            }
        }

        if (!$hasExistingProperty) {
            $stream[] = "$property=$value";
        }

        file_put_contents(Hyde::path('.env'), implode("\n", $stream));
    }

    /**
     * Make sure the dotenv file exists, else create it
     * @return void
     */
    private function ensureDotEnvIsSetup()
    {
        if (!file_exists(Hyde::path('.env'))) {
            if (file_exists(Hyde::path('.env.example'))) {
                copy(Hyde::path('.env.example'), Hyde::path('.env'));
            } else {
                file_put_contents(Hyde::path('.env'), '');
            }
        }
    }

    /**
     * Publish the selected homepage view
     * @return void
     */
    private function publishHomepage(): void
    {
        if (file_exists(Hyde::path('resources/views/index.blade.php')) && $this->allowFileOverwrites !== true) {
            $this->warnings[] = [
                'level' => 'warn',
                'message' => 'Refusing to publish homepage as an index.blade.php already exists.',
                'context' => 'You can force the file to overwritten using php hyde publish:homepage --force.'
            ];
            return;
        }

        copy(
            Hyde::path("vendor/hyde/framework/resources/views/homepages/$this->homepage.blade.php"),
            Hyde::path("resources/views/index.blade.php")
        );
    }

}