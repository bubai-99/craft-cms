<?php
namespace mycompany\menumanager\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class NavigationAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@mycompany/menumanager/resources/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/navigation.css',
        ];

        $this->js = [
            'js/navigation.js',
        ];

        parent::init();
    }
}
