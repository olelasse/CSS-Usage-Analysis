<?php
// src/templates.php

/**
 * Renders a template file.
 *
 * @param string $file Path to the template file.
 * @param array $data Data to be extracted into variables for the template.
 * @return string The rendered HTML.
 */
function render(string $file, array $data = []): string
{
    // Make data available as variables in the template file (e.g. $data['title'] becomes $title)
    extract($data);
    
    ob_start();
    require $file;
    return ob_get_clean();
}
