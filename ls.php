<?php
/**
 * $nodes - contains DOM selectors and data to change.
 * $actions - a list of available modules
 * $path - information about working path
 * @origin main.php
 * */


$table = '';
foreach(ls() as $row){
    $table .= '<tr>';

    foreach ($row as $col)
        $table .= '<td>' .
            (empty($col['GET'])?
                $col['title'] :
                "<a href=\"${col['GET']}\">${col['title']}</a>") . // If item is an action, set the link
            '</td>';

    $table .= '</tr>';
}
if(empty($table)) $table = "<tr><th>Directory is empty</th></tr>";

$nodes['div']['innerHTML'] .= breadcrumbs() . "<table>$table</table>";



/**
 * @return array a list of files properties/actions.
 * [ [ ['title' => string, 'GET' => string], ... ], ...]
 */
function ls() : array
{
    global $path, $actions;
    $ls = [];

    // Get a list of files excluding '.' and '..'
    $files = array_values(array_diff(scandir($path['absolute']),['.', '..']));

    for ($i = 0; $i < count($files); $i++) { //Iterate through the files list
        $file = getPathInfo("${path['relative']}/${files[$i]}");
        // Adding filename to the row
        $ls[$i][] = ['title' => $file['base'], 'GET' => $file['is_dir'] ? '?' . $actions['DEFAULT']['name'] ."=${file['relative']}" : ''];

        // Adding allowed actions
        foreach ($actions as $get_query => $action) {
            if ($get_query === 'DEFAULT') continue;

            // Check if action is allowed for the type of current file
            $allowed = in_array($file['is_dir'], $action['types']);

            //If action isn't allowed, column will be empty
            $ls[$i][] =
                $allowed ?
                    ['title' => $action['title'], 'GET' => "?action=$get_query&". $actions['DEFAULT']['name'] ."=${file['relative']}"] :
                    ['title' => '', 'GET' => ''];
        }
    }

    // Directories first
    usort($ls, fn ($a, $b) => $b[0]['GET'] <=> $a[0]['GET']);

    return $ls;
}