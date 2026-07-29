<?php

namespace Breakdance\DynamicData;

use function Breakdance\Singularity\Brand\getBrandSettings;

class FutureLayerSiteLogo extends ImageField
{

    public function label()
    {
        return __('Site Logo (Brand)', 'breakdance');
    }

    public function category()
    {
        return __('Site Info', 'breakdance');
    }

    public function slug()
    {
        return 'futurelayer_site_logo';
    }

    // update the const in logo.ts if you change this
    public $defaultLogoUrl = 'https://breakdancelibrary.com/barebones/wp-content/uploads/sites/50/2025/05/logoipsum-custom-logo-1.svg';

    public function handler($attributes): ImageData
    {
        $brand_settings = getBrandSettings();

        $logo_media = $brand_settings['logo']['logo'] ?? null;

        if (!$logo_media) {
            return ImageData::fromUrl($this->defaultLogoUrl);
        }

        return ImageData::fromAttachmentId($logo_media['id']);
    }
}
