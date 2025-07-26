<?php
/**
 * Template for the "Unused Selectors" table.
 *
 * @var array $unusedSelectors Array of selector strings that were not found.
 */

// Sorts for a neat and predictable list
sort($unusedSelectors);
?>
<h2>Unused CSS selectors (<?= count($unusedSelectors) ?> unique)</h2>

<?php if (empty($unusedSelectors)): ?>
    <p>🎉 All defined CSS selectors (that are not color codes) appear to be in use!</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Selector</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($unusedSelectors as $selector): ?>
                <tr>
                    <td class="selector-cell"><?= htmlspecialchars($selector) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
