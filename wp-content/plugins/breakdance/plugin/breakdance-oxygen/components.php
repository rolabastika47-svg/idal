<?php
// @psalm-ignore-file

namespace Breakdance\Components;

/**
 * @psalm-type ComponentTarget = array{nodeId: int, propertyKey: string, controlPath: string}
 * @psalm-type ComponentData = array{componentId: int, targets: ComponentTarget[], properties: array<string, mixed>}
 * @psalm-type EditableProperty = array{enabled?: bool, label?: string, controlPath?: string, propertyKey?: string}
 */

class ComponentInputValueHolder
{
    use \Breakdance\Singleton;

    /** @var array<int, ComponentData> */
    private static $componentStack = [];

    /**
     * @param ComponentData $component
     */
    public static function setCurrentComponent($component): void {
        self::$componentStack[] = $component;
        add_filter('breakdance_before_render_node', '\Breakdance\Components\replaceNodePropertiesWithEditedPropertiesFromComponent');
    }

    public static function resetComponent(): void {
        array_pop(self::$componentStack);

        if (empty(self::$componentStack)) {
            remove_filter('breakdance_before_render_node', '\Breakdance\Components\replaceNodePropertiesWithEditedPropertiesFromComponent');
        }
    }

    /**
     * @return ComponentData|null
     */
    public static function getCurrentComponent() {
        if (empty(self::$componentStack)) {
            return null;
        }

        return self::$componentStack[array_key_last(self::$componentStack)];
    }
}

/**
 * @param int $nodeId
 * @return ComponentTarget[]
 */
function getTargetsForNode($nodeId)
{
    $component = ComponentInputValueHolder::getCurrentComponent();

    if ($component === null) {
        return [];
    }

    $targets = $component['targets'] ?? [];

    return array_filter($targets, function ($target) use ($nodeId) {
        return $target['nodeId'] === $nodeId;
    });
}

/**
 * @param TreeNode $node
 * @param ComponentTarget $target
 * @return EditableProperty|null
 */
function getEditablePropertyForNode($node, $target)
{
    /** @psalm-var EditableProperty[] $editableProperties */
    $editableProperties = $node['data']['properties']['meta']['component']['editableProperties'] ?? [];

    $filtered = array_filter($editableProperties, function (array $editableProperty) use ($target) {
        return isset($editableProperty['propertyKey']) && $editableProperty['propertyKey'] === $target['propertyKey'];
    });
    /** @psalm-var EditableProperty|false $result */
    $result = reset($filtered);
    return $result !== false ? $result : null;
}

/**
 * @param EditableProperty|null $editableProperty
 * @return bool
 */
function isEditablePropertyEnabled($editableProperty)
{
    if ($editableProperty === null) return false;
    return isset($editableProperty['enabled']) ? (bool)$editableProperty['enabled'] : true;
}

/**
 * @param TreeNode $node
 * @return TreeNode
 */
function replaceNodePropertiesWithEditedPropertiesFromComponent($node)
{
    $targetPropertiesForNode = getTargetsForNode($node['id']);

    $component = ComponentInputValueHolder::getCurrentComponent();

    if ($component === null) {
        return $node;
    }

    $properties = $component['properties'] ?? [];

    foreach ($targetPropertiesForNode as $target) {
        /** @var mixed $propertyValueInComponent */
        $propertyValueInComponent = $properties[$target['propertyKey']] ?? null;
        /** use of isset - explanation is https://github.com/soflyy/breakdance/issues/6707 */
        if (isset($propertyValueInComponent)) {
            $editableProperty = getEditablePropertyForNode($node, $target);

            if (isEditablePropertyEnabled($editableProperty)) {
                assignArrayByPath($node['data']['properties'], $target['controlPath'], $propertyValueInComponent);
            }
        }
    }

    return $node;
}
