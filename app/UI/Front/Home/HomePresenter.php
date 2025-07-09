<?php

namespace App\UI\Front\Home;

use Nette\Application\UI\Form;
use App\Model\PageFacade;
use Nette\Http\Session;
use App\UI\Front\BaseFrontPresenter;


final class HomePresenter extends BaseFrontPresenter

{
    private PageFacade $pageFacade;
    private Session $session;

    public function __construct(PageFacade $pageFacade, Session $session)
    {
        parent::__construct();
        $this->pageFacade = $pageFacade;
        $this->session = $session;
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
        $this->template->customization = $this->pageFacade->getSectionContent('customization');
        $this->template->gallery = $this->pageFacade->getGalleryImages();
        $this->template->contact = $this->pageFacade->getContactInfo();
        $this->template->manufacturers = $this->pageFacade->getManufacturers();
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
            ->setPrompt('vyberte model')
            ->setHtmlAttribute('data-depends', $manufacturer->getHtmlName())
            ->setHtmlAttribute('data-url', $this->link('Endpoint:models', ['manufacturerId' => '#']))
            ->setHtmlAttribute('data-colors-url', $this->link('Endpoint:modelColors', ['modelId' => '#']))
            ->setRequired('Prosím vyberte model.');

        $form->addHidden('color')->setRequired('Please select a color.');
        $form->addHidden('chargingPortCover')->setDefaultValue('Ano');
        $form->addHidden('frontCameraCover')->setDefaultValue('Ano');
        $form->addHidden('cardHolder')->setDefaultValue('Žádný');

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
        $manufacturerId = (int)$values['manufacturer'];
        $modelId = (int)$values['model'];
        $color = $values['color'];
        $chargingPortCover = $values['chargingPortCover'];
        $frontCameraCover = $values['frontCameraCover'];
        $cardHolder = $values['cardHolder'];

        try {
            $manufacturerName = $this->pageFacade->getManufacturerNameById($manufacturerId);
            $modelName = $this->pageFacade->getModelNameById($modelId);

            if (!$manufacturerName || !$modelName) {
                throw new \Exception('Invalid manufacturer or model selected.');
            }

            $userId = $this->getUser()->isLoggedIn() ? $this->getUser()->getId() : null;

            $this->orderFacade->createCase([
                'manufacturer' => $manufacturerName,
                'model' => $modelName,
                'color' => $color,
                'port_cover' => $chargingPortCover === 'Ano' ? 1 : 0,
                'camera_cover' => $frontCameraCover === 'Ano' ? 1 : 0,
                'card_holder' => $cardHolder,
            ], $userId);

            $this->flashMessage('Položka byla přidána do košíku.', 'success');
            $this->redirect('Cart:default'); // Redirect to cart page (create this route if it doesn't exist)
        } catch (\Exception $e) {
            $this->flashMessage('Chyba při přidávání do košíku: ' . $e->getMessage(), 'error');
            $this->redirect('this');
        }
    }
}