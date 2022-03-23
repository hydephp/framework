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

    public function __construct()
    {
        $this->loadDefaults();
        
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
        if ($this->homepage !== null) {
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
        $this->site_url = config('hyde.site_url');

        $this->homepage = null;
    }

    /**
     * Save the parameters to the dotenv file
     * @return void
     */
    private function saveToDotEnv()
    {
        $this->ensureDotEnvIsSetup();
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
        if (file_exists(Hyde::path('resources/views/index.blade.php'))) {
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