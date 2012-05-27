<?php
/**
 * Connects all the components (including autoloading), thanks Symfony team!
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

require_once __DIR__ . '/lib/vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require_once __DIR__ . '/src/IntoWebDevelopment/Converter/config.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->register();
$loader->registerNamespaces(array(
    'IntoWebDevelopment'    =>  __DIR__ . '/src/',
    'Symfony'               =>  __DIR__ . '/lib/vendor/'
));

use Symfony\Component\Finder\Finder;
use IntoWebDevelopment\Converter\Template\Parser;
use IntoWebDevelopment\Converter\Template\Template;

$templates = Finder::create()->directories()->exclude('backup')->depth(0)->in('src' . DIRECTORY_SEPARATOR . 'Template');
$templates_zip = Finder::create()->files()->name('*.zip')->depth(0)->in('src' . DIRECTORY_SEPARATOR . 'Template');
$zipClassAvailable = class_exists('ZipArchive');
$names = array();

if (0 === count($templates) && 0 !== count($templates_zip)) {
    if (!$zipClassAvailable) {
        throw new RuntimeException('We found a couple of templates packed inside a ZIP file. We cannot extract these files without --enable-zip');
    }

    /**
     * @var $template_zip   SplFileInfo
     */
    foreach ($templates_zip as $template_zip) {
        $zip = new ZipArchive();

        if (false !== $zip->open($template_zip)) {
            $zip->extractTo('src' . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR . $template_zip->getBasename('.zip'));
            $zip->close();

            @unlink($template_zip);
        } else {
            throw new ErrorException(sprintf("The supplied ZIP file '%s' cannot be read.", $template_zip));
        }
    }

    $templates = Finder::create()->directories()->exclude('backup')->depth(0)->in('src' . DIRECTORY_SEPARATOR . 'Template');
}

/**
 * @var $template   SplFileInfo
 */
foreach ($templates as $template) {
    $names[] = $template->getFilename();
}

if (0 === count($templates)) {
    throw new RuntimeException(sprintf("Please copy a template into '%s' to continue.", 'src' . DIRECTORY_SEPARATOR . 'Template'));
}

foreach ($templates as $template) {
    $parser = new Parser();
    $parser->parse(new Template($template));
}

echo sprintf("All templates ('%s') are successfully migrated to Joomla 2.5. Please double check the results before uploading this.\n", implode(', ', $names));
echo "When you think it's okay then go ahead and archive the contents from src" . DIRECTORY_SEPARATOR . "Templates into a ZIP-file.";