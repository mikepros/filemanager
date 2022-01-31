<?php
/**
 * $path - information about working path
 * $actions - a list of available modules
 * @origin main.php
 */

/**
 * Returns navigation panel's HTML-code
 * @return string
 */
function breadcrumbs() : string
{
    global $path, $actions;

    $breadcrumbs = '';
    $part = $path;

    // Generate breadcrumbs
    while ($part['relative'] !== '') { // if it is not a ROOT

        $breadcrumbs = ' / ' .
            (($part['is_dir']) ?
                '<a href="?' . $actions['DEFAULT']['name'] . "=${part['relative']}\">${part['base']}</a>" :
                $part['base']) . $breadcrumbs;

        $part = getPathInfo($part['dir']);
    }

    return
        '<pre> <b> <a href="?' . $actions['DEFAULT']['name'] . '=/">Home</b></a>' . // Add home link
        $breadcrumbs .
        '</pre><br>';
}
