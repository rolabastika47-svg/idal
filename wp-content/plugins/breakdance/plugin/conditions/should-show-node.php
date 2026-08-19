<?php

namespace Breakdance\Conditions;

add_filter('breakdance_render_show_node', '\Breakdance\Conditions\shouldShowNode', 69, 2);

/**
 *
 * @param boolean $shouldShow
 * @param array $node
 * @return boolean
 */
function shouldShowNode($shouldShow, $node)
{
    /** @var boolean $nodeIsADraft */
    $nodeIsADraft = $node['data']['properties']['settings']['advanced']['draft'] ?? false;

    /** @var bool|string $visible */
    $visible = $node['data']['properties']['settings']['conditions']['visible'] ?? true;

    if ($nodeIsADraft || $visible === false) {
        return false;
    }

    $ruleGroups = getSettingsConditions($node);

    // TODO - type safety

    if ($ruleGroups) {
        $evaluatedRuleGroups = array_map('\Breakdance\Themeless\doesRuleGroupApply', $ruleGroups);

        return in_array(true, $evaluatedRuleGroups, true);
    } else {
        return $shouldShow;
    }
}
