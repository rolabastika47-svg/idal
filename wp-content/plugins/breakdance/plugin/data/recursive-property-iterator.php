<?php

namespace Breakdance\Data;

use function Breakdance\Config\Breakpoints\get_breakpoints;

class RecursivePropertyIterator extends \RecursiveArrayIterator
{
    public const IGNORED_KEYS = [
        'ruleDynamicMeta', // Conditions
        'dynamicMeta', // Dynamic meta
        'globalSettings', // Global settings loaded within the element's context
        'variables' // Oxygen variables do not need to be iterated
    ];

    public function shouldPropertyKeyBeIgnored(string $propertyKey): bool {
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return str_ends_with($propertyKey, '_dynamic_meta') ||
               in_array($propertyKey, self::IGNORED_KEYS);
    }

    public function hasChildren(): bool
    {
        /** @var mixed $property */
        $property = $this->current();
        if (!is_array($property)) {
            return false;
        }

        // If this is an array with number, style, unit keys we don't need to descend
        if (isset($property['number'], $property['style'], $property['unit'])) {
            return false;
        }

        $key = (string) $this->key();

        if ($this->shouldPropertyKeyBeIgnored($key)) {
            return false;
        }

        return true;
    }
}
