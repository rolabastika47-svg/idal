<?php

namespace Breakdance\DynamicData;

abstract class PostField extends StringField
{

    /**
     * @return \WP_Post|null
     */
    abstract public function getPost();

    public function controls()
    {
        return [
            \Breakdance\Elements\control('post_field', __('Post Field', 'breakdance'), [
                'type' => 'dropdown',
                'items' => [
                    ['text' => __('Post Title', 'breakdance'), 'value' => 'post_title'],
                    ['text' => __('Post Content', 'breakdance'), 'value' => 'post_content'],
                    ['text' => __('Post Excerpt', 'breakdance'), 'value' => 'post_excerpt'],
                    ['text' => __('Post Terms', 'breakdance'), 'value' => 'post_terms'],
                    ['text' => __('Post Date', 'breakdance'), 'value' => 'post_date'],
                    ['text' => __('Post Time', 'breakdance'), 'value' => 'post_time'],
                    ['text' => __('Comments Number', 'breakdance'), 'value' => 'comments_number'],
                    ['text' => __('Custom Field', 'breakdance'), 'value' => 'custom_field'],
                ],
            ]),
            \Breakdance\Elements\control('post_date_format', __('Format', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge(
                    [['text' => __('Default', 'breakdance'), 'value' => '']],
                    \Breakdance\DynamicData\get_date_formats(),
                    [['text' => __('Custom', 'breakdance'), 'value' => 'Custom'], ['text' => __('Human', 'breakdance'), 'value' => 'Human']]
                ),
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_date'
                ]
            ]),
            \Breakdance\Elements\control('post_date_format_custom', __('Custom Format', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_date_format',
                    'operand' => 'equals',
                    'value' => 'Custom'
                ]
            ]),
            \Breakdance\Elements\control('post_date_type', __('Type', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => __('Post Published', 'breakdance'), 'value' => 'published'],
                    ['text' => __('Post Modified', 'breakdance'), 'value' => 'modified'],
                ],
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_date'
                ]
            ]),
            \Breakdance\Elements\control('post_time_format', __('Format', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => array_merge(
                    [['text' => __('Default', 'breakdance'), 'value' => '']],
                    \Breakdance\DynamicData\get_time_formats(),
                    [
                        ['text' => __('Custom', 'breakdance'), 'value' => 'Custom'],
                        ['text' => __('Human', 'breakdance'), 'value' => 'Human']
                    ]
                ),
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_time'
                ]
            ]),
            \Breakdance\Elements\control('post_time_custom', __('Custom Format', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_time_format',
                    'operand' => 'equals',
                    'value' => 'Custom'
                ]
            ]),
            \Breakdance\Elements\control('post_terms_taxonomy', __('Taxonomy', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => \Breakdance\DynamicData\get_taxonomies_for_builder_post(),
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_terms'
                ]
            ]),
            \Breakdance\Elements\control('post_time_type', __('Type', 'breakdance'), [
                'type' => 'dropdown',
                'layout' => 'vertical',
                'items' => [
                    ['text' => __('Post Published', 'breakdance'), 'value' => 'published'],
                    ['text' => __('Post Modified', 'breakdance'), 'value' => 'modified'],
                ],
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_time'
                ]
            ]),
            \Breakdance\Elements\control('post_terms_link', __('Link', 'breakdance'), [
                'type' => 'toggle',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_terms'
                ]
            ]),
            \Breakdance\Elements\control('post_terms_separator', __('Separator', 'breakdance'), [
                'type' => 'text',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'post_terms'
                ]
            ]),
            \Breakdance\Elements\control('comments_number_zero', __('No comments', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'comments_number'
                ]
            ]),
            \Breakdance\Elements\control('comments_number_one', __('One comment', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'comments_number'
                ]
            ]),
            \Breakdance\Elements\control('comments_number_more', __('Multiple comments', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'comments_number'
                ]
            ]),
            \Breakdance\Elements\control('custom_field_key', __('Key', 'breakdance'), [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => [
                    'path' => 'attributes.post_field',
                    'operand' => 'equals',
                    'value' => 'custom_field'
                ]
            ]),
        ];
    }

    /**
     * @param mixed $attributes
     */
    public function handler($attributes): StringData
    {
        if (!is_array($attributes) || !array_key_exists('post_field', $attributes)) {
            return StringData::emptyString();
        }

        $value = '';
        $post = $this->getPost();
        if (!$post) {
            return StringData::emptyString();
        }

        if (isset($attributes['post_field'])) {
            $value = (string) $post->{$attributes['post_field']};
        }

        if ($attributes['post_field'] === 'post_date') {
            $value = $this->getPostDateValue($post, $attributes);
        }

        if ($attributes['post_field'] === 'post_time') {
            $value = $this->getPostTimeValue($post, $attributes);
        }

        if ($attributes['post_field'] === 'post_terms') {
            $value = $this->getPostTermsValue($post, $attributes);
        }

        if ($attributes['post_field'] === 'comments_number') {
            $value = (string) get_comments_number_text(
                (string) ($attributes['comments_number_zero'] ?? __('No comments', 'breakdance')),
                (string) ($attributes['comments_number_one'] ?? __('One comment', 'breakdance')),
                (string) ($attributes['comments_number_more'] ?? __('% comment', 'breakdance')),
                $post->ID
            );
        }

        if ($attributes['post_field'] === 'custom_field') {
            if (!array_key_exists('custom_field_key', $attributes) || !is_string($attributes['custom_field_key'])) {
                return StringData::emptyString();
            }
            $value = (string) get_post_meta($post->ID, $attributes['custom_field_key'], true);
        }

        return StringData::fromString($value);
    }


    /**
     * @param \WP_Post $post
     * @param mixed $attributes
     * @return string
     */
    protected function getPostDateValue($post, $attributes)
    {
        if (!is_array($attributes)) {
            return '';
        }
        $postDateType = (string) ($attributes['post_date_type'] ?? 'modified');
        $format = (string) ($attributes['post_date_format'] ?? '');
        if ($format === 'Custom') {
            $format = (string) ($attributes['post_date_format_custom'] ?? '');
        }
        if ($postDateType === 'modified') {
            if ($format === 'Human') {
                return human_time_diff((int) get_the_modified_date('U', $post));
            }
            return (string) apply_filters('the_modified_date', get_the_modified_date($format, $post));
        }
        if ($format === 'Human') {
            return human_time_diff((int) get_the_date('U', $post));
        }
        return (string) apply_filters('the_date', get_the_date($format, $post));
    }

    /**
     * @param \WP_Post $post
     * @param mixed $attributes
     * @return string
     */
    protected function getPostTimeValue($post, $attributes)
    {
        if (!is_array($attributes)) {
            return '';
        }
        $format = (string) ($attributes['post_time_format'] ?? '');
        if ($format === 'Custom') {
            $format = (string) ($attributes['post_time_custom'] ?? '');
        }
        $postTimeType = (string) ($attributes['post_time_type'] ?? 'modified');
        if ($postTimeType === 'published') {
            if ($format === 'Human') {
                return (string) human_time_diff((int) get_the_time('U', $post));
            }
            return (string) apply_filters('the_time', get_the_time((string) $format, $post));
        }
        if ($format === 'Human') {
            return (string) human_time_diff((int) get_the_modified_time('U', $post));
        }
        return (string) apply_filters('the_modified_time', get_the_modified_time((string) $format, $post));
    }

    /**
     * @param \WP_Post $post
     * @param mixed $attributes
     * @return string
     */
    protected function getPostTermsValue($post, $attributes)
    {
        if (!is_array($attributes) || !array_key_exists('post_terms_taxonomy', $attributes)) {
            return '';
        }
        $separator = (string) ($attributes['post_terms_separator'] ?? ', ');
        $terms = get_the_term_list($post->ID, (string) $attributes['post_terms_taxonomy'], '', $separator);

        // a similar check is duplicated a few times
        if (is_wp_error($terms)) {
            /** @var \WP_Error $terms */
            $message = "WP_Error: " . $terms->get_error_message(); // should we actually get all the error messages and join them?
            return $message;
        }

        if (!is_string($terms)) {
            return '';
        }

        $link = filter_var($attributes['post_terms_link'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if ($link) {
            return $terms;
        }
        return (string) strip_tags($terms);
    }
}
