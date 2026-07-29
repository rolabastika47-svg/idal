<?php

namespace Breakdance\DynamicData;

class PHPField extends Field
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __('PHP', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return __('Utility', 'breakdance');
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'phpreturn';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control(
                'code',
                __('Code', 'breakdance'),
                [
                    'placeholder' => 'return $value;',
                    'codeOptions' => ['language' => 'php', 'autofillOnEmpty' => '$value = "example";
return $value;PLACECURSORHERE'],
                    'type' => "code",
                    'layout' => 'vertical'
                ]
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['image_url', 'string'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): FieldData
    {
        $code = $attributes['code'] ?? "return '';";

        try {
            $result = eval($code);
        } catch (\ParseError $e) {
            ob_start();
            echo __('An error occurred:', 'breakdance');
            echo '<br />';
            /* translators: %s: Exception message */
            echo sprintf(__('Caught exception: %s', 'breakdance'), $e->getMessage()) . "\n";
            /* translators: %s: Line number */
            echo sprintf(__('Line: %s', 'breakdance'), $e->getLine());
            echo "<br />";
            $result = ob_get_clean();
        }

        if (is_null($result)) {
            $result = '';
        }

        return StringData::fromString($result);
    }
}
