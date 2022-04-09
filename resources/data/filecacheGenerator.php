<?php

/**
 * @internal Internal devtool to generate a filecache.json file.
 * @usage (cli from the data directory) php filecacheGenerator.php
 */
$time_start = microtime(true);

$filecache = [];

$bladeFiles = glob(__DIR__.'/../../resources/views/**/*.blade.php');
$frontendFiles = glob(__DIR__.'/../../resources/frontend/*.{css,scss,js}', GLOB_BRACE);

$files = array_merge($bladeFiles, $frontendFiles);

foreach ($files as $file) {
    $filecache[str_replace(__DIR__.'/../../', '', $file)] = [
        'unixsum' => unixsum(file_get_contents($file)),
        'last_modified' => filemtime($file),
    ];
}

function unixsum(string $string): string
{
    // Replace all end of line characters with a unix line ending
    $string = str_replace(["\r\n", "\r"], "\n", $string);

    return md5($string);
}

file_put_contents('filecache.json', json_encode($filecache, JSON_PRETTY_PRINT));

$execution_time = (microtime(true) - $time_start);
echo sprintf(
    'Saved checksums for %s files in %s seconds (%sms)',
    sizeof($files),
    number_format(
        $execution_time,
        8
    ),
    number_format($execution_time * 1000, 2)
);

exit(0);
