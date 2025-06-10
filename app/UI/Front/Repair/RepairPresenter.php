<?php

declare(strict_types=1);

namespace App\UI\Front\Repair;

use Nette;
use App\Model\RepairFacade;
use Nette\Application\UI\Form;
use stdClass;

final class RepairPresenter extends Nette\Application\UI\Presenter
{
    private RepairFacade $repairFacade;

    public function __construct(RepairFacade $repairFacade)
    {
        parent::__construct();
        $this->repairFacade = $repairFacade;
    }

    protected function createComponentForm(): Form
    {
        $form = new Form;

        $type = $form->addSelect('type', 'Device Type:', $this->repairFacade->getTypes())
            ->setPrompt('----');

        $manufacturer = $form->addSelect('manufacturer', 'Manufacturer:')
            ->setHtmlAttribute('data-depends', $type->getHtmlName())
            ->setHtmlAttribute('data-url', $this->link('Endpoint:manufacturers', ['typeId' => '#']));

        $model = $form->addSelect('model', 'Model:')
            ->setHtmlAttribute('data-depends', $manufacturer->getHtmlName())
            ->setHtmlAttribute('data-url', $this->link('Endpoint:models', ['manufacturerId' => '#', 'typeId' => '#']));

        $fault = $form->addSelect('fault', 'Fault:')
            ->setHtmlAttribute('data-url', $this->link('Endpoint:faults', ['faultId' => '#']));

        $form->addText('name', 'First Name:')
            ->setRequired('Please enter your first name.');

        $form->addText('surname', 'Last Name:')
            ->setRequired('Please enter your last name.');

        $form->addEmail('email', 'Email:')
            ->setRequired('Please enter a valid email address.');

        $form->addText('phone', 'Phone:')
            ->setRequired('Please enter your phone number.');

        $form->addSubmit('submit', 'Submit');

        $form->onAnchor[] = function () use ($model, $manufacturer, $type, $fault) {
            $model->setItems(
                $manufacturer->getValue() && $type->getValue()
                    ? $this->repairFacade->getModelsByManufacturer($manufacturer->getValue(), $type->getValue())
                    : []
            );
            $fault->setItems($this->repairFacade->getFaults(null));
        };

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }

    public function processForm(Form $form, stdClass $values): void
{
    $values = $form->getHttpData();

    // Explicitly cast form values to integers
    $manufacturerId = (int) $values['manufacturer'];
    $modelId = (int) $values['model'];
    $faultId = (int) $values['fault'];
    $name = $values['name'];
    $surname = $values['surname'];
    $email = $values['email'];
    $phone = $values['phone'];

    // Call addRepair with correctly typed arguments
    $success = $this->repairFacade->addRepair($manufacturerId, $modelId, $faultId, $name, $surname, $email, $phone);

    if ($success) {
        $this->flashMessage('Data was successfully saved.', 'success');
    } else {
        $this->flashMessage('An error occurred while saving data.', 'error');
    }

    $this->redirect('this');
}

}
