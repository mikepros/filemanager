<?php
/**
 * $nodes - contains DOM selectors and data to change.
 * $actions - a list of available modules
 * $action - current module
 * $path - information about working path
 * @origin main.php
 */

if (!isset($_POST['rename_to'])) {
    $default = $actions['DEFAULT'];

    $nodes['div']['innerHTML'] .=

<<<HTML
<form class="prompt" method="POST">
    <label for="rename_to">New name: </label>
    <input type="text" value="${path['base']}" id="rename_to" name="rename_to">
    <input type="submit" value="Rename">
    <input type="hidden" name="${default['name']}" value="${path['relative']}">
    <input type="hidden" name="action" value="$action">
</form>
HTML;

}
else {
    $new_name =  $path['dir'] . '/' . $_POST['rename_to'];
    $success = @rename($path['absolute'], ROOT . $new_name) // Make rename
              and $path = getPathInfo($new_name); // Update working path

    $nodes['div']['innerHTML'] .= '<p class="prompt"><b>' .
        ($success ?                 // Depends on renaming result
            'Renamed successfully.' // Show positive
            :                       // or
            'Couldn\'t rename!') .  // negative response
        '</b></p>';
}

// Add breadcrumbs to the top of the page
$nodes['div']['innerHTML'] = breadcrumbs() . $nodes['div']['innerHTML'];