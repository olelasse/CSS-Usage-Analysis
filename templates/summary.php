<?php
/**
 * Template for the summary section.
 *
 * @var string $stylesheetPath Path to the analyzed CSS file.
 * @var string $templatesPath  Path to the directory that was scanned.
 * @var int    $selectorCount  Total number of unique selectors found in the CSS.
 */
?>
<div class="summary">
    <p>
        <strong>Parsed stylesheet:</strong>
        <code><?= htmlspecialchars($stylesheetPath) ?></code>
    </p>
    <p>
        <strong>Searched folder for use:</strong>
        <code><?= htmlspecialchars($templatesPath) ?></code>
    </p>
    <p>
        <strong>Number of unique selectors found:</strong>
        <?= htmlspecialchars($selectorCount) ?>
    </p>
</div>
