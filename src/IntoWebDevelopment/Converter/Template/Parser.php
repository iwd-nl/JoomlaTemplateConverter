<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Template;

use IntoWebDevelopment\Converter\Exception\FileNotFoundException;
use IntoWebDevelopment\Converter\Template\Replace\Replace;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Parser implements ParserInterface
{
    protected $directory;

    /**
     * {@inheritDoc}
     */
    public function parse(TemplateInterface $template)
    {
        if (is_null($template->getDirectory()) || is_null($template->getTemplate())) {
            throw new \UnexpectedValueException('Please supply the directory and template before parsing.');
        }

        $filesystem = new Filesystem();
        $ds = DIRECTORY_SEPARATOR;

        // Before mirroring we're going to check if the template already is converted to Joomla 2.5
        if (0 < count(Finder::create()->files()->name('.converted')->depth(0)->in($template->getDirectory())) && !defined('USE_FORCE')) {
            throw new \RuntimeException('The template is already converted. Please see: doc/README');
        }

        $this->directory = $template->getDirectory();
        $filesystem->mirror($template->getDirectory(), $template->getDirectory() . $ds . "..{$ds}..{$ds}backup" . $ds . $template->getTemplate());

        $this->parsePHP(
            Finder::create()->files()->name("*.php")->depth(0)->in($template->getDirectory()),
            Finder::create()->files()->name("*.php")->depth(0)->in(__DIR__ . $ds . 'Replace' . $ds . 'PHP')
        );
        $this->parseXML(
            Finder::create()->files()->name("*.xml")->depth(0)->in($template->getDirectory()),
            Finder::create()->files()->name("*.php")->depth(0)->in(__DIR__ . $ds . 'Replace' . $ds . 'XML')
        );

        touch($template->getDirectory() . '/.converted');
        return true;
    }

    /**
     * Parse the content of the PHP files.
     * Replace the content from the files defined in the ``IntoWebDevelopment\Converter\Template\Replace\PHP`` namespace
     *
     * @param   Finder  $files
     * @param   Finder  $replaceClasses
     * @throws  \RuntimeException
     * @return  Parser
     */
    protected function parsePHP(Finder $files, Finder $replaceClasses)
    {
        /**
         * @var \SplFileInfo    $file
         */
        foreach ($files as $file) {
            if (!$file->isWritable()) {
                throw new \RuntimeException(sprintf("Unable to write to '%s'", $file->getRealPath()));
            }

            $content = file_get_contents($file->getRealPath());

            foreach ($replaceClasses as $replaceClass) {
                $token = $this->getClassName(file_get_contents($replaceClass));

                if (false === $token) {
                    continue;
                }

                $this->replaceContent($content, new $token($content));
            }

            if (false === file_put_contents($file->getRealPath(), $content, LOCK_EX)) {
                throw new \RuntimeException(sprintf("An error occured while writing to '%s'", $file->getRealPath()));
            }
        }

        return $this;
    }

    protected function parseXML(Finder $files, Finder $replaceClasses)
    {
        /**
         * @var \SplFileInfo    $file
         */
        foreach ($files as $file) {
            if (!$file->isWritable()) {
                throw new \RuntimeException(sprintf("Unable to write to '%s'", $file->getRealPath()));
            }

            $content = file_get_contents($file->getRealPath());
            $this->createXmlDocument($content);

            foreach ($replaceClasses as $replaceClass) {
                $token = $this->getClassName(file_get_contents($replaceClass));

                if (false === $token) {
                    continue;
                }

                $this->replaceContent($content, new $token($content));
            }

            if (false === file_put_contents($file->getRealPath(), $content, LOCK_EX)) {
                throw new \RuntimeException(sprintf("An error occured while writing to '%s'", $file->getRealPath()));
            }
        }
    }

    /**
     * Iterates over the $replace classes and changes the $content variable.
     *
     * @param   string  $content
     * @param   Replace\Replace    $replace
     * @return  string
     */
    private function replaceContent(&$content, Replace $replace)
    {
        $pairs = $replace->getPairs();

        if (false === $pairs) {
            return $content;
        }

        for ($i = 0; $i < count($pairs); $i++) {
            if ('/' === substr($pairs[$i]['search'], 0, 1) && '/' === substr($pairs[$i]['search'], -1, 1)) {
                // We're dealing with a regular expression here.
                $content = preg_replace($pairs[$i]['search'], $pairs[$i]['replace'], $content);
                continue;
            } elseif ('.*' === $pairs[$i]['search']) {
                // Clean up the old content and replace it with the new value.
                $content = $pairs[$i]['replace'];
                continue;
            }

            $content = str_replace($pairs[$i]['search'], $pairs[$i]['replace'], $content);
        }

        return $content;
    }

    /**
     * Create a new XML document based on the $content variable.
     *
     * @param   string  $content
     * @throws  \ErrorException
     * @return  Boolean|string
     */
    private function createXmlDocument(&$content)
    {
        libxml_use_internal_errors(true);
        $xmlContent = simplexml_load_string($content);
        $document = new \XMLWriter();

        if (!$xmlContent) {
            throw new \ErrorException('The supplied content is not an XML document');
        }

        if (!isset($xmlContent->children()->name)) {
            return false;
        }

        $document->openMemory();
        $document->startDocument('1.0', 'UTF-8');
        $document->setIndent(true);
        $document->setIndentString('    ');
        $document->writeDtd(sprintf(
            'extension PUBLIC "-//Joomla! %s//DTD template 1.0//EN" "https://intowebdevelopment.nl/dtd/joomla/%s/template-install.dtd"',
            DTD_JOOMLA_VERSION,
            DTD_JOOMLA_VERSION
        ));
        $document->startElement('extension');
        $document->writeAttribute('version', TARGET_JOOMLA_VERSION);
        $document->writeAttribute('type', 'template');
        $document->writeAttribute('client', TARGET_JOOMLA_TEMPLATE);
        $document->writeAttribute('method', 'upgrade');
        $document->writeElement('name', (string) $xmlContent->children()->name);
        $document->writeElement('creationDate', (string) $xmlContent->children()->creationDate ?: date('Y-m-d'));
        $document->writeElement('author', (string) $xmlContent->children()->author);
        $document->writeElement('authorEmail', (string) $xmlContent->children()->authorEmail);
        $document->writeElement('authorUrl', (string) $xmlContent->children()->authorUrl);
        $document->writeElement('version', (string) $xmlContent->children()->version);
        $document->writeElement('license', (string) $xmlContent->children()->license);
        $document->startElement('description');
        $document->writeCdata((string) $xmlContent->children()->description);
        $document->endElement();
        $document->startElement('files');
        $contains_templateDetails = false;

        foreach ($xmlContent->children()->files->children() as $file) {
            if (!is_file($this->directory . DIRECTORY_SEPARATOR . (string) $file)) {
                throw new FileNotFoundException((string) $file);
            }

            $contains_templateDetails = false !== strpos((string) $file, 'templateDetails.xml');
            $document->writeElement($file->getName(), (string) $file);
        }

        if (!$contains_templateDetails) {
            $document->writeElement('filename', 'templateDetails.xml');
        }

        $document->endElement();
        $document->startElement('positions');

        foreach ($xmlContent->children()->positions->children() as $position) {
            $document->writeElement('position', (string) $position);
        }

        $document->endElement();

        if (isset($xmlContent->children()->languages)) {
            $document->startElement('languages');

            foreach ($xmlContent->children()->languages->children() as $language) {
                $document->startElement('language');
                $document->writeAttribute('tag', (string) $language->attributes()->tag);
                $document->text((string) $language);
                $document->endElement();
            }

            $document->endElement();
        }

        if (isset($xmlContent->children()->params)) {
            $document->startElement('config');
            $document->startElement('fields');
            $document->writeAttribute('name', 'params');

            if (($addPath = $xmlContent->children()->params->attributes()->addpath) && isset($addPath)) {
                $document->writeAttribute('addfieldpath', (string) $addPath);
            }

            $document->startElement('fieldset');
            $document->writeAttribute('name', 'advanced');

            foreach ($xmlContent->children()->params->children() as $param) {
                $document->startElement('field');
                $document->writeAttribute('name', (string) $param->attributes()->name);
                $document->writeAttribute('type', (string) $param->attributes()->type);
                $document->writeAttribute('default', (string) $param->attributes()->default);
                $document->writeAttribute('label', (string) $param->attributes()->label);
                $document->writeAttribute('description', (string) $param->attributes()->description);

                if (0 < $param->count()) {
                    foreach ($param->children() as $option) {
                        $document->startElement('option');
                        $document->writeAttribute('value', (string) $option->attributes()->value);
                        $document->text((string) $option);
                        $document->endElement();
                    }
                }

                $document->endElement();
            }

            $document->endElement();
            $document->endElement();
            $document->endElement();
        }

        $document->endElement();
        return $content = $document->outputMemory(true);
    }

    /**
     * Get the class name out of the filename contents by using the Tokenizer class.
     *
     * @param   string  $filenameContents
     * @return  Boolean|string
     */
    private function getClassName($filenameContents)
    {
        $tokens = token_get_all($filenameContents);

        $return = '';
        $nextToken = null;

        for ($i = 0; $i < count($tokens); $i++) {
            if (is_string($tokens[$i][0])) {
                continue;
            }

            if (T_NAMESPACE === $tokens[$i][0]) {
                $nextToken = T_WHITESPACE;
            }

            if ($nextToken === $tokens[$i][0] || (T_NS_SEPARATOR === $tokens[$i][0] && T_STRING === $nextToken)) {
                if ("class" === $tokens[$i][1]) {
                    $tokens[$i][1] = '\\';
                }

                $return .= $tokens[$i][1];
                $nextToken = T_STRING;
            }

            if (T_USE === $tokens[$i][0]) {
                $nextToken = T_CLASS;
            }

            if (isset($tokens[$i + 1][0]) && T_EXTENDS === $tokens[$i + 1][0]) {
                break;
            }
        }

        return trim($return) ?: false;
    }
}
