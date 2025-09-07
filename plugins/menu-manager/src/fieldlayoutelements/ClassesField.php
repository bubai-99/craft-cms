<?php
namespace mycompany\menumanager\fieldlayoutelements;

use Craft;
use craft\base\ElementInterface;
use craft\fieldlayoutelements\TextField;

class ClassesField extends TextField
{
    // Properties
    // =========================================================================

    public string $attribute = 'classes';
    public bool $requirable = true;


    // Public Methods
    // =========================================================================

    public function defaultLabel(?ElementInterface $element = null, bool $static = false): ?string
    {
        return Craft::t('menu-manager', 'Classes');
    }

    public function instructions(ElementInterface $element = null, bool $static = false): ?string
    {
        return Craft::t('menu-manager', 'Additional CSS classes for this navigation item.');
    }
}
