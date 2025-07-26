<?php

// 1. Initialization
require __DIR__ . '/../src/analysis.php';
require __DIR__ . '/../src/templates.php';

$config = require __DIR__ . '/../config/config.php';

$stylesheetPath = $config['paths']['stylesheet'];
$templatesPath = $config['paths']['templates'];
$templateDir = __DIR__ . '/../templates/';

// 2. Get CSS content and selectors
$cssContent = file_get_contents($stylesheetPath);
$allCssSelectors = getCssSelectors($cssContent);

// 3. Run the analysis
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($templatesPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);
$usageData = getSelectorUsage($allCssSelectors, $iterator);

// 4. Prepare data and render sections
$summarySection = render($templateDir . 'summary.php', [
    'stylesheetPath' => $stylesheetPath,
    'templatesPath' => $templatesPath,
    'selectorCount' => count($allCssSelectors)
]);

$usedSelectorsSection = render($templateDir . 'used_selectors.php', [
    'usedSelectorsData' => $usageData['used']
]);

$unusedSelectorsSection = render($templateDir . 'unused_selectors.php', [
    'unusedSelectors' => $usageData['unused']
]);

// 5. Render the main layout with all the sections
echo render($templateDir . '_layout.php', [
    'summarySection' => $summarySection,
    'usedSelectorsSection' => $usedSelectorsSection,
    'unusedSelectorsSection' => $unusedSelectorsSection,
]);
