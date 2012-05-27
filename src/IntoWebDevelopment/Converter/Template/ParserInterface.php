<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Template;

interface ParserInterface
{
    /**
     * Extract the template from the Template class.
     *
     * @abstract
     * @param   TemplateInterface   $template
     * @return  Boolean
     */
    function parse(TemplateInterface $template);
}