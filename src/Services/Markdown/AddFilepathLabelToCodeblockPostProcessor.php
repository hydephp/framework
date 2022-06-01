<?php

namespace Hyde\Framework\Services\Markdown;

/**
* @todo Add config option to enable/disable this processor
*/
class AddFilepathLabelToCodeblockPostProcessor
{
    public static function process(string $html): string
    {
        $torchlightKey = '<!-- Syntax highlighted by torchlight.dev -->';        
        $withTorchlight = str_contains($html, $torchlightKey);
        
        
        $lines = explode("\n", $html);
        
        foreach ($lines as $index => $line) {
            if (str_starts_with($line, '<!-- HYDE[Filepath]')) {
                $path = static::trimHydeDirective($line);
                unset($lines[$index]);
                $codeBlockLine = $index + 1;
                $label = static::resolveTemplate($path, $lines[$codeBlockLine]);
                
                $lines[$codeBlockLine] = $withTorchlight
                ? static::resolveTorchlightCodeLine($torchlightKey, $label, $lines[$codeBlockLine])
                : static::resolveCodeLine($label, $lines[$codeBlockLine]);
            }
        }
        // Insert the label after the '<pre><code class="language-*">' using regex
        
        return implode("\n", $lines);
    }
    
    public static function preprocess(string $markdown): string
    {
        $lines = explode("\n", $markdown);
        
        foreach ($lines as $index => $line) {
            if (static::lineMatchesPattern($line) && ! str_contains($line, '// HYDE! {"shortcodes": false} HYDE! //')) {
                // Add the meta-block two lines before the pattern, placing it just above the code block.
                // This prevents the meta-block from interfering with other processes.
                $lines[$index - 2] .= "\n".'<!-- HYDE[Filepath]'.trim(str_replace(static::$patterns, '', $line)).' -->'; 
                
                // Remove the original comment lines
                unset($lines[$index]);
                // Only unset the next line if it's empty
                if (trim($lines[$index + 1]) === '') {
                    unset($lines[$index + 1]);
                }
            }
        }
        
        return implode("\n", $lines);
    }
    
    protected static array $patterns = [
        '// filepath: ',
        '// Filepath: ',
        '# filepath: ',
        '# Filepath: ',
        '// filepath ',
        '// Filepath ',
        '# filepath ',
        '# Filepath ',
    ];
    
    protected static function lineMatchesPattern(string $line): bool
    {
        foreach (static::$patterns as $pattern) {
            if (str_starts_with($line, $pattern)) {
                return true;
            }
        }
        
        return false;
    }
    
    protected static function trimHydeDirective(string $line): string
    {
        return trim(str_replace('-->', '', str_replace(
            '<!-- HYDE[Filepath]', '', $line))
        );
    }
    
    protected static function resolveTemplate(string $path, string $line): string
    {
        $template = '<small class="filepath"><span class="sr-only">Filepath: </span>%s</small>';
        return sprintf($template, $path, $line);
    }

    protected static function resolveTorchlightCodeLine(string $torchlightKey, string $label, $lines): string|array
    {
        return str_replace(
            $torchlightKey,
            $torchlightKey . $label,
            $lines
        );
    }

    protected static function resolveCodeLine(string $label, $lines): string|array|null
    {
        return preg_replace(
            '/<pre><code class="language-(.*?)">/',
            '<pre><code class="language-$1">' . $label,
            $lines
        );
    }
}