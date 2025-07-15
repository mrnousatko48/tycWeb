<?php
declare(strict_types=1);

namespace App\UI\Front\Home;

use Nette\Application\UI\Form;
use App\UI\Front\BaseFrontPresenter;

final class HomePresenter extends BaseFrontPresenter
{
    public function __construct()
    {
        parent::__construct(); 
    }

    public function startup(): void
    {
        parent::startup();
        $savedTheme = $this->getHttpRequest()->getCookie('theme');
        if ($savedTheme === 'dark') {
            $this->template->darkMode = true;
        } elseif ($savedTheme === 'light') {
            $this->template->darkMode = false;
        } else {
            $this->template->darkMode = $this->isDarkModePreferred();
        }
    }

    public function isDarkModePreferred(): bool
    {
        return $this->getHttpRequest()->isAjax() ? false : (bool)preg_match('/dark/', $this->getHttpRequest()->getHeader('Sec-CH-Prefers-Color-Scheme') ?: '');
    }

    public function handleToggleTheme(): void
    {
        $currentTheme = $this->getHttpRequest()->getCookie('theme') ?: ($this->isDarkModePreferred() ? 'dark' : 'light');
        $newTheme = $currentTheme === 'light' ? 'dark' : 'light';
        $this->getHttpResponse()->setCookie('theme', $newTheme, '30 days');
        $this->redirect('this');
    }

    public function renderDefault(): void
    {
        $this->template->banner = $this->pageFacade->getSectionContent('banner');
        $this->template->durability = $this->pageFacade->getSectionContent('durability');
        $this->template->customizations = $this->pageFacade->getCustomizations();
        $this->template->gallery = $this->pageFacade->getGalleryImages();
        $this->template->contact = $this->pageFacade->getContactInfo();
    }

    public function renderDetail(): void
    {
        $this->template->manufacturers = $this->pageFacade->getManufacturers();
        // Optionally, pass initial images if a default model is pre-selected
        // For example, if you want to show images for a default model (e.g., model ID 1):
        // $this->template->initialImages = $this->pageFacade->getImagesByModel(1);
    }

    protected function createComponentCaseForm(): Form
    {
        $form = new Form;

        $manufacturers = $this->pageFacade->getManufacturers();
        $manufacturerItems = [];
        foreach ($manufacturers as $manufacturer) {
            $manufacturerItems[$manufacturer->id] = $manufacturer->name;
        }

        $manufacturer = $form->addSelect('manufacturer', 'Manufacturer:', $manufacturerItems)
            ->setPrompt('Vyberte výrobce')
            ->setHtmlAttribute('data-url', $this->link('Endpoint:manufacturers'))
            ->setRequired('Prosím vyberte výrobce.');

        $model = $form->addSelect('model', 'Model:')
            ->setPrompt('Vyberte model')
            ->setHtmlAttribute('data-depends', $manufacturer->getHtmlName())
            ->setHtmlAttribute('data-url', $this->link('Endpoint:models', ['manufacturerId' => '#']))
            ->setHtmlAttribute('data-colors-url', $this->link('Endpoint:modelColors', ['modelId' => '#']))
            ->setHtmlAttribute('data-features-url', $this->link('Endpoint:modelFeatures', ['modelId' => '#']))
            ->setHtmlAttribute('data-price-url', $this->link('Endpoint:modelPrice', ['modelId' => '#']))
            ->setHtmlAttribute('data-images-url', $this->link('Endpoint:modelImages', ['modelId' => '#'])) // Added
            ->setRequired('Prosím vyberte model.');

        $form->addHidden('color')->setRequired('Prosím vyberte barvu.');
        $form->addHidden('features')->setDefaultValue('{}');
        $form->addHidden('total_price')->setDefaultValue('0.00');

        $form->addSubmit('submit', 'Přidat do košíku');

        $form->onAnchor[] = function () use ($model, $manufacturer) {
            $model->setItems(
                $manufacturer->getValue()
                    ? $this->pageFacade->getModelsByManufacturer((int)$manufacturer->getValue())
                    : []
            );
        };

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }

    public function processForm(Form $form): void
    {
        $values = $form->getValues();
        bdump($values);
        error_log('Form submitted with values: ' . print_r($values, true));
        try {
            $manufacturerId = (int)$values['manufacturer'];
            $modelId = (int)$values['model'];
            $color = $values['color'];
            $totalPrice = (float)$values['total_price'];
            $features = json_decode($values['features'], true);

            if (!$manufacturerId || !$modelId || !$color) {
                throw new \Exception('Missing required fields: manufacturer, model, or color.');
            }

            if (!is_array($features)) {
                throw new \Exception('Invalid features format.');
            }

            $manufacturerName = $this->pageFacade->getManufacturerNameById($manufacturerId);
            $modelName = $this->pageFacade->getModelNameById($modelId);

            if (!$manufacturerName || !$modelName) {
                throw new \Exception('Invalid manufacturer or model selected.');
            }

            $caseData = [
                'manufacturer' => $manufacturerName,
                'model' => $modelName,
                'color' => $color,
                'total_price' => $totalPrice,
                'features' => $values['features']
            ];

            $userId = $this->getUser()->isLoggedIn() ? $this->getUser()->getId() : null;
            error_log('Creating case with data: ' . print_r($caseData, true));
            $this->orderFacade->createCase($caseData, $userId);

            $this->flashMessage('Položka byla přidána do košíku.', 'success');
            $this->redirect('Cart:default');
        } catch (\Exception $e) {
            error_log('Error in processForm: ' . $e->getMessage());
            $this->flashMessage('Chyba při přidávání do košíku: ' . $e->getMessage(), 'error');
            $this->redirect('this');
        }
    }
}