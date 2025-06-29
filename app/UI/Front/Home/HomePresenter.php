<?php

declare(strict_types=1);

namespace App\UI\Front\Home;

use Nette;
use Nette\Application\UI\Form;
use App\Model\OrderFacade;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    private OrderFacade $orderFacade;

    public function __construct(OrderFacade $orderFacade)
    {
        parent::__construct();
        $this->orderFacade = $orderFacade;
    }

    public function renderDefault(): void
    {
       
    }

    protected function createComponentCaseForm(): Form
    {
        $form = new Form;

        $form->addSelect('manufacturer', 'Výrobce:', [
            'apple' => 'Apple',
            'samsung' => 'Samsung',
            'xiaomi' => 'Xiaomi',
        ])
            ->setPrompt('Vyberte výrobce')
            ->setRequired();

        $form->addText('model', 'Model:')
            ->setRequired();

        $form->addSelect('color', 'Barva:', [
            'Černá' => 'Černá',
            'Bílá' => 'Bílá',
            'Modrá' => 'Modrá',
            'Červená' => 'Červená',
        ])
            ->setRequired();

        $form->addRadioList('port_cover', 'Krytka nabíjecího portu:', [
            1 => 'Ano',
            0 => 'Ne',
        ])
            ->setRequired();

        $form->addSelect('card_holder', 'Držák karet:', [
            '1 slot' => '1 slot',
            '2 sloty' => '2 sloty',
            'Žádný' => 'Žádný',
        ])
            ->setRequired();

        $form->addSubmit('submit', 'Přidat do košíku');

        $form->onSuccess[] = [$this, 'caseFormSucceeded'];

        return $form;
    }

    public function CaseFormSucceeded(Form $form, \stdClass $values): void
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
    
}
