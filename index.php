<?php

/**
 * streamTV DLLConverter
 *
 * Written by: djcrackhome (Sebastian Graebner) <sgraebner@my.canyons.edu>
 * License: I dedicate any and all copyright interest in this software to the public domain. I make this dedication for
 * the benefcit of the public at large and to the detriment of my heirs and successors. I intend this dedication to be
 * an overt act of relinquishment in perpetuity of all present and future rights to this software under copyright law.
 */

$DLLSource = 'http://vitalis-tempel.de/agl54.dll';

function __autoload($classname) {
    require_once('classes/'.$classname.'.php');
}

$DLLObject = new DLLConverter($DLLSource);
$DLLObject->decodeDLL();

header('Content-Type: application/json');
echo json_encode($DLLObject->getStreamTVList());
