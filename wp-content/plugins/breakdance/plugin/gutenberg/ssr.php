<?php

namespace Breakdance\Gutenberg;

use function Breakdance\Data\get_tree;
use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\isRequestFromGutenbergIframe;

/**
 * @param array $classes
 * @return array
 */
function bodyClasses($classes)
{
  if (!isRequestFromBuilderIframe() && !isRequestFromGutenbergIframe()) {
    return $classes;
  }

  $postId = get_the_ID();
  $tree = get_tree((int) $postId);

  if ($tree === false) {
    $classes[] = 'is-breakdance-block-empty';
  }

  return $classes;
}

add_filter('body_class', '\Breakdance\Gutenberg\bodyClasses');
