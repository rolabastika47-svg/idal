<?php

declare(strict_types=1);

namespace Breakdance\Lib\Vendor\League\HTMLToMarkdown;

interface ConfigurationAwareInterface
{
    public function setConfig(Configuration $config): void;
}
