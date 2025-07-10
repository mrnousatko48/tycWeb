<?php
declare(strict_types=1);

namespace App\UI\Admin\Product;

use Nette;
use App\Model\ModelFacade;
use Nette\Application\UI\Form;

final class ProductPresenter extends Nette\Application\UI\Presenter
{
    private ModelFacade $modelFacade;

    public function __construct(ModelFacade $modelFacade)
    {
        parent::__construct();
        $this->modelFacade = $modelFacade;
    }

    public function renderDefault(): void
    {
    }

    public function renderColors(): void
    {
        $this->template->colors = $this->modelFacade->getColors();
        $this->template->isAjax = $this->isAjax();
    }

    public function renderFeatures(): void
    {
        $this->template->features = $this->modelFacade->getFeatures();
        $this->template->featureOptions = $this->modelFacade->getAllFeatureOptions();
        $this->template->isAjax = $this->isAjax();
    }

    public function renderModels(?int $manufacturerId = null): void
    {
        $this->template->manufacturers = $this->modelFacade->getManufacturers();
        $this->template->models = $this->modelFacade->getModels($manufacturerId);
        $this->template->selectedManufacturerId = $manufacturerId;
        $this->template->isAjax = $this->isAjax();
    }

    public function createComponentModelForm(): Form
    {
        $form = new Form;

        $form->addSelect('manufacturer_id', 'Výrobce:', 
            $this->modelFacade->getManufacturers()->fetchPairs('id', 'name'))
            ->setPrompt('Vyberte výrobce')
            ->setRequired('Prosím, vyberte výrobce.');

        $form->addText('name', 'Název modelu:')
            ->setRequired('Prosím, zadejte název modelu.');

        $form->addMultiSelect('color_ids', 'Dostupné barvy:', 
            $this->modelFacade->getColors()->fetchPairs('id', 'name'))
            ->setHtmlAttribute('multiple')
            ->setRequired('Vyberte alespoň jednu barvu.');

        $features = $this->modelFacade->getFeatures()->fetchPairs('id', 'name');
        $featureOptions = [];
        foreach ($features as $featureId => $featureName) {
            $options = $this->modelFacade->getFeatureOptions($featureId);
            $featureOptions[$featureId] = array_combine(
                array_map(fn($opt) => "$featureId:$opt[id]", $options),
                array_map(fn($opt) => "$featureName: {$opt['name']}", $options)
            );
        }
        $form->addMultiSelect('feature_options', 'Dostupné vlastnosti:', $featureOptions)
            ->setHtmlAttribute('multiple');

        $form->addSubmit('save', 'Uložit model');

        $form->onSuccess[] = [$this, 'modelFormSucceeded'];
        return $form;
    }

    public function modelFormSucceeded(Form $form, array $values): void
    {
        try {
            $featureOptions = [];
            foreach ($values['feature_options'] as $featureOptionId) {
                $parts = explode(':', $featureOptionId, 2);
                if (count($parts) === 2) {
                    $featureId = (int)$parts[0];
                    $optionId = (int)trim($parts[1]);
                    $featureOptions[$featureId] = $optionId;
                }
            }
            $values['feature_options'] = $featureOptions;

            $this->modelFacade->addModel($values);
            $this->flashMessage('Model byl úspěšně přidán!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->redrawControl('modelsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('models');
        }
    }

    public function createComponentManufacturerForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Název výrobce:')
            ->setRequired('Prosím, zadejte název výrobce.');

        $form->addSubmit('save', 'Přidat výrobce');

        $form->onSuccess[] = [$this, 'manufacturerFormSucceeded'];
        return $form;
    }

    public function manufacturerFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->addManufacturer($values['name']);
            $this->flashMessage('Výrobce byl přidán!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->redrawControl('manufacturersSelect');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('models');
        }
    }

    public function createComponentColorForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Název barvy:')
            ->setRequired('Prosím, zadejte název barvy.');

        $form->addText('hex_code', 'Hex kód (např. #FF0000):')
            ->addRule($form::PATTERN, 'Musí být platný hex kód (např. #FF0000)', '^#[0-9A-Fa-f]{6}$')
            ->setRequired(false);

        $form->addSubmit('save', 'Přidat barvu');

        $form->onSuccess[] = [$this, 'colorFormSucceeded'];
        return $form;
    }

    public function colorFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->addColor($values['name'], $values['hex_code'] ?? null);
            $this->flashMessage('Barva byla úspěšně přidána!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->redrawControl('colorsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('colors');
        }
    }

    public function createComponentFeatureForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Název funkci:')
            ->setRequired('Prosím, zadejte název funkce.');

        $form->addSubmit('save', 'Přidat funkci');

        $form->onSuccess[] = [$this, 'featureFormSucceeded'];
        return $form;
    }

    public function featureFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->addFeature($values['name']);
            $this->flashMessage('Funkce byla úspěšně přidána!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->features = $this->modelFacade->getFeatures();
            $this->redrawControl('featuresTable');
            $this->redrawControl('featureOptionForm-feature_id');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('features');
        }
    }


    # Creating feature option form with price input
    public function createComponentFeatureOptionForm(): Form
    {
        $form = new Form;

        $form->addSelect('feature_id', 'Funkce:', 
            $this->modelFacade->getFeatures()->fetchPairs('id', 'name'))
            ->setPrompt('Vyberte funkci')
            ->setRequired('Prosím, vyberte funkci.');

        $form->addText('name', 'Název varianty:')
            ->setRequired('Prosím, zadejte název varianty.');

        $form->addText('price', 'Cena (CZK):')
            ->setRequired('Prosím, zadejte cenu.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

        $form->addSubmit('save', 'Přidat variantu');

        $form->onSuccess[] = [$this, 'featureOptionFormSucceeded'];
        return $form;
    }

    # Handling feature option form submission
    public function featureOptionFormSucceeded(Form $form, array $values): void
    {
        try {
            $existingOption = $this->modelFacade->getAllFeatureOptions()
                ->where('feature_id', $values['feature_id'])
                ->where('name', $values['name'])
                ->fetch();
            
            if ($existingOption) {
                $this->flashMessage("Možnost '{$values['name']}' již pro tuto vlastnost existuje.", 'error');
            } else {
                $this->modelFacade->addFeatureOption($values['feature_id'], $values['name'], (float)$values['price']);
                $this->flashMessage('Možnost byla úspěšně přidána!', 'success');
            }
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->featureOptions = $this->modelFacade->getAllFeatureOptions();
            $this->redrawControl('featureOptionsTable');
            $this->redrawControl('featureOptionForm-feature_id');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('features');
        }
    }

    # Creating feature option edit form
    public function createComponentFeatureOptionEditForm(): Form
    {
        $form = new Form;

        $form->addHidden('id');

        $form->addSelect('feature_id', 'Funkce:', 
            $this->modelFacade->getFeatures()->fetchPairs('id', 'name'))
            ->setPrompt('Vyberte funkci')
            ->setRequired('Prosím, vyberte funkci.');

        $form->addText('name', 'Název varianty:')
            ->setRequired('Prosím, zadejte název varianty.');

        $form->addText('price', 'Cena (CZK):')
            ->setRequired('Prosím, zadejte cenu.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

        $form->addSubmit('save', 'Upravit variantu');

        $form->onSuccess[] = [$this, 'featureOptionEditFormSucceeded'];
        return $form;
    }

    # Handling feature option edit form submission
    public function featureOptionEditFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->updateFeatureOption((int)$values['id'], $values['name'], (float)$values['price']);
            $this->flashMessage('Varianta byla úspěšně upravena!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->featureOptions = $this->modelFacade->getAllFeatureOptions();
            $this->redrawControl('featureOptionsTable');
            $this->redrawControl('featureOptionForm-feature_id');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('features');
        }
    }

    # Handling edit feature option action
    public function handleEditFeatureOption(int $optionId): void
    {

        $option = $this->modelFacade->getAllFeatureOptions()->get($optionId);
        if (!$option) {
            $this->flashMessage('Možnost neexistuje.', 'error');
            $this->redirect('features');
        }

        $this['featureOptionEditForm']->setDefaults([
            'id' => $option->id,
            'feature_id' => $option->feature_id,
            'name' => $option->name,
            'price' => $option->price,
        ]);

        if ($this->isAjax()) {
            $this->redrawControl('featureOptionEditForm');
        }
    }

    public function handleFilterModels(int $manufacturerId): void
    {
        if ($this->isAjax()) {
            $this->template->models = $this->modelFacade->getModels($manufacturerId);
            $this->template->selectedManufacturerId = $manufacturerId;
            $this->redrawControl('modelsTable');
        }
    }

    public function handleDeleteColor(int $colorId): void
    {
        try {
            $this->modelFacade->deleteColor($colorId);
            $this->flashMessage('Barva byla úspěšně smazána!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->redrawControl('colorsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('colors');
        }
    }

    public function handleDeleteFeature(int $featureId): void
    {
        try {
            $this->modelFacade->deleteFeature($featureId);
            $this->flashMessage('Vlastnost byla úspěšně smazána!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->features = $this->modelFacade->getFeatures();
            $this->template->featureOptions = $this->modelFacade->getAllFeatureOptions();
            $this->redrawControl('featuresTable');
            $this->redrawControl('featureOptionsTable');
            $this->redrawControl('featureOptionForm-feature_id');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('features');
        }
    }

    public function handleDeleteFeatureOption(int $optionId): void
    {
        try {
            $this->modelFacade->deleteFeatureOption($optionId);
            $this->flashMessage('Možnost byla úspěšně smazána!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->featureOptions = $this->modelFacade->getAllFeatureOptions();
            $this->redrawControl('featureOptionsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('features');
        }
    }

    public function handleDeleteModel(int $modelId): void
    {
        try {
            $this->modelFacade->deleteModel($modelId);
            $this->flashMessage('Model byl úspěšně smazán!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->models = $this->modelFacade->getModels($this->template->selectedManufacturerId);
            $this->redrawControl('modelsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('models');
        }
    }

    public function handleDeleteManufacturer(int $manufacturerId): void
    {
        try {
            $this->modelFacade->deleteManufacturer($manufacturerId);
            $this->flashMessage('Výrobce byl úspěšně smazán!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->manufacturers = $this->modelFacade->getManufacturers();
            $this->template->models = $this->modelFacade->getModels();
            $this->template->selectedManufacturerId = null;
            $this->redrawControl('manufacturersSelect');
            $this->redrawControl('modelsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('models');
        }
    }
}