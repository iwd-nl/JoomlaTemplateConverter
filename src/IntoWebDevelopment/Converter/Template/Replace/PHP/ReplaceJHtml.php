<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Template\Replace\PHP;

use IntoWebDevelopment\Converter\Template\Replace\Replace;

class ReplaceJHtml extends Replace
{
    /**
     * {@inheritDoc}
     */
    public function getPairs()
    {
        return array(
            array(
                'search'        =>  '/JHTML::_\([ ]*["\']{1}([a-zA-Z.]+)["\']{1}[ ]*\)[ ]*;/i',
                'replace'       =>  "JHtml::_('$1');",
            ),
        );
    }
}
