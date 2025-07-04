<?php
declare(strict_types=1);

namespace App\UI\Front\Home;

use Nette;
use Nette\Application\UI\Form;
use App\Model\OrderFacade;
use App\Model\PageFacade;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    private OrderFacade $orderFacade;
    private PageFacade $pageFacade;

    public function __construct(OrderFacade $orderFacade, PageFacade $pageFacade)
    {
        parent::__construct();
        $this->orderFacade = $orderFacade;
        $this->pageFacade = $pageFacade;
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
            ->setPrompt('Select a manufacturer')
            ->setHtmlAttribute('data-url', $this->link('Endpoint:manufacturers'));

        $model = $form->addSelect('model', 'Model:')
            ->setPrompt('Select a model')
            ->setHtmlAttribute('data-depends', $manufacturer->getHtmlName())
            ->setHtmlAttribute('data-url', $this->link('Endpoint:models', ['manufacturerId' => '#']))
            ->setHtmlAttribute('data-colors-url', $this->link('Endpoint:modelColors', ['modelId' => '#']));

        $form->addHidden('color');

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
        $manufacturerId = (int) $values['manufacturer'];
        $modelId = (int) $values['model'];
        $color = $values['color'];

        $success = $this->orderFacade->addOrder($manufacturerId, $modelId, $color);
        if ($success) {
            $this->flashMessage('Data was successfully saved.', 'success');
        } else {
            $this->flashMessage('An error occurred while saving data.', 'error');
        }
        $this->redirect('this');
    }
}