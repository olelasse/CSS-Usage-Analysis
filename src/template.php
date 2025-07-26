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
    // Gjør data tilgjengelig som variabler i template-filen (f.eks. $data['title'] blir til $title)
    extract($data);
    
    ob_start();
    require $file;
    return ob_get_clean();
}
