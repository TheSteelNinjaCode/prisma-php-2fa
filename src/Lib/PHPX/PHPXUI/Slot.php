<?php

namespace Lib\PHPX\PHPXUI;

use Lib\PHPX\PHPX;
use Lib\PHPX\TwMerge;
use DOMElement;
use Lib\PHPX\TemplateCompiler;

class Slot extends PHPX
{
    public function __construct(array $props = [])
    {
        parent::__construct($props);
    }

    private function processChildNodes(string $children, string $class): string
    {
        $dom = TemplateCompiler::convertToXml($children);

        $root = $dom->documentElement;
        $updatedContent = [];
        $firstElementFound = false;

        // Iterate over child nodes of the root element
        foreach ($root->childNodes as $node) {
            if (!$firstElementFound && $node instanceof DOMElement) {
                $firstElementFound = true;

                // Modify the class attribute of the first element node
                $existingClass = $node->getAttribute("class");
                $mergeClass = TwMerge::mergeClasses($class, $existingClass);
                $node->setAttribute("class", $mergeClass);

                // Preserve other attributes dynamically
                foreach ($this->props as $key => $value) {
                    if ($key !== 'class' && $key !== 'asChild' && $key !== 'children' && is_string($value)) {
                        $node->setAttribute($key, $value);
                    }
                }
            }
            $updatedContent[] = $dom->saveXML($node);
        }

        return implode('', $updatedContent);
    }

    public function render(): string
    {
        $attributes = $this->getAttributes();
        $class = $this->getMergeClasses();
        $asChild = $this->props['asChild'] ?? false;

        if ($asChild) {
            return $this->processChildNodes($this->children, $class);
        }

        return <<<HTML
        <div class="$class" $attributes>{$this->children}</div>
        HTML;
    }
}
