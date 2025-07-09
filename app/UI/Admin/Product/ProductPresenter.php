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

        $form->addSelect('manufacturer_id', 'Manufacturer:', 
            $this->modelFacade->getManufacturers()->fetchPairs('id', 'name'))
            ->setPrompt('Select manufacturer')
            ->setRequired();

        $form->addText('name', 'Model name:')
            ->setRequired();

        $form->addMultiSelect('color_ids', 'Available colors:', 
            $this->modelFacade->getColors()->fetchPairs('id', 'name'))
            ->setHtmlAttribute('multiple')
            ->setRequired('Select at least one color.');

        $features = $this->modelFacade->getFeatures()->fetchPairs('id', 'name');
        $featureOptions = [];
        foreach ($features as $featureId => $featureName) {
            $options = $this->modelFacade->getFeatureOptions($featureId);
            $featureOptions[$featureId] = array_combine(
                array_map(fn($opt) => "$featureId:$opt[id]", $options),
                array_map(fn($opt) => "$featureName: {$opt['name']}", $options)
            );
        }
        $form->addMultiSelect('feature_options', 'Available feature options:', $featureOptions)
            ->setHtmlAttribute('multiple')
            ->setPrompt('Select feature options');

        $form->addSubmit('save', 'Save model');

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
            $this->flashMessage('Model added successfully!', 'success');
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

        $form->addText('name', 'Manufacturer name:')
            ->setRequired();

        $form->addSubmit('save', 'Add manufacturer');

        $form->onSuccess[] = [$this, 'manufacturerFormSucceeded'];
        return $form;
    }

    public function manufacturerFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->addManufacturer($values['name']);
            $this->flashMessage('Manufacturer added!', 'success');
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

        $form->addText('name', 'Color name:')
            ->setRequired();

        $form->addText('hex_code', 'Hex code (e.g., #FF0000):')
            ->addRule($form::PATTERN, 'Must be a valid hex color code (e.g., #FF0000)', '^#[0-9A-Fa-f]{6}$')
            ->setRequired(false);

        $form->addSubmit('save', 'Add color');

        $form->onSuccess[] = [$this, 'colorFormSucceeded'];
        return $form;
    }

    public function colorFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->addColor($values['name'], $values['hex_code'] ?? null);
            $this->flashMessage('Color added successfully!', 'success');
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

        $form->addText('name', 'Feature name:')
            ->setRequired();

        $form->addSubmit('save', 'Add feature');

        $form->onSuccess[] = [$this, 'featureFormSucceeded'];
        return $form;
    }

    public function featureFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->addFeature($values['name']);
            $this->flashMessage('Feature added successfully!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->features = $this->modelFacade->getFeatures();
            $this->redrawControl('featuresTable');
            $this->redrawControl('featureOptionForm-feature_id'); // Update the feature select dropdown
            $this->redrawControl('flashes');
        } else {
            $this->redirect('features');
        }
    }

    public function createComponentFeatureOptionForm(): Form
    {
        $form = new Form;

        $form->addSelect('feature_id', 'Feature:', 
            $this->modelFacade->getFeatures()->fetchPairs('id', 'name'))
            ->setPrompt('Select feature')
            ->setRequired();

        $form->addText('name', 'Option name:')
            ->setRequired();

        $form->addSubmit('save', 'Add option');

        $form->onSuccess[] = [$this, 'featureOptionFormSucceeded'];
        return $form;
    }

    public function featureOptionFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->addFeatureOption($values['feature_id'], $values['name']);
            $this->flashMessage('Option added successfully!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->featureOptions = $this->modelFacade->getAllFeatureOptions();
            $this->redrawControl('featureOptionsTable');
            $this->redrawControl('featureOptionForm-feature_id'); // Update the feature select dropdown
            $this->redrawControl('flashes');
        } else {
            $this->redirect('features');
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
            $this->flashMessage('Color deleted successfully!', 'success');
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
            $this->flashMessage('Feature deleted successfully!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->features = $this->modelFacade->getFeatures();
            $this->template->featureOptions = $this->modelFacade->getAllFeatureOptions();
            $this->redrawControl('featuresTable');
            $this->redrawControl('featureOptionsTable');
            $this->redrawControl('featureOptionForm-feature_id'); // Update the feature select dropdown
            $this->redrawControl('flashes');
        } else {
            $this->redirect('features');
        }
    }

    public function handleDeleteFeatureOption(int $optionId): void
    {
        try {
            $this->modelFacade->deleteFeatureOption($optionId);
            $this->flashMessage('Option deleted successfully!', 'success');
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
            $this->flashMessage('Model deleted successfully!', 'success');
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
            $this->flashMessage('Manufacturer deleted successfully!', 'success');
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