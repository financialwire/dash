<?php

use Illuminate\Support\Facades\File;

if (!function_exists('get_avaliable_icons')) {
    function get_avaliable_icons(?string $path = null, string $sufix = 'fas', bool $icons = false)
    {
        $mediaPath = $path ?? base_path('vendor/owenvoke/blade-fontawesome/resources/svg/solid/');

        $filesInFolder = File::allFiles($mediaPath);

        $allIcons = [];

        foreach ($filesInFolder as $file) {
            $iconName = str($file->getBaseName())->remove('.svg');

            $option = "{$sufix}-{$iconName}";

            $allIcons[$option] = '';

            if ($icons) {
                $allIcons[$option] = $option;
            }
        }

        return $allIcons;
    }
}
