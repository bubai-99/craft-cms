<?php

namespace mycompany\simplemenu\controllers;

use craft\web\Controller;
use yii\web\Response;

class SimpleController extends Controller
{
    public function actionIndex(): Response
    {
        return $this->renderTemplate('simple-menu/simple/index', [
            'title' => 'Navigation',
        ]);
    }

    public function actionNew(): Response
    {
        return $this->renderTemplate('simple-menu/simple/new', [
            'title' => 'Create Navigation',
        ]);
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();
        
        $name = $this->request->getBodyParam('name');
        $handle = $this->request->getBodyParam('handle');
        
        if (empty($name) || empty($handle)) {
            $this->setFailFlash('Name and Handle are required.');
            return $this->renderTemplate('simple-menu/simple/new');
        }
        
        $this->setSuccessFlash('Navigation saved successfully!');
        return $this->redirect('simple-menu');
    }
}