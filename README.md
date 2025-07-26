# CSS Usage Analysis

A PHP script to analyze which CSS classes and IDs from a stylesheet are used in a project's template files, and which are not. This helps in cleaning up unused CSS to reduce file size and improve maintainability.

## Requirements
* PHP 8.1 or newer.
* A local web server (like Apache, Nginx, or using PHP's built-in server).

## Installation & Setup

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/olelasse/CSS-Usage-Analysis.git](https://github.com/olelasse/CSS-Usage-Analysis.git)
    cd CSS-Usage-Analysis
    ```

2.  **Create your configuration file:**
    Copy the sample configuration file.
    ```bash
    cp config/config.php.sample config/config.php
    ```

3.  **Edit `config/config.php`:**
    Open `config/config.php` and update the paths to point to your project's folders.
    * `stylesheets`: Path to the CSS file you want to analyze.
    * `templates`: Path to the folder containing your PHP/HTML template files where the CSS selectors are used.

## Usage

1.  Point your web server's document root to the `/public` directory of this project.
2.  Open your browser and navigate to the project's URL (e.g., `http://localhost/`).
3.  The analysis report will be generated and displayed automatically.
