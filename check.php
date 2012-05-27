<?php
/**
 * Check if the system has all the required dependencies to run this script.
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

$errors = array();

if (!class_exists("XMLWriter")) {
    $errors[] = "Please recompile PHP without the '--disable-xmlwriter' option. More information: http://php.net/manual/en/xmlwriter.installation.php";
}

if (!class_exists("SimpleXMLElement")) {
    $errors[] = "Please recompile PHP without the '--disable-simplexml' option. More information: http://php.net/manual/en/simplexml.installation.php";
}

echo implode($errors, "\n");

if (0 === count($errors)) {
    echo 'Please read doc/README before you proceed.';
}