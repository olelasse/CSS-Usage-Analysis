<?php
/**
 * Template for the "Used Selectors" table.
 *
 * @var array $usedSelectorsData Associative array with used selectors and their usage locations.
 */
?>
<h2>Used CSS selectors (<?= count($usedSelectorsData) ?> unique)</h2>

<?php if (empty($usedSelectorsData)): ?>
    <p>None of the defined CSS selectors were found in the project files.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Selector</th>
                <th>Filepath</th>
                <th>Line no.</th>
                <th>Line of code</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usedSelectorsData as $selector => $usages): ?>
                <?php $firstUsage = true; ?>
                <?php foreach ($usages as $usage): ?>
                    <tr>
                        <?php if ($firstUsage): ?>
                            <td class="selector-cell" rowspan="<?= count($usages) ?>">
                                <?= htmlspecialchars($selector) ?>
                            </td>
                            <?php $firstUsage = false; ?>
                        <?php endif; ?>

                        <td class="filepath-cell"><?= htmlspecialchars($usage['file']) ?></td>
                        <td class="linenum-cell"><?= htmlspecialchars($usage['line']) ?></td>
                        <td class="code-cell"><?= htmlspecialchars(trim($usage['line_content'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
