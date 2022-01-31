<?php
/**
 * $nodes - contains DOM selectors and data to change.
 * $actions - a list of available modules
 * $path - information about working path
 * @origin main.php
 */

if (!isset($_POST['confirm'])) {
    $default = $actions['DEFAULT'];

    $nodes['div']['innerHTML'] .=
<<<HTML
<form class="prompt" method="POST">
Are you sure you want to delete <b>"${path['base']}"</b>?<br>
<input type="submit" value="Yes" name="confirm">
<input type="submit" value="No" name="confirm">
<input type="hidden" name="${default['name']}" value="${path['relative']}">
<input type="hidden" name="action" value="$action">
</form>
HTML;
}
else {
    $nodes['div']['innerHTML'] .= '<p class="prompt"><b>';

    if ($_POST['confirm'] === 'Yes') {
        $nodes['div']['innerHTML'] .= rm($path['absolute']) ?
            'Deleted successfully.'
            :
            'Couldn\'t delete!';
    } else $nodes['div']['innerHTML'] .= 'Operation cancelled.';

    $nodes['div']['innerHTML'] .= '</b></p>';

}

// Add breadcrumbs to the top of the page
$nodes['div']['innerHTML'] = breadcrumbs() . $nodes['div']['innerHTML'];

/**
 * Deleting a file or a folder
 * @param string $path absolute path to the deletion target
 * @return bool
 */
function rm($path = '') : bool
{
    if (file_exists($path)) {
        if (is_file($path) || is_link($path)) {
            return @unlink($path);
        }
        else {
            $dir = opendir($path);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    $full = $path . '/' . $file;
                    if (is_dir($full)) {
                        rm($full);
                    } else {
                        @unlink($full);
                    }
                }
            }
            closedir($dir);
            return @rmdir($path);
        }
    }
    return false;
}