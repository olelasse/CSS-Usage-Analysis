<?php
// src/analysis.php

function getCssSelectors(string $cssContent): array 
{
    $validSelectors = []; 

    // Finds all potential candidates
    preg_match_all(
        '/(\.[a-zA-Z0-9_-]+)|(\#[a-zA-Z0-9_-]+)/', 
        $cssContent, 
        $matches, 
        PREG_SET_ORDER
    );

    foreach ($matches as $match) {
        if (!empty($match[1])) { 
            $validSelectors[] = trim($match[1]);
        } elseif (!empty($match[2])) { 
            $validSelectors[] = trim($match[2]);
        }
    }

    // Goes through the candidates and removes those that are invalid
    foreach ($validSelectors as $key => $selector) {
        switch (substr($selector, 0, 1)) {
            case '.':
                // Checks classes starting with numbers to avoid em/rgba values
                if (is_numeric(substr($selector, 1, 1))) {
                    if (str_ends_with($selector, 'em') || strlen(substr($selector, 1)) === 1) {
                        unset($validSelectors[$key]);
                    }
                }
                break;
            case '#':
                // Removes probable color codes
                $selectorName = substr($selector, 1);
                if (
                    preg_match('/^[0-9a-fA-F]+$/', $selectorName)
                    && in_array(strlen($selectorName), [3, 4, 6, 8])
                ) {
                    unset($validSelectors[$key]);
                }
                break;
        }
    }
    return array_values(array_unique($validSelectors)); // Returns a re-indexed, unique list
}

function getPatternFromSelector(string $fullSelector): string 
{
    $baseSelector = preg_quote(substr($fullSelector, 1), '/');

    return match (substr($fullSelector, 0, 1)) {
        '.' => '/class\s*=\s*["\'][^"\']*?\b' . $baseSelector . '\b[^"\']*?["\']/',
        '#' => '/id\s*=\s*["\']\s*' . $baseSelector . '\s*["\']/',
        default => ''
    };
}

function getSelectorUsage(array $selectors, RecursiveIteratorIterator $files): array 
{
    $usedSelectorsData = [];
    $unusedSelectors = $selectors;

    foreach ($files as $file) {
        // We only check for files with relevant extensions
        if (!$file->isFile() || !in_array($file->getExtension(), ['php', 'phtml', 'html'])) {
            continue;
        }

        $filePath = $file->getPathname();
        $fileContentLines = file($filePath, FILE_IGNORE_NEW_LINES);

        foreach ($selectors as $selector) {
            $pattern = getPatternFromSelector($selector);
            if (empty($pattern)) {
                continue;
            }

            foreach ($fileContentLines as $lineNumber => $lineContent) {
                if (!preg_match($pattern, $lineContent)) {
                    continue; // Move on to the next line
                }
                
                // The class has been found, add to used and remove from unused
                $usedSelectorsData[$selector][] = [
                    'file' => $filePath,
                    'line' => $lineNumber + 1,
                    'line_content' => $lineContent
                ];

                if (($key = array_search($selector, $unusedSelectors)) !== false) {
                    unset($unusedSelectors[$key]);
                }
            }
        }
    }

    return [
        'used'   => $usedSelectorsData,
        'unused' => array_values($unusedSelectors) // Return re-indexed
    ];
}
