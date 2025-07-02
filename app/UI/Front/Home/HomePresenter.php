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
    }

    protected function createComponentCaseForm(): Form
    {
        $form = new Form;

        $manufacturers = $this->pageFacade->getFormOptions('manufacturer');
        $form->addSelect('manufacturer', 'Výrobce:', array_column($manufacturers, 'option_label', 'option_value'))
            ->setPrompt('Vyberte výrobce')
            ->setRequired();

        $form->addText('model', 'Model:')
            ->setRequired();

        $colors = $this->pageFacade->getFormOptions('color');
        $form->addSelect('color', 'Barva:', array_column($colors, 'option_label', 'option_value'))
            ->setRequired();

        $portCovers = $this->pageFacade->getFormOptions('port_cover');
        $form->addRadioList('port_cover', 'Krytka nabíjecího portu:', array_column($portCovers, 'option_label', 'option_value'))
            ->setRequired();

        $cardHolders = $this->pageFacade->getFormOptions('card_holder');
        $form->addSelect('card_holder', 'Držák karet:', array_column($cardHolders, 'option_label', 'option_value'))
            ->setRequired();

        $form->addSubmit('submit', 'Přidat do košíku');

        $form->onSuccess[] = [$this, 'caseFormSucceeded'];

        return $form;
    }

    public function caseFormSucceeded(Form $form, \stdClass $values): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage('Pro zadání objednávky se musíte přihlásit.', 'danger');
            $this->redirect('Sign:in');
        }
    
        $userId = (int) $this->getUser()->getId();
    
        $this->orderFacade->createCase([
            'user_id' => $userId,
            'manufacturer' => $values->manufacturer,
            'model' => $values->model,
            'color' => $values->color,
            'port_cover' => (bool) $values->port_cover,
            'card_holder' => $values->card_holder,
            'STATE' => "KOSIK",
            'created_at' => new \DateTime(),
        ]);
    
        $this->flashMessage('Kryt byl uložen do Vašeho košíku.', 'success');
        $this->redirect('this');
    }

    protected function createComponentEditContentForm(): Form
    {
        $form = new Form;

        $sections = ['banner', 'durability', 'customization'];
        foreach ($sections as $section) {
            $sectionContent = $this->pageFacade->getSectionContent($section);
            foreach ($sectionContent as $content) {
                if ($content->content_text !== null) {
                    $form->addTextArea("{$section}_{$content->content_type}", ucfirst($section) . ' ' . $content->content_type)
                        ->setDefaultValue($content->content_text);
                }
                if ($content->image_path) {
                    $form->addText("{$section}_{$content->content_type}_image", ucfirst($section) . ' ' . $content->content_type . ' Image')
                        ->setDefaultValue($content->image_path);
                }
            }
        }

        $form->addSubmit('submit', 'Uložit změny');
        $form->onSuccess[] = [$this, 'editContentFormSucceeded'];
        return $form;
    }

    public function editContentFormSucceeded(Form $form, \stdClass $values): void
    {
        foreach ($values as $key => $value) {
            [$section, $content_type] = explode('_', $key, 2);
            if (strpos($content_type, 'image') !== false) {
                [$content_type] = explode('_', $content_type);
                $this->pageFacade->updateSectionContent($section, $content_type, null, $value);
            } else {
                $this->pageFacade->updateSectionContent($section, $content_type, $value);
            }
        }
        $this->flashMessage('Obsah byl úspěšně aktualizován.', 'success');
        $this->redirect('this');
    }

    public function renderEditContent(): void
    {
        if (!$this->getUser()->isLoggedIn() || !$this->getUser()->isInRole('admin')) {
            $this->flashMessage('Pro úpravu obsahu musíte být přihlášen jako administrátor.', 'danger');
            $this->redirect('Sign:in');
        }
    }
}