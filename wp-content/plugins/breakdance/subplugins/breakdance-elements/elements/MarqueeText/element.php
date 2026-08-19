<?php

namespace EssentialElements;

use function Breakdance\Elements\c;
use function Breakdance\Elements\PresetSections\getPresetSection;


\Breakdance\ElementStudio\registerElementForEditing(
    "EssentialElements\\Marqueetext",
    \Breakdance\Util\getdirectoryPathRelativeToPluginFolder(__DIR__)
);

class Marqueetext extends \Breakdance\Elements\Element
{
    static function uiIcon()
    {
        return 'TextIcon';
    }

    static function tag()
    {
        return 'div';
    }

    static function tagOptions()
    {
        return [];
    }

    static function tagControlPath()
    {
        return false;
    }

    static function name()
    {
        return 'Marquee Text';
    }

    static function className()
    {
        return 'bde-marquee-text';
    }

    static function category()
    {
        return 'blocks';
    }

    static function badge()
    {
        return ['backgroundColor' => 'var(--gray300)', 'textColor' => 'var(--white-fixed)', 'label' => 'Beta'];
    }

    static function slug()
    {
        return __CLASS__;
    }

    static function template()
    {
        return file_get_contents(__DIR__ . '/html.twig');
    }

    static function defaultCss()
    {
        return file_get_contents(__DIR__ . '/default.css');
    }

    static function defaultProperties()
    {
        return false;
    }

    static function defaultChildren()
    {
        return false;
    }

    static function cssTemplate()
    {
        $template = file_get_contents(__DIR__ . '/css.twig');
        return $template;
    }

    static function designControls()
    {
        return [c(
        "marquee",
        "Marquee",
        [c(
        "direction",
        "Direction",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => [['text' => 'Left', 'value' => 'rtl'], ['value' => 'ltr', 'text' => 'Right']]],
        false,
        false,
        [],
        
      ), c(
        "shape",
        "Shape",
        [],
        ['type' => 'button_bar', 'layout' => 'inline', 'items' => [['value' => 'straight', 'text' => 'Straight'], ['text' => 'Wave', 'value' => 'wave']]],
        false,
        false,
        [],
        
      ), c(
        "wave",
        "Wave",
        [c(
        "amplitude",
        "Amplitude",
        [],
        ['type' => 'number', 'layout' => 'inline', 'unitOptions' => ['types' => ['none']], 'rangeOptions' => ['min' => 10, 'max' => 100, 'step' => 5]],
        false,
        false,
        [],
        
      ), c(
        "frequency",
        "Frequency",
        [],
        ['type' => 'number', 'layout' => 'inline', 'unitOptions' => ['types' => ['none']], 'rangeOptions' => ['min' => 1, 'max' => 10, 'step' => 1]],
        false,
        false,
        [],
        
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout'], 'condition' => [[['path' => 'design.marquee.shape', 'operand' => 'equals', 'value' => 'wave']]]],
        false,
        false,
        [],
        
      ), c(
        "speed",
        "Speed",
        [],
        ['type' => 'number', 'layout' => 'inline', 'unitOptions' => ['types' => ['none']]],
        false,
        false,
        [],
        
      ), c(
        "space_between",
        "Space Between",
        [],
        ['type' => 'number', 'layout' => 'inline', 'unitOptions' => ['types' => ['none']], 'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1]],
        false,
        false,
        [],
        
      ), c(
        "spacer",
        "Spacer",
        [],
        ['type' => 'text', 'layout' => 'inline'],
        false,
        false,
        [],
        
      ), c(
        "typography",
        "Typography",
        [c(
        "font_family",
        "Font Family",
        [],
        ['type' => 'font_family', 'layout' => 'inline'],
        false,
        false,
        [],
        
      ), c(
        "font_weight",
        "Font Weight",
        [],
        ['type' => 'dropdown', 'layout' => 'inline', 'items' => [['value' => '100', 'text' => '100'], ['text' => '200', 'value' => '200'], ['text' => '300', 'value' => '300'], ['text' => '400', 'value' => '400'], ['text' => '500', 'value' => '500'], ['text' => '600', 'value' => '600'], ['text' => '700', 'value' => '700'], ['text' => '800', 'value' => '800'], ['text' => '900', 'value' => '900']]],
        false,
        false,
        [],
        
      ), c(
        "size",
        "Size",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['px']]],
        false,
        false,
        [],
        
      ), c(
        "color",
        "Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
        
      ), c(
        "stroke_color",
        "Stroke Color",
        [],
        ['type' => 'color', 'layout' => 'inline'],
        false,
        false,
        [],
        
      ), c(
        "stroke_width",
        "Stroke Width",
        [],
        ['type' => 'number', 'layout' => 'inline', 'unitOptions' => ['types' => ['px']]],
        false,
        false,
        [],
        
      )],
        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
        false,
        false,
        [],
        
      )],
        ['type' => 'section'],
        false,
        false,
        [],
        
      ), c(
        "size",
        "Size",
        [c(
        "width",
        "Width",
        [],
        ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => []]],
        false,
        false,
        [],
        
      )],
        ['type' => 'section'],
        false,
        false,
        [],
        
      ), getPresetSection(
      "EssentialElements\\spacing_margin_y",
      "Spacing",
      "spacing",
       ['type' => 'popout']
     )];
    }

    static function contentControls()
    {
        return [c(
        "content",
        "Content",
        [c(
        "text",
        "Text",
        [],
        ['type' => 'text', 'layout' => 'vertical'],
        false,
        false,
        [],
        
      )],
        ['type' => 'section', 'layout' => 'vertical'],
        false,
        false,
        [],
        
      )];
    }

    static function settingsControls()
    {
        return [];
    }

    static function dependencies()
    {
        return ['0' =>  ['title' => 'Marquee Text JS','scripts' => ['%%BREAKDANCE_ELEMENTS_PLUGIN_URL%%dependencies-files/breakdance-marquee@1/breakdance-marquee.js'],],'1' =>  ['inlineScripts' => ['



const config = {
        text: {{ content.content.text|json_encode}},
        spacer: {{ design.marquee.spacer|json_encode}},
        spacing: {{ design.marquee.space_between|json_encode}},
        direction: {{ design.marquee.direction|json_encode}}, 
        shape: {{ design.marquee.shape|json_encode}},
        speed: {{ design.marquee.speed|json_encode}},
        fontSize: {{ design.marquee.typography.size.number|json_encode}},
        waveAmplitude: {{ design.marquee.wave.amplitude|json_encode}},
        waveFrequency: {{ design.marquee.wave.frequency|json_encode}}
    };

new BreakdanceMarqueeText(\'%%SELECTOR%%\', config)


'],'builderCondition' => 'return false;',],];
    }

    static function settings()
    {
        return ['proOnly' => false];
    }

    static function addPanelRules()
    {
        return false;
    }

    static public function actions()
    {
        return [

'onPropertyChange' => [['script' => '(function() {
  if (window.breakdanceMarqueeTextInstances && window.breakdanceMarqueeTextInstances[%%ID%%]) {
    window.breakdanceMarqueeTextInstances[%%ID%%].destroy();
  }



    const config = {
        text: {{ content.content.text|json_encode}},
        spacer: {{ design.marquee.spacer|json_encode}},
        spacing: {{ design.marquee.space_between|json_encode}},
        direction: {{ design.marquee.direction|json_encode}}, 
        shape: {{ design.marquee.shape|json_encode}},
        speed: {{ design.marquee.speed|json_encode}},
        fontSize: {{ design.marquee.typography.size.number|json_encode}},
        waveAmplitude: {{ design.marquee.wave.amplitude|json_encode}},
        waveFrequency: {{ design.marquee.wave.frequency|json_encode}}
    };

    console.log(config);


  window.breakdanceMarqueeTextInstances[%%ID%%] = new BreakdanceMarqueeText(\'%%SELECTOR%%\', config);
}());',
],],

'onMountedElement' => [['script' => '(function() {
    if (!window.breakdanceMarqueeTextInstances) window.breakdanceMarqueeTextInstances = {};

    if (window.breakdanceMarqueeTextInstances && window.breakdanceMarqueeTextInstances[%%ID%%]) {
      window.breakdanceMarqueeTextInstances[%%ID%%].destroy();
    }

    const config = {
        text: {{ content.content.text|json_encode}},
        spacer: {{ design.marquee.spacer|json_encode}},
        spacing: {{ design.marquee.space_between|json_encode}},
        direction: {{ design.marquee.direction|json_encode}}, 
        shape: {{ design.marquee.shape|json_encode}},
        speed: {{ design.marquee.speed|json_encode}},
  
        fontSize: {{ design.marquee.typography.size.number|json_encode}},
        waveAmplitude: {{ design.marquee.wave.amplitude|json_encode}},
        waveFrequency: {{ design.marquee.wave.frequency|json_encode}}
    };

    window.breakdanceMarqueeTextInstances[%%ID%%] = new BreakdanceMarqueeText(\'%%SELECTOR%%\', config);
  }());',
],],

'onMovedElement' => [['script' => '(function() {
  if (window.breakdanceMarqueeTextInstances && window.breakdanceMarqueeTextInstances[%%ID%%]) {
    window.breakdanceMarqueeTextInstances[%%ID%%].update();
  }
}());',
],],

'onBeforeDeletingElement' => [['script' => '  (function() {
    if (window.breakdanceMarqueeTextInstances && window.breakdanceMarqueeTextInstances[%%ID%%]) {
      window.breakdanceMarqueeTextInstances[%%ID%%].destroy();
      delete window.breakdanceMarqueeTextInstances[%%ID%%];
    }
  }());',
],],];
    }

    static function nestingRule()
    {
        return ['type' => 'final'];
    }

    static function spacingBars()
    {
        return [['cssProperty' => 'margin-top', 'location' => 'outside-top', 'affectedPropertyPath' => 'design.spacing.margin_top.%%BREAKPOINT%%'], ['cssProperty' => 'margin-bottom', 'location' => 'outside-bottom', 'affectedPropertyPath' => 'design.spacing.margin_bottom.%%BREAKPOINT%%']];
    }

    static function attributes()
    {
        return false;
    }

    static function experimental()
    {
        return false;
    }

    static function availableIn()
    {
        return ['breakdance'];
    }


    static function order()
    {
        return 1300;
    }

    static function dynamicPropertyPaths()
    {
        return false;
    }

    static function additionalClasses()
    {
        return false;
    }

    static function projectManagement()
    {
        return false;
    }

    static function propertyPathsToWhitelistInFlatProps()
    {
        return false;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
