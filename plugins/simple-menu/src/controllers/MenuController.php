<?php

namespace mycompany\simplemenu\controllers;

use craft\web\Controller;
use mycompany\simplemenu\models\Menu;
use mycompany\simplemenu\Plugin;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MenuController extends Controller
{
    public function actionIndex(): Response
    {
        $menus = Plugin::getInstance()->menuService->getAllMenus();

        return $this->renderTemplate('simple-menu/_index.twig', [
            'menus' => $menus,
        ]);
    }

    public function actionNew(): Response
    {
        $menu = new Menu();

        return $this->renderTemplate('simple-menu/_edit_nav_style.twig', [
            'menu' => $menu,
            'isNew' => true,
        ]);
    }

    public function actionEdit(int $id): Response
    {
        $menu = Plugin::getInstance()->menuService->getMenuById($id);
        if (!$menu) {
            throw new NotFoundHttpException('Menu not found');
        }

        // Use Verbb-style template
        return $this->renderTemplate('simple-menu/_edit_nav_style.twig', [
            'menu' => $menu,
            'isNew' => false,
        ]);
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $menuId = $this->request->getBodyParam('id');
        if ($menuId) {
            $menu = Plugin::getInstance()->menuService->getMenuById((int)$menuId);
            if (!$menu) {
                throw new NotFoundHttpException('Menu not found');
            }
        } else {
            $menu = new Menu();
        }

        $menu->name = $this->request->getBodyParam('name');
        $menu->handle = $this->request->getBodyParam('handle');
        $menu->maxLevels = $this->request->getBodyParam('maxLevels');
        
        // Legacy support for items
        $menu->items = $this->request->getBodyParam('items', []);
        
        // New nodes support
        $menu->nodes = $this->request->getBodyParam('nodes', []);
        $menu->settings = $this->request->getBodyParam('settings', []);

        if (Plugin::getInstance()->menuService->saveMenu($menu)) {
            $this->setSuccessFlash('Navigation saved successfully.');
            return $this->redirectToPostedUrl($menu);
        }

        $this->setFailFlash('Could not save navigation.');
        return $this->renderTemplate('simple-menu/_edit_nav_style.twig', [
            'menu' => $menu,
            'isNew' => !$menu->id,
        ]);
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();

        $menuId = $this->request->getRequiredBodyParam('id');
        $menu = Plugin::getInstance()->menuService->getMenuById((int)$menuId);
        
        if (!$menu) {
            throw new NotFoundHttpException('Menu not found');
        }

        if (Plugin::getInstance()->menuService->deleteMenu($menu)) {
            $this->setSuccessFlash('Menu deleted successfully.');
        } else {
            $this->setFailFlash('Could not delete menu.');
        }

        return $this->redirectToPostedUrl();
    }
}