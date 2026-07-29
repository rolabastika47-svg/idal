<?php

declare(strict_types=1);

namespace Breakdance\Lib\Vendor\League\HTMLToMarkdown;

interface PreConverterInterface
{
    public function preConvert(ElementInterface $element): void;
}
