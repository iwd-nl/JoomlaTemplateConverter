<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Exception;

class FileNotFoundException extends \Exception
{
    public function __construct($filename)
    {
        $this->message = sprintf("The requested file '%s' is not in the active path ('%s').", $filename, dirname($filename));
    }
}
