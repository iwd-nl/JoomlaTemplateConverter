<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Exception;

class DirectoryNotFoundException extends \Exception
{
    public function __construct($directoryName, $targetDir = '')
    {
        $this->message = sprintf("Cannot find '%s' inside '%s' (target directory)", $directoryName, $targetDir);
    }
}
