#!/usr/bin/env php
<?php

// Internal helper to speed up branching up cherry picked commits for pull requests

// Check if we have the correct number of arguments
if ($argc !== 3) {
    echo "Usage: php bin/pick.php <commit-hash> <new-branch-name>\n";
    echo "Example: php bin/pick.php abc123 feature-branch\n";
    exit(1);
}

// Get arguments
$hash = $argv[1];
$branch = $argv[2];

// Create new branch from master
$checkoutCommand = "git checkout -b $branch master";
exec($checkoutCommand, $output, $returnCode);

if ($returnCode !== 0) {
    echo "Error creating new branch: $branch\n";
    exit(1);
}

// Cherry-pick the commit
$cherryPickCommand = "git cherry-pick $hash";
exec($cherryPickCommand, $output, $returnCode);

if ($returnCode !== 0) {
    echo "Error cherry-picking commit: $hash\n";
    exit(1);
}

echo "Successfully created branch '$branch' and cherry-picked commit '$hash'\n";

// Get the commit message
$command = "git show $hash --pretty=format:\"%s%n%b\" -s";
exec($command, $output, $returnCode);

if ($returnCode === 0 && !empty($output)) {
    // Join output lines
    $commitMessage = implode("\n", $output);
    
    // Check if this matches the subrepo sync format
    if (preg_match('/^Merge pull request #(\d+).*\n(.*?)https:\/\/github\.com\/hydephp\/develop\/commit/', $commitMessage, $matches)) {
        $prNumber = $matches[1];
        $title = trim($matches[2]);
        
        echo "\nSuggested PR format:\n";
        echo "Title: $title\n";
        echo "Description: Merges pull request https://github.com/hydephp/develop/pull/$prNumber\n";
    }
}
