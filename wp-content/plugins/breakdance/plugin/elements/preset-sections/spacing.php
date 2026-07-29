<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;

add_action('init', function() {
    // --- "All" ---
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_all",
        controlSection('spacing_all', __('Spacing (All)', 'breakdance'), [
            responsiveControl("margin", __("Margin", 'breakdance'), ['type' => "spacing_complex", "layout" => "vertical"]),
            responsiveControl("padding", __("Padding", 'breakdance'), ['type' => "spacing_complex", "layout" => "vertical"]),
        ]),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_all_not_responsive",
        controlSection('spacing_all_not_responsive', __('Spacing (All - not responsive)', 'breakdance'), [
            control("margin", __("Margin", 'breakdance'), ['type' => "spacing_complex", "layout" => "vertical"]),
            control("padding", __("Padding", 'breakdance'), ['type' => "spacing_complex", "layout" => "vertical"]),
        ]),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_all_y",
        controlSection('spacing_all_y', __('Spacing (All Top & Bottom)', 'breakdance'), [
            responsiveControl("margin_top", __("Margin Top", 'breakdance'), ['type' => "unit"]),
            responsiveControl("margin_bottom", __("Margin Bottom", 'breakdance'), ['type' => "unit"]),
            responsiveControl("padding_top", __("Padding Top", 'breakdance'), ['type' => "unit"]),
            responsiveControl("padding_bottom", __("Padding Bottom", 'breakdance'), ['type' => "unit"]),
        ]),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_all_x",
        controlSection('spacing_all_x', __('Spacing (All Left & Right)', 'breakdance'), [
            responsiveControl("margin_left", __("Margin Left", 'breakdance'), ['type' => "unit"]),
            responsiveControl("margin_right", __("Margin Right", 'breakdance'), ['type' => "unit"]),
            responsiveControl("padding_left", __("Padding Left", 'breakdance'), ['type' => "unit"]),
            responsiveControl("padding_right", __("Padding Right", 'breakdance'), ['type' => "unit"]),
        ]),
        true
    );

    // --- Padding ---

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_padding_all",
        controlSection('spacing_padding_all', __('Padding (All)', 'breakdance'), [
            responsiveControl("padding", __("Padding", 'breakdance'), ['type' => "spacing_complex", "layout" => "vertical"]),
        ]),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_padding_y",
        controlSection('spacing_padding_y', __('Padding (Top & Bottom)', 'breakdance'), [
            responsiveControl("padding_top", __("Padding Top", 'breakdance'), ['type' => "unit"]),
            responsiveControl("padding_bottom", __("Padding Bottom", 'breakdance'), ['type' => "unit"]),
        ]),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_padding_x",
        controlSection('spacing_padding_x', __('Padding (Left & Right)', 'breakdance'), [
            responsiveControl("padding_left", __("Padding Left", 'breakdance'), ['type' => "unit"]),
            responsiveControl("padding_right", __("Padding Right", 'breakdance'), ['type' => "unit"]),
        ]),
        true
    );

    // --- Margin ---

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_margin_all",
        controlSection('spacing_margin_all', __('Margin (All)', 'breakdance'), [
            responsiveControl("margin", __("Margin", 'breakdance'), ['type' => "spacing_complex", "layout" => "vertical"]),
        ]),
        true
    );


    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_margin_y",
        controlSection('spacing_margin_y', __('Margin (Top & Bottom)', 'breakdance'), [
            responsiveControl("margin_top", __("Margin Top", 'breakdance'), ['type' => "unit"]),
            responsiveControl("margin_bottom", __("Margin Bottom", 'breakdance'), ['type' => "unit"]),
        ]),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\spacing_margin_x",
        controlSection('spacing_margin_x', __('Margin (Left & Right)', 'breakdance'), [
            responsiveControl("margin_left", __("Margin Left", 'breakdance'), ['type' => "unit"]),
            responsiveControl("margin_right", __("Margin Right", 'breakdance'), ['type' => "unit"]),
        ]),
        true
    );
});
