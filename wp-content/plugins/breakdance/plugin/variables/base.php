<?php

require_once __DIR__ . "/variables.php";
require_once __DIR__ . "/controls.php";
require_once __DIR__ . "/save.php";

/** @psalm-suppress UndefinedConstant */
if (BREAKDANCE_MODE == 'breakdance') {
    add_filter(
        '__breakdance_variables_css_stylesheet_tag',
        function () {
            return '';
        }
    );
}
