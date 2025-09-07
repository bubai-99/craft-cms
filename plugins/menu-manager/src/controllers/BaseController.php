<?php
namespace mycompany\menumanager\controllers;

use mycompany\menumanager\MenuManager;
use mycompany\menumanager\migrations\AmNavPlugin;
use mycompany\menumanager\migrations\NaveePlugin;
use mycompany\menumanager\models\Settings;

use Craft;
use craft\web\Controller;

use yii\web\Response;

class BaseController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionSettings(): Response
    {
        /* @var Settings $settings */
        $settings = MenuManager::$plugin->getSettings();

        return $this->renderTemplate('menu-manager/settings', [
            'settings' => $settings,
        ]);
    }

}