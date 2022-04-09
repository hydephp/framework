<?php

/**
 * @internal Internal devtool to generate a filecache.json file.
 * @usage (cli from the data directory) php filecacheGenerator.php
 */

$time_start = microtime(true);

$filecache = [];

$bladeFiles = glob(__DIR__ . '/../../resources/views/**/*.blade.php');
$frontendFiles = glob(__DIR__ . '/../../resources/frontend/*.{css,scss,js}', GLOB_BRACE);

$files = array_merge($bladeFiles, $frontendFiles);

foreach ($files as $file) {
	$filecache[str_replace(__DIR__ . '/../../', '', $file)] = [
		'md5sum' => md5_file($file),
		'last_modified' => filemtime($file),
	];
}

file_put_contents('filecache.json', json_encode($filecache));

$execution_time = (microtime(true) - $time_start);
echo ('Saved checksums for ' . sizeof($files) . ' files in ' . number_format(
	$execution_time,
	8
) . ' seconds (' . number_format(($execution_time * 1000), 2) . 'ms)');

exit(0);