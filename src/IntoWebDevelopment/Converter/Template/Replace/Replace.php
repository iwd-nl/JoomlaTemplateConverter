<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Template\Replace;

abstract class Replace
{
    protected $context;

    public function __construct($context = null)
    {
        $this->context = $context;
    }

    /**
     * Return the search - replace pairs.
     * Please note that // expects a regular expressen and that .* is equal to everything in the context. Other formats
     * are automaticly progressed by a str_replace call.
     *
     * @throws  \RuntimeException
     * @return  array
     */
    public function getPairs()
    {
        throw new \RuntimeException(sprintf("Make sure that '%s' returns an array", 'getPairs'));
    }
}
