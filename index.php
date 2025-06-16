<?php

// --- Configuration ---
$phpScriptsFolder = 'folder/'; // Folder with php scripts
$cssFile = 'folder/css/css.css';       // CSS-file to be checked

// --- Helpfunctions ---

/**
 * Gather classes and ID's from a CSS-file.
 * Create selectors with prefix (. eller #).
 * Filter away probable hex colorcodes from ID-candidates.
 * @param string $cssFilePath Path to CSS-file.
 * @return array Array with CSS-selectores (ex. ['.my-class', '#my-id']).
 */
function getCssSelectors(string $cssFilePath): array {
    $initialSelectors = []; // Temporary list for raw-collection
    if (!file_exists($cssFilePath)) {
        // Error to be showed in HTML-output later if necessary
        return $initialSelectors; // Return empty array
    }

   $cssContent = file_get_contents($cssFilePath);
    // Regex to find .class and #id definition
    // Include now . and # in the catch
    preg_match_all('/(\.[a-zA-Z0-9_-]+)|(\#[a-zA-Z0-9_-]+)/', $cssContent, $definitionMatches, PREG_SET_ORDER);

    foreach ($definitionMatches as $match) {
        if (!empty($match[1])) { // Class (ex. .my-class)
            $initialSelectors[] = trim($match[1]);
        } elseif (!empty($match[2])) { // ID (ex. #my-id or #fff)
            $initialSelectors[] = trim($match[2]);
        }
    }

    // Filter away hex color codes
    $finalSelectors = [];
    foreach ($initialSelectors as $selector) {
        if (strpos($selector, '#') === 0) {
            $potentialIdName = substr($selector, 1); // ex. "fff" or "my-id"

            // Check if it's JUST hexa (0-9, a-f, A-F, case-insencitive)
            $isPurelyHexChars = preg_match('/^[0-9a-fA-F]+$/', $potentialIdName);
            // Check if length is typical of hex colorcode (3, 4, 6, or 8 signs)
            $isTypicalHexLength = in_array(strlen($potentialIdName), [3, 4, 6, 8]);

            if ($isPurelyHexChars && $isTypicalHexLength) {
                // This is probably a hexadecimal colorcode, not an ID-selector. Skip.
                // Example: #fff, #aabbcc, #123456, #12345678
                continue;
            }
            // If it's not pure hexadecimal (ex. #my-id) ELLER
            // if it's pure hexadecimal but untypical length (ex #abcdef0 - 7 signs),
            // we will see it as potencially ID.
        }
        $finalSelectors[] = $selector;
    }

    return array_unique($finalSelectors); // Remove duplicates from the filtered list
}



// --- HTML Header ---
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Usage Analysis</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; color: #333; }
        h1, h2 { color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; background-color: #fff; box-shadow: 0 2px 3px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 10px 12px; text-align: left; }
        th { background-color: #e9e9e9; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .selector-cell { font-family: monospace; color: #007bff; }
        .filepath-cell { font-size: 0.9em; color: #555; }
        .linenum-cell { text-align: right; width: 80px; }
        .code-cell { font-family: monospace; white-space: pre-wrap; word-break: break-all; background-color: #fdfdfd; font-size:0.85em; }
        .error { color: red; font-weight: bold; }
        .info { margin-bottom: 20px; padding: 10px; background-color: #e7f3fe; border-left: 5px solid #2196F3; }
        .summary { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>CSS Usage Analysis</h1>
HTML;

if (!file_exists($cssFile)) {
    echo "<p class='error'>ERROR: CSS-file was not found: " . htmlspecialchars($cssFile) . "</p></body></html>";
    exit;
}
if (!is_dir($phpScriptsFolder)) {
    echo "<p class='error'>ERROR: Folder with PHP-script was not found: " . htmlspecialchars($phpScriptsFolder) . "</p></body></html>";
    exit;
}

// --- Main logic ---

$allCssSelectors = getCssSelectors($cssFile);

if (empty($allCssSelectors)) {
    echo "<p class='info'>No CSS-selectors (class or ID that is not colorcode) found in " . htmlspecialchars($cssFile) . ". Cannot continue analysis.</p></body></html>";
    exit;
}


echo "<div class='summary'>";
echo "<p>Analysing CSS-file: <strong>" . htmlspecialchars($cssFile) . "</strong></p>";
echo "<p>Seraching in PHP-files in folder: <strong>" . htmlspecialchars($phpScriptsFolder) . "</strong></p>";
echo "<p>Number of unique CSS-selectors (class/ID, exclusive colorcodes) found in CSS-file: <strong>" . count($allCssSelectors) . "</strong></p>";
echo "</div>";

$usedSelectorsData = []; // Will save ['selector' => [['file' => ..., 'line' => ..., 'line_content' => ...], ...]]
$unusedSelectors = $allCssSelectors; // Start with all unused

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($phpScriptsFolder, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $phpFilePath = $file->getPathname();
        $phpContentLines = file($phpFilePath, FILE_IGNORE_NEW_LINES);

        foreach ($allCssSelectors as $fullSelector) {
            // $fullSelector is ex. ".my-class" or "#my-id"
            $baseSelector = substr($fullSelector, 1); // ex. "my-class" or "my-id"
            $selectorType = substr($fullSelector, 0, 1); // ex. "." or "#"

            $pattern = '';
            if ($selectorType === '.') {
                // Search after class="... baseSelector ..." or class="baseSelector"
                $pattern = '/class\s*=\s*["\'][^"\']*?\b' . preg_quote($baseSelector, '/') . '\b[^"\']*?["\']/';
            } elseif ($selectorType === '#') {
                // Search after id="baseSelector"
                $pattern = '/id\s*=\s*["\']\s*' . preg_quote($baseSelector, '/') . '\s*["\']/';
            }

            if (empty($pattern)) continue; // shouldn't happen if getCssSelectors works

            foreach ($phpContentLines as $lineNumber => $lineContent) {
                if (preg_match($pattern, $lineContent)) {
                    if (!isset($usedSelectorsData[$fullSelector])) {
                        $usedSelectorsData[$fullSelector] = [];
                    }
                    $usedSelectorsData[$fullSelector][] = [
                        'file' => $phpFilePath,
                        'line' => $lineNumber + 1, // Linenumber start on 0
                        'line_content' => $lineContent
                    ];

                    // Remove from unused if found
                    if (($key = array_search($fullSelector, $unusedSelectors)) !== false) {
                        unset($unusedSelectors[$key]);
                    }
                }
            }
        }
    }
}

// --- Result report in HTML ---

// Table for Used Selectors
echo "<h2>Used CSS-selectors (" . count($usedSelectorsData) . " unique)</h2>";
if (empty($usedSelectorsData)) {
    echo "<p>None of the defined CSS-selectors is found in the PHP-files.</p>";
} else {
    echo "<table>";
    echo "<thead><tr><th>Selector</th><th>Filepath</th><th>Linenmb.</th><th>Line with code found</th></tr></thead>";
    echo "<tbody>";
    foreach ($usedSelectorsData as $selector => $usages) {
        $firstUsage = true;
        foreach ($usages as $usage) {
            echo "<tr>";
            if ($firstUsage) {
                echo "<td class='selector-cell' rowspan='" . count($usages) . "'>" . htmlspecialchars($selector) . "</td>";
                $firstUsage = false;
            }
            echo "<td class='filepath-cell'>" . htmlspecialchars($usage['file']) . "</td>";
            echo "<td class='linenum-cell'>" . htmlspecialchars($usage['line']) . "</td>";
            echo "<td class='code-cell'>" . htmlspecialchars(trim($usage['line_content'])) . "</td>";
            echo "</tr>";
        }
    }
    echo "</tbody></table>";
}

// Table for Unused Selectores
echo "<h2>Unused CSS-selectors (" . count($unusedSelectors) . " unique)</h2>";
if (empty($unusedSelectors)) {
    echo "<p>All defined CSS-selectores (that is not colorcodes) looks to be in use.</p>";
} else {
    echo "<table>";
    echo "<thead><tr><th>Selector</th></tr></thead>";
    echo "<tbody>";
    // Sort unused selectors for consistent viewing
    $sortedUnusedSelectors = array_values($unusedSelectors); // Re-index array after unset
    sort($sortedUnusedSelectors); // Sort alphabetically
    foreach ($sortedUnusedSelectors as $selector) {
        echo "<tr><td class='selector-cell'>" . htmlspecialchars($selector) . "</td></tr>";
    }
    echo "</tbody></table>";
}

echo "<p style='margin-top:30px; font-size:0.8em; text-align:center; color:#777;'>Rapport generated: " . date('Y-m-d H:i:s') . "</p>";
echo "</body></html>";

?>
