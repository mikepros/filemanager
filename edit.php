<?php
/**
 * $nodes - contains DOM selectors and data to change.
 * $actions - a list of available modules
 * $action - current module
 * $path - information about working path
 * @origin main.php
 */

if (isset($_POST['new_content'])) {
    $new_content = html_entity_decode($_POST['new_content']);

    $nodes['#message'] = @file_put_contents($path['absolute'], $new_content) ?             // Depends on the result
        ['innerHTML' => 'Changes saved successfully.', 'className' => 'success disappear']   // Show positive response
        :                                                       // otherwise
        ['innerHTML' => 'Edit error!', 'className' => 'error']; // negative

}

$content = htmlentities(@file_get_contents($path['absolute']));
$default = $actions['DEFAULT'];
$nodes['div']['innerHTML'] .=
breadcrumbs() .
<<<HTML
<form class="prompt" method="POST">
    <h1>New content:</h1><span id="message"></span>
    <textarea id="file_source" name='new_content'>$content</textarea><br>
    <input type='submit' value="Save"><br>
    <input type="hidden" name="${default['name']}" value="${path['relative']}">
    <input type="hidden" name="action" value="$action">
</form>
HTML;