<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Template\Replace\PHP;

use IntoWebDevelopment\Converter\Template\Replace\Replace;

class ReplaceErrorCodes extends Replace
{

    /**
     * {@inheritDoc}
     */
    public function getPairs()
    {
        preg_match_all('/\$this->error->(code|message)[ ]*;/', $this->context, $matches);

        if (1 > count($matches)) {
            return false;
        }

        return array(
            array(
                'search'        =>  '/\$this->error->(code|message)[ ]*;/g',
                'replace'       =>  '$this->error->get' . ucfirst($matches[1] . '();'),
            ),
        );
    }
}
