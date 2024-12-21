#!/usr/bin/env php
<?php

// Internal helper to speed up branching up cherry picked commits for pull requests

// Check if we have the correct number of arguments
if ($argc !== 3) {
    echo "\033[31mError: Invalid number of arguments\033[0m\n";
    echo "\033[33mUsage:\033[0m php bin/pick.php <commit-hash> <new-branch-name>\n";
    echo "\033[33mExample:\033[0m php bin/pick.php abc123 feature-branch\n";
    exit(1);
}

// Get arguments
$hash = $argv[1];
$branch = $argv[2];

// Get the commit message
exec("git show $hash --pretty=format:\"%s%n%b\" -s", $output, $returnCode);

if ($returnCode === 0 && !empty($output)) {
    $commitMessage = implode("\n", $output);

    // Check if this matches the subrepo sync format
    if (preg_match('/^Merge pull request #(\d+).*\n(.*?)https:\/\/github\.com\/hydephp\/develop\/commit/', $commitMessage, $matches)) {
        $prNumber = $matches[1];
        $title = trim($matches[2]);

        $echo = "\n\033[33mSuggested PR format:\033[0m\n";
        $echo .= "\033[33mTitle:\033[0m $title\n";
        $echo .= "\033[33mDescription:\033[0m Merges pull request https://github.com/hydephp/develop/pull/$prNumber\n";
    }
}

// Create new branch from master
exec("git checkout -b $branch master", $output, $returnCode);

if ($returnCode !== 0) {
    echo "\033[31mError creating new branch: $branch\033[0m\n";
    exit(1);
}

// Cherry-pick the commit
exec("git cherry-pick $hash", $output, $returnCode);

if ($returnCode !== 0) {
    echo "\033[31mError cherry-picking commit: $hash\033[0m\n";
    exit(1);
}

echo "\033[32mSuccessfully created branch '$branch' and cherry-picked commit '$hash'\033[0m\n";

if (isset($echo)) {
    echo $echo;
}
