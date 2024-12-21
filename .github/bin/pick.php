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
