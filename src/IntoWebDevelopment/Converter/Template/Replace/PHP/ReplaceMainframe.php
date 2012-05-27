<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Template\Replace\PHP;

use IntoWebDevelopment\Converter\Template\Replace\Replace;

class ReplaceMainframe extends Replace
{
    /**
     * {@inheritDoc}
     */
    public function getPairs()
    {
        if (false !== strpos($this->context, '$mainframe')) {
            $output = '';
            $startTagOccurred = false;
            $tokens = token_get_all($this->context);

            for ($i = 0; $i < count($tokens); $i++) {
                if (T_OPEN_TAG === $tokens[$i][0] && !$startTagOccurred) {
                    $output .= '<?php' . PHP_EOL . PHP_EOL;
                    $output .= <<<EOT
/**
 * The \$app variable is a replacement for the \$mainframe.
 * Please see http://docs.joomla.org/JFactory::getApplication/1.6 for more information.
 */
\$app = &JFactory::getApplication();
EOT;
;
                    $output .= PHP_EOL . PHP_EOL;
                    $startTagOccurred = true;
                } else {
                    $output .= (is_string($tokens[$i])) ? $tokens[$i] : $tokens[$i][1];
                }
            }

            return array(
                array(
                    'search'    =>  '.*',
                    'replace'   =>  $output,
                ), array(
                    'search'    =>  '$mainframe->',
                    'replace'   =>  '$app->',
                ),
            );
        }

        return false;
    }
}
