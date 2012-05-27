<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Template;

interface TemplateInterface
{
    function setDirectory($directory);
    function getDirectory();
    function setTemplate($template);
    function getTemplate();
}
