<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Template\Replace\PHP;

use IntoWebDevelopment\Converter\Template\Replace\Replace;

class ReplaceDefine extends Replace
{
    /**
     * {@inheritDoc}
     */
    public function getPairs()
    {
        return array(
            array(
                'search'    =>  '/defined\([ ]*[\'"]{1}_JEXEC]*[\'"]{1}[ ]*\)[ ]*or[ ]*die[ ]*\([ ]*[\'"]{1}.*[\'"]{1}[ ]*\);/',
                'replace'   =>  "defined('_JEXEC') or die;",
            ),
        );
    }
}
