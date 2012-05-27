<?php
/**
 *
 * @author IntoWebDevelopment <hello@intowebdevelopment.nl>
 * @copyright Copyright (c) 2012, IntoWebDevelopment
 */

namespace IntoWebDevelopment\Converter\Template;

use IntoWebDevelopment\Converter\Exception\DirectoryNotFoundException;

class Template implements TemplateInterface
{
    protected $directory;
    protected $template;

    public function __construct($template = null)
    {
        if (is_null($template)) {
            return $this;
        }

        $this->setTemplate($template);
        return $this;
    }

    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    public function setTemplate($template)
    {
        $directory = realpath(dirname($template));

        if ($template instanceof \SplFileInfo) {
            $directory .= DIRECTORY_SEPARATOR . $template->getRelativePathname();
        }

        if (!is_dir($directory)) {
            throw new DirectoryNotFoundException($template, $directory);
        }

        $this->setDirectory($directory);
        $this->template = $template;
        return $this;
    }

    public function getDirectory()
    {
        return $this->directory;
    }

    public function getTemplate()
    {
        return $this->template;
    }
}
