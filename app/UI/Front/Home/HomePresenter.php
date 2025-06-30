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

    public function caseFormSucceeded(Form $form, \stdClass $values): void
{
    $userId = $this->getUser()->isLoggedIn() ? (int) $this->getUser()->getId() : null;

    $data = [
        'manufacturer' => $values->manufacturer,
        'model' => $values->model,
        'color' => $values->color,
        'port_cover' => (bool) $values->port_cover,
        'card_holder' => $values->card_holder,
        'created_at' => new \DateTime(),
    ];

    $this->orderFacade->createCase($data, $userId, $this->getSession());

    $this->flashMessage('Kryt byl přidán do košíku.', 'success');
    $this->redirect('this');
}

    
}
