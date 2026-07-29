<?php

namespace Breakdance\DynamicData;

$futurelayerFields = [
    new FutureLayerSiteLogo(),
];

foreach ($futurelayerFields as $field) {
    DynamicDataController::getInstance()->registerField($field);
}
