<?php
include_once 'widgets.php';

const ROOT = '../test'; // The top level of file listing

// Set information about working path
$path = getPathInfo('');

$nodes = ['div' => ['innerHTML' => '']]; // Contains DOM selectors and data to change.

// List of available actions
$actions = [
    // Show list of directories and files. 'name' is used to generate 'href' attribute for the link
    'DEFAULT' => ['script' => 'ls.php', 'title' => 'Files list', 'name'=>'path'],
    // Delete file or directory                                  allowed:   dir   file
    'delete' => ['script' => 'delete.php', 'title' => 'Delete', 'types' => [true, false]],
    // Rename file or directory
    'rename' => ['script' => 'rename.php', 'title' => 'Rename', 'types' => [true, false]],
    // Edit file
    'edit' => ['script' => 'edit.php', 'title' => 'Edit', 'types' => [false]]
];

$action = 'DEFAULT';


// Checking validity of the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST[$actions['DEFAULT']['name']])) {
        $new_path = getPathInfo($_POST[$actions['DEFAULT']['name']]);

        // Set working directory
        if (
            strpos($new_path['absolute'], $path['absolute']) === 0 && // Is it inside the ROOT directory?
            $new_path['is_dir'] !== null // if file exists
        )
            $path = $new_path; // If everything fine, set $new_path as working directory
       
        unset($new_path); // Remove unusable variable

        // Set working module
        if (
            $path['absolute'] !== realpath(ROOT) &&                   // If working path isn't root
            isset($_POST['action']) &&                                     // And module change was requested
            array_key_exists($_POST['action'], $actions ) &&               // And requested module exists
            in_array($path['is_dir'], $actions[$_POST['action']]['types']) // And module is allowed
        )
            $action = $_POST['action']; // Set current module
    }

    include_once $actions[$action]['script']; // And call current module
}


/**
 * @param string $path
 * @return array useful information about specified path
 */
function getPathInfo(string $path) : array
{
    $info['relative'] = $path === '/' ? '' : $path;
    $info['level'] = substr_count($info['relative'], DIRECTORY_SEPARATOR);
    $info['absolute'] = realpath(ROOT.$info['relative']);
    $info['base'] = basename($info['relative']);
    $info['dir'] = dirname($info['relative']);
    if (in_array($info['dir'],['/', '.'])) $info['dir'] = '';
    $info['is_dir'] = file_exists($info['absolute']) ? is_dir($info['absolute']) : null;

    return $info;
}

// Send JSON-response
echo json_encode($nodes);

