<?php

declare(strict_types=1);

namespace Breakdance\Lib\Vendor\League\HTMLToMarkdown\Converter;

use Breakdance\Lib\Vendor\League\HTMLToMarkdown\ElementInterface;

interface ConverterInterface
{
    public function convert(ElementInterface $element): string;

    /**
     * @return string[]
     */
    public function getSupportedTags(): array;
}
