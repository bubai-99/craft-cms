<?php

namespace mycompany\simplemenu\controllers;

use craft\web\Controller;
use craft\helpers\Json;
use mycompany\simplemenu\models\Nav;
use mycompany\simplemenu\elements\Node;
use mycompany\simplemenu\Plugin;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class NavController extends Controller
{
    public function actionIndex(): Response
    {
        $navs = Plugin::getInstance()->navService->getAllNavs();

        return $this->renderTemplate('simple-menu/navs/index', [
            'navs' => $navs,
        ]);
    }

    public function actionNew(): Response
    {
        $nav = new Nav();

        return $this->renderTemplate('simple-menu/navs/edit', [
            'nav' => $nav,
            'isNew' => true,
        ]);
    }

    public function actionEdit(int $id): Response
    {
        $nav = Plugin::getInstance()->navService->getNavById($id);
        if (!$nav) {
            throw new NotFoundHttpException('Navigation not found');
        }

        return $this->renderTemplate('simple-menu/navs/edit', [
            'nav' => $nav,
            'isNew' => false,
        ]);
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $navId = $this->request->getBodyParam('id');
        if ($navId) {
            $nav = Plugin::getInstance()->navService->getNavById((int)$navId);
            if (!$nav) {
                throw new NotFoundHttpException('Navigation not found');
            }
        } else {
            $nav = new Nav();
        }

        $nav->name = $this->request->getBodyParam('name');
        $nav->handle = $this->request->getBodyParam('handle');
        $nav->instructions = $this->request->getBodyParam('instructions');
        $nav->maxLevels = $this->request->getBodyParam('maxLevels');
        $nav->sortOrder = $this->request->getBodyParam('sortOrder', 1);

        if (Plugin::getInstance()->navService->saveNav($nav)) {
            $this->setSuccessFlash(\Craft::t('simple-menu', 'Navigation saved.'));
            
            // Handle nodes saving
            $this->saveNodes($nav);
            
            return $this->redirectToPostedUrl($nav);
        }

        $this->setFailFlash(\Craft::t('simple-menu', 'Could not save navigation.'));
        return $this->renderTemplate('simple-menu/navs/edit', [
            'nav' => $nav,
            'isNew' => !$nav->id,
        ]);
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();

        $navId = $this->request->getRequiredBodyParam('id');
        $nav = Plugin::getInstance()->navService->getNavById((int)$navId);
        
        if (!$nav) {
            throw new NotFoundHttpException('Navigation not found');
        }

        if (Plugin::getInstance()->navService->deleteNav($nav)) {
            $this->setSuccessFlash(\Craft::t('simple-menu', 'Navigation deleted.'));
        } else {
            $this->setFailFlash(\Craft::t('simple-menu', 'Could not delete navigation.'));
        }

        return $this->redirectToPostedUrl();
    }

    public function actionGetNodes(): Response
    {
        $this->requireAcceptsJson();
        
        $navId = $this->request->getRequiredParam('navId');
        $nav = Plugin::getInstance()->navService->getNavById((int)$navId);
        
        if (!$nav) {
            throw new NotFoundHttpException('Navigation not found');
        }

        $nodes = $nav->getNodes();
        
        return $this->asJson([
            'success' => true,
            'nodes' => array_map(function($node) {
                return [
                    'id' => $node->id,
                    'title' => $node->getTitle(),
                    'url' => $node->getUrl(),
                    'type' => $node->type,
                    'enabled' => $node->enabled,
                    'newWindow' => $node->newWindow,
                    'classes' => $node->classes,
                ];
            }, $nodes),
        ]);
    }

    private function saveNodes(Nav $nav): void
    {
        $nodesData = $this->request->getBodyParam('nodes', []);
        
        // Delete existing nodes
        $existingNodes = Node::find()->navId($nav->id)->all();
        foreach ($existingNodes as $node) {
            \Craft::$app->elements->deleteElement($node);
        }

        // Save new nodes
        foreach ($nodesData as $nodeData) {
            $node = new Node();
            $node->navId = $nav->id;
            $node->title = $nodeData['title'] ?? '';
            $node->type = $nodeData['type'] ?? 'custom';
            $node->customUrl = $nodeData['customUrl'] ?? '';
            $node->elementId = !empty($nodeData['elementId']) ? (int)$nodeData['elementId'] : null;
            $node->elementSiteId = !empty($nodeData['elementSiteId']) ? (int)$nodeData['elementSiteId'] : null;
            $node->classes = $nodeData['classes'] ?? '';
            $node->newWindow = !empty($nodeData['newWindow']);
            $node->enabled = !empty($nodeData['enabled']);

            \Craft::$app->elements->saveElement($node);
        }
    }
}