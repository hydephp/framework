# Draft for the release notes of upcoming HydePHP versions

---

# v0.28.0-beta - Refactor configuration files 

## Changes to the configuration files

The entire configuration system has been refactored.

### Snake_case is used for all configuration keys

All configuration keys are now in the snake_case_format. Published configuration files will need to be updated accordingly. This is pretty fast in a modern code editor like VS Code.

### Documentation options have been moved to a new file

The documentation page specific options have been moved to the `config/docs.php` file.
You may need to republish Blade views if you have done so before. 

This is also easy to do in a modern code editor. See this example of the search and replace I used
to update the codebase:

`hyde.docs_sidebar_header_title` => `docs.header_title`

### Authors.yml has been removed

The authors.yml and related services have been removed. Define authors in the main Hyde config instead.

### Deprecations and removals

The deprecated option named `hyde.docs_directory` has been removed.

Use `docs.output_directory` instead.

## What's Changed
* Refactor configuration to use snake_case for all options, and extract documentation settings to own file by @caendesilva in https://github.com/hydephp/framework/pull/444
* Refactor author configuration system by @caendesilva in https://github.com/hydephp/framework/pull/449


**Full Changelog**: https://github.com/hydephp/framework/compare/v0.27.12-beta...v0.28.0-beta