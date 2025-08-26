<?php
declare(strict_types=1);

namespace App\UI\Admin\Product;

use Nette;
use App\Model\ModelFacade;
use App\Model\OrderFacade;
use Nette\Application\UI\Form;

final class ProductPresenter extends Nette\Application\UI\Presenter
{
    private ModelFacade $modelFacade;
    private OrderFacade $orderFacade;

    public function __construct(ModelFacade $modelFacade, OrderFacade $orderFacade)
    {
        parent::__construct();
        $this->modelFacade = $modelFacade;
        $this->orderFacade = $orderFacade;
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

    public function renderImages(int $modelId): void
    {
        $model = $this->modelFacade->getModelById($modelId);
        if (!$model) {
            $this->error('Model nenalezen.');
        }

        $images = $this->modelFacade->getModelImages($modelId);

        $this['imageForm']->setDefaults([
            'model_id' => $modelId,
        ]);

        $this->template->model = $model;
        $this->template->images = $images;
    }

    public function renderDefaultImages(): void
    {
        $defaultImages = $this->modelFacade->getDefaultImages();
        $this['defaultImageForm']->setDefaults([]);
        $this->template->defaultImages = $defaultImages;
    }

    public function renderShipping(): void
    {
        $vendors = $this->orderFacade->getAllVendors();
        $this->template->vendors = array_combine(
            array_column(iterator_to_array($vendors), 'id'),
            $vendors
        );
        $this->template->shippingOptions = $this->orderFacade->getAllShippingOptions();
        $this->template->paymentMethods = $this->orderFacade->getAllPaymentMethods();
        $this->template->isAjax = $this->isAjax();
    }

    public function createComponentDefaultImageForm(): Form
    {
        $form = new Form;
        $form->addUpload('image', 'Globální defaultní obrázek:')
            ->setRequired('Prosím, vyberte globální defaultní obrázek.')
            ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF).')
            ->addRule(Form::MIME_TYPE, 'Soubor musí být JPEG, PNG nebo GIF.', ['image/jpeg', 'image/png', 'image/gif']);

        $form->addSubmit('save', 'Nahrát obrázek');
        $form->onSuccess[] = [$this, 'defaultImageFormSucceeded'];
        return $form;
    }

    public function defaultImageFormSucceeded(Form $form, array $values): void
    {
        try {
            $image = $this->modelFacade->addDefaultImage($values['image']);
            if ($image) {
                $this->flashMessage('Defaultní obrázek byl úspěšně nahrán!', 'success');
            } else {
                $this->flashMessage('Nepodařilo se nahrát defaultní obrázek.', 'error');
            }
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->defaultImages = $this->modelFacade->getDefaultImages();
            $this->redrawControl('defaultImagesList');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
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

        $form->addText('price', 'Základní cena (CZK):')
            ->setRequired('Prosím, zadejte základní cenu.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

        $form->addText('price_eur', 'Základní cena (EUR):')
            ->setRequired('Prosím, zadejte základní cenu v EUR.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

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

        $form->addUpload('model_3d_file', '3D soubor (.gltf):')
            ->setRequired(false);

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

   public function createComponentModelEditForm(): Form
    {
        $form = new Form;

        $form->addHidden('id');

        $form->addSelect('manufacturer_id', 'Výrobce:', 
            $this->modelFacade->getManufacturers()->fetchPairs('id', 'name'))
            ->setPrompt('Vyberte výrobce')
            ->setRequired('Prosím, vyberte výrobce.');

        $form->addText('name', 'Název modelu:')
            ->setRequired('Prosím, zadejte název modelu.');

        $form->addText('price', 'Základní cena (CZK):')
            ->setRequired('Prosím, zadejte základní cenu.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

        $form->addText('price_eur', 'Základní cena (EUR):')
            ->setRequired('Prosím, zadejte základní cenu v EUR.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

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

        $form->addUpload('model_3d_file', '3D soubor (.gltf):')
            ->setRequired(false);

        $form->addSubmit('save', 'Upravit model');

        $form->onSuccess[] = [$this, 'modelEditFormSucceeded'];
        return $form;
    }

    public function modelEditFormSucceeded(Form $form, array $values): void
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

            $this->modelFacade->updateModel((int)$values['id'], $values);
            $this->flashMessage('Model byl úspěšně upraven!', 'success');
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

    public function handleEditModel(int $modelId): void
    {
        $model = $this->modelFacade->getModelById($modelId);
        if (!$model) {
            $this->flashMessage('Model neexistuje.', 'error');
            $this->redirect('models');
        }

        $colorIds = array_keys($this->modelFacade->getModelColors($modelId));
        $featureOptions = [];
        foreach ($this->modelFacade->getModelFeatures($modelId) as $mf) {
            if ($mf->feature_option_id) {
                $featureOptions[] = "{$mf->feature_id}:{$mf->feature_option_id}";
            }
        }

        $this['modelEditForm']->setDefaults([
            'id' => $model->id,
            'manufacturer_id' => $model->manufacturer_id,
            'name' => $model->name,
            'price' => $model->price,
            'price_eur' => $model->price_eur,
            'color_ids' => $colorIds,
            'feature_options' => $featureOptions,
        ]);

        if ($this->isAjax()) {
            $this->redrawControl('modelEditForm');
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
            $this->redrawControl('manufacturersTable');
            $this->redrawControl('manufacturersSelect');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('models');
        }
    }

    public function createComponentManufacturerEditForm(): Form
    {
        $form = new Form;

        $form->addHidden('id');

        $form->addText('name', 'Název výrobce:')
            ->setRequired('Prosím, zadejte název výrobce.');

        $form->addSubmit('save', 'Upravit výrobce');

        $form->onSuccess[] = [$this, 'manufacturerEditFormSucceeded'];
        return $form;
    }

    public function manufacturerEditFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->updateManufacturer((int)$values['id'], $values['name']);
            $this->flashMessage('Výrobce byl úspěšně upraven!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->manufacturers = $this->modelFacade->getManufacturers();
            $this->redrawControl('manufacturersTable');
            $this->redrawControl('manufacturersSelect');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('models');
        }
    }

    public function handleEditManufacturer(int $manufacturerId): void
    {
        $manufacturer = $this->modelFacade->getManufacturer($manufacturerId);
        if (!$manufacturer) {
            $this->flashMessage('Výrobce neexistuje.', 'error');
            $this->redirect('models');
        }

        $this['manufacturerEditForm']->setDefaults([
            'id' => $manufacturer->id,
            'name' => $manufacturer->name,
        ]);

        if ($this->isAjax()) {
            $this->redrawControl('manufacturerEditForm');
        }
    }

    public function createComponentColorForm(): Form
{
    $form = new Form;

    $form->addText('name', 'Název barvy:')
        ->setRequired('Prosím, zadejte název barvy.');

    $form->addText('name_cs', 'Název barvy (CZ):')
        ->setRequired('Prosím, zadejte název barvy v češtině.');

    $form->addText('name_en', 'Název barvy (EN):')
        ->setRequired('Prosím, zadejte název barvy v angličtině.');

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
        $this->modelFacade->addColor($values['name'], $values['hex_code'] ?? null, $values['name_cs'], $values['name_en']);
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

public function createComponentColorEditForm(): Form
{
    $form = new Form;

    $form->addHidden('id');

    $form->addText('name', 'Název barvy:')
        ->setRequired('Prosím, zadejte název barvy.');

    $form->addText('name_cs', 'Název barvy (CZ):')
        ->setRequired('Prosím, zadejte název barvy v češtině.');

    $form->addText('name_en', 'Název barvy (EN):')
        ->setRequired('Prosím, zadejte název barvy v angličtině.');

    $form->addText('hex_code', 'Hex kód (např. #FF0000):')
        ->addRule($form::PATTERN, 'Musí být platný hex kód (např. #FF0000)', '^#[0-9A-Fa-f]{6}$')
        ->setRequired(false);

    $form->addSubmit('save', 'Upravit barvu');

    $form->onSuccess[] = [$this, 'colorEditFormSucceeded'];
    return $form;
}

public function colorEditFormSucceeded(Form $form, array $values): void
{
    try {
        $this->modelFacade->updateColor(
            (int)$values['id'],
            $values['name'],
            $values['hex_code'] ?? null,
            $values['name_cs'],
            $values['name_en']
        );
        $this->flashMessage('Barva byla úspěšně upravena!', 'success');
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

public function handleEditColor(int $colorId): void
{
    $color = $this->modelFacade->getColor($colorId);
    if (!$color) {
        $this->flashMessage('Barva neexistuje.', 'error');
        $this->redirect('colors');
    }

    $this['colorEditForm']->setDefaults([
        'id' => $color->id,
        'name' => $color->name,
        'name_cs' => $color->name_cs,
        'name_en' => $color->name_en,
        'hex_code' => $color->hex_code,
    ]);

    if ($this->isAjax()) {
        $this->redrawControl('colorEditForm');
    }
}

    public function createComponentFeatureForm(): Form
    {
        $form = new Form;

        $form->addText('name', 'Název funkce:')
            ->setRequired('Prosím, zadejte název funkce.');

        $form->addText('name_en', 'Název funkce (EN):')
            ->setRequired(false);

        $form->addSubmit('save', 'Přidat funkci');

        $form->onSuccess[] = [$this, 'featureFormSucceeded'];
        return $form;
    }

    public function featureFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->addFeature($values['name'], $values['name_en'] ?? null);
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

    public function createComponentFeatureEditForm(): Form
    {
        $form = new Form;

        $form->addHidden('id');

        $form->addText('name', 'Název funkce:')
            ->setRequired('Prosím, zadejte název funkce.');

        $form->addText('name_en', 'Název funkce (EN):')
            ->setRequired(false);

        $form->addSubmit('save', 'Upravit funkci');

        $form->onSuccess[] = [$this, 'featureEditFormSucceeded'];
        return $form;
    }

        public function featureEditFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->updateFeature(
                (int)$values['id'],
                $values['name'],
                $values['name_en'] ?? null
            );
            $this->flashMessage('Funkce byla úspěšně upravena!', 'success');
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

   public function createComponentFeatureOptionForm(): Form
    {
        $form = new Form;

        $form->addSelect('feature_id', 'Funkce:', 
            $this->modelFacade->getFeatures()->fetchPairs('id', 'name'))
            ->setPrompt('Vyberte funkci')
            ->setRequired('Prosím, vyberte funkci.');

        $form->addText('name', 'Název varianty(ID):')
            ->setRequired('Prosím, zadejte název varianty.');

        $form->addText('name_cs', 'Název varianty (CZ):')
            ->setRequired('Prosím, zadejte název varianty v češtině.');

        $form->addText('name_en', 'Název varianty (EN):')
            ->setRequired('Prosím, zadejte název varianty v angličtině.');

        $form->addText('price', 'Cena (CZK):')
            ->setRequired('Prosím, zadejte cenu.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

        $form->addText('price_eur', 'Cena (EUR):')
            ->setRequired('Prosím, zadejte cenu v EUR.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

        $form->addText('mesh_name', 'Mesh Name:')
            ->setRequired(false);

        $form->addCheckbox('visible', 'Mesh je vidět při vybrání')
            ->setDefaultValue(true);
        
            $form->addCheckbox('allow_user_upload', 'Povolit nahrání souboru')
            ->setDefaultValue(false);

        $form->addSubmit('save', 'Přidat variantu');

        $form->onSuccess[] = [$this, 'featureOptionFormSucceeded'];
        return $form;
    }

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
                $this->modelFacade->addFeatureOption(
                    $values['feature_id'],
                    $values['name'],
                    (float)$values['price'],
                    (float)$values['price_eur'],
                    (bool)$values['allow_user_upload'],
                    $values['name_cs'],
                    $values['name_en'],
                    $values['mesh_name'] ?? null,
                    $values['visible'] ?? null
                );
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

        $form->addText('name_cs', 'Název varianty (CZ):')
            ->setRequired('Prosím, zadejte název varianty v češtině.');

        $form->addText('name_en', 'Název varianty (EN):')
            ->setRequired('Prosím, zadejte název varianty v angličtině.');

        $form->addText('price', 'Cena (CZK):')
            ->setRequired('Prosím, zadejte cenu.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

        $form->addText('price_eur', 'Cena (EUR):')
            ->setRequired('Prosím, zadejte cenu v EUR.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');

        $form->addCheckbox('allow_user_upload', 'Nahrát Soubor')
            ->setDefaultValue(false);

        $form->addText('mesh_name', 'Mesh Name:')
            ->setRequired(false);

        $form->addCheckbox('visible', 'Viditelnost')
            ->setDefaultValue(true);

        $form->addSubmit('save', 'Upravit variantu');

        $form->onSuccess[] = [$this, 'featureOptionEditFormSucceeded'];
        return $form;
    }

    public function featureOptionEditFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->modelFacade->updateFeatureOption(
                (int)$values['id'],
                $values['name'],
                (float)$values['price'],
                (float)$values['price_eur'],
                (bool)$values['allow_user_upload'],
                $values['name_cs'],
                $values['name_en'],
                $values['mesh_name'] ?? null,
                $values['visible'] ?? null
            );
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
                'name_cs' => $option->name_cs,
                'name_en' => $option->name_en,
                'price' => $option->price,
                'price_eur' => $option->price_eur,
                'allow_user_upload' => (bool)$option->allow_user_upload,
                'mesh_name' => $option->mesh_name,
                'visible' => $option->visible !== null ? (bool)$option->visible : true,
            ]);

            if ($this->isAjax()) {
                $this->redrawControl('featureOptionEditForm');
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

        public function handleEditFeature(int $featureId): void
    {
        $feature = $this->modelFacade->getFeature($featureId);
        if (!$feature) {
            $this->flashMessage('Funkce neexistuje.', 'error');
            $this->redirect('features');
        }

        $this['featureEditForm']->setDefaults([
            'id' => $feature->id,
            'name' => $feature->name,
            'name_en' => $feature->name_en,
        ]);

        if ($this->isAjax()) {
            $this->redrawControl('featureEditForm');
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
            $manufacturerId = $this->template->selectedManufacturerId ?? null;
            $this->template->models = $this->modelFacade->getModels($manufacturerId);
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
            $this->redrawControl('manufacturersTable');
            $this->redrawControl('manufacturersSelect');
            $this->redrawControl('modelsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('models');
        }
    }


    public function createComponentImageForm(): Form
    {
        $form = new Form;
        $form->addUpload('image', 'Obrázek:')
            ->setRequired('Prosím, vyberte obrázek.')
            ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF).')
            ->addRule(Form::MIME_TYPE, 'Soubor musí být JPEG, PNG nebo GIF.', ['image/jpeg', 'image/png', 'image/gif']);
            
        $form->addHidden('model_id');
        $form->addSubmit('save', 'Nahrát obrázek');
        $form->onSuccess[] = [$this, 'imageFormSucceeded'];
        return $form;
    }

    public function imageFormSucceeded(Form $form, array $values): void
    {
        $modelId = (int)$values['model_id'];
        try {
            $image = $this->modelFacade->addModelImage($modelId, $values['image']);
            if ($image) {
                $this->flashMessage('Obrázek byl úspěšně nahrán!', 'success');
            } else {
                $this->flashMessage('Nepodařilo se nahrát obrázek.', 'error');
            }
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->images = $this->modelFacade->getModelImages($modelId);
            $this->redrawControl('imagesList');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }

    public function handleDeleteImage(int $imageId): void
    {
        try {
            $this->modelFacade->deleteModelImage($imageId);
            $this->flashMessage('Obrázek byl úspěšně smazán.', 'success');
        } catch (\Exception $e) {
            $this->flashMessage('Nepodařilo se smazat obrázek: ' . $e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->redrawControl('imagesList');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }

    public function handleDeleteDefaultImage(int $imageId): void
    {
        try {
            $this->modelFacade->deleteDefaultImage($imageId);
            $this->flashMessage('Globální defaultní obrázek byl úspěšně smazán.', 'success');
        } catch (\Exception $e) {
            $this->flashMessage('Nepodařilo se smazat globální defaultní obrázek: ' . $e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->defaultImages = $this->modelFacade->getDefaultImages();
            $this->redrawControl('defaultImagesList');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('this');
        }
    }
    
    public function createComponentVendorForm(): Form
    {
        $form = new Form;
        $form->addText('name', 'Název dopravce:')
            ->setRequired('Prosím, zadejte název dopravce.');
        $form->addMultiSelect('supported_lang', 'Podporované jazyky:', [
            'cs' => 'Čeština',
            'en' => 'Angličtina',
        ])
            ->setRequired('Prosím, vyberte alespoň jeden jazyk.')
            ->setHtmlAttribute('multiple');
        $form->addSubmit('save', 'Přidat dopravce');
        $form->onSuccess[] = [$this, 'vendorFormSucceeded'];
        return $form;
    }

    public function vendorFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->orderFacade->addVendor($values['name'], $values['supported_lang']);
            $this->flashMessage('Dopravce byl úspěšně přidán!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->vendors = $this->orderFacade->getVendors();
            $this->redrawControl('vendorsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('shipping');
        }
    }

    public function createComponentVendorEditForm(): Form
    {
        $form = new Form;
        $form->addHidden('id');
        $form->addText('name', 'Název dopravce:')
            ->setRequired('Prosím, zadejte název dopravce.');
        $form->addMultiSelect('supported_lang', 'Podporované jazyky:', [
            'cs' => 'Čeština',
            'en' => 'Angličtina',
        ])
            ->setRequired('Prosím, vyberte alespoň jeden jazyk.')
            ->setHtmlAttribute('multiple');
        $form->addSubmit('save', 'Upravit dopravce');
        $form->onSuccess[] = [$this, 'vendorEditFormSucceeded'];
        return $form;
    }

    public function vendorEditFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->orderFacade->updateVendor((int)$values['id'], $values['name'], $values['supported_lang']);
            $this->flashMessage('Dopravce byl úspěšně upraven!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->vendors = $this->orderFacade->getVendors();
            $this->redrawControl('vendorsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('shipping');
        }
    }

public function handleEditVendor(int $vendorId): void
{
    $vendor = $this->orderFacade->getVendorById($vendorId);
    if (!$vendor) {
        $this->flashMessage('Dopravce neexistuje.', 'error');
        $this->redirect('shipping');
    }

    $supportedLang = $vendor->supported_lang ? explode(',', $vendor->supported_lang) : [];
    $this['vendorEditForm']->setDefaults([
        'id' => $vendor->id,
        'name' => $vendor->name,
        'supported_lang' => $supportedLang,
    ]);

    if ($this->isAjax()) {
        $this->redrawControl('vendorEditForm');
    }
}

    public function handleDeleteVendor(int $vendorId): void
    {
        try {
            $this->orderFacade->deleteVendor($vendorId);
            $this->flashMessage('Dopravce byl úspěšně smazán!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->vendors = $this->orderFacade->getVendors();
            $this->redrawControl('vendorsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('shipping');
        }
    }

public function createComponentShippingOptionForm(): Form
{
    $form = new Form;
    $lang = $this->template->lang ?? 'cs';
    $vendors = $this->orderFacade->getVendors($lang);
    $vendorOptions = array_combine(array_keys($vendors), array_values($vendors));
    $form->addSelect('vendor_id', 'Dopravce:', $vendorOptions)
        ->setRequired('Prosím, vyberte dopravce.');
    $form->addText('name', 'Název dopravy:')
        ->setRequired('Prosím, zadejte název dopravy tř. "Na adresu"');
    $form->addText('cost', 'Cena (CZK):')
        ->setRequired('Prosím, zadejte cenu.')
        ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
        ->setDefaultValue('0.00');
    $form->addText('cost_eur', 'Cena (EUR):')
        ->setRequired('Prosím, zadejte cenu v EUR.')
        ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
        ->setDefaultValue('0.00');
    $form->addSubmit('save', 'Způsob dopravy');
    $form->onSuccess[] = [$this, 'shippingOptionFormSucceeded'];
    return $form;
}

    public function shippingOptionFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->orderFacade->addShippingOption(
                $values['vendor_id'],
                $values['name'],
                (float)$values['cost'],
                (float)$values['cost_eur']
            );
            $this->flashMessage('Možnost dopravy byla úspěšně přidána!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->shippingOptions = $this->orderFacade->getAllShippingOptions();
            $this->redrawControl('shippingOptionsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('shipping');
        }
    }

    public function createComponentShippingOptionEditForm(): Form
{
    $form = new Form;
    $lang = $this->template->lang ?? 'cs';
    $form->addHidden('id');
    $vendors = $this->orderFacade->getVendors($lang);
    $vendorOptions = array_combine(array_keys($vendors), array_values($vendors));
    $form->addSelect('vendor_id', 'Dopravce:', $vendorOptions)
        ->setRequired('Prosím, vyberte dopravce.');
    $form->addText('name', 'Název možnosti dopravy:')
        ->setRequired('Prosím, zadejte název možnosti dopravy.');
    $form->addText('cost', 'Cena (CZK):')
        ->setRequired('Prosím, zadejte cenu.')
        ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
        ->setDefaultValue('0.00');
    $form->addText('cost_eur', 'Cena (EUR):')
        ->setRequired('Prosím, zadejte cenu v EUR.')
        ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
        ->setDefaultValue('0.00');
    $form->addSubmit('save', 'Upravit možnost dopravy');
    $form->onSuccess[] = [$this, 'shippingOptionEditFormSucceeded'];
    return $form;
}

public function shippingOptionEditFormSucceeded(Form $form, array $values): void
{
    try {
        $this->orderFacade->updateShippingOption(
            (int)$values['id'],
            $values['vendor_id'],
            $values['name'],
            (float)$values['cost'],
            (float)$values['cost_eur']
        );
        $this->flashMessage('Možnost dopravy byla úspěšně upravena!', 'success');
    } catch (\Exception $e) {
        $this->flashMessage($e->getMessage(), 'error');
    }

    if ($this->isAjax()) {
        $this->template->shippingOptions = $this->orderFacade->getAllShippingOptions();
        $this->redrawControl('shippingOptionsTable');
        $this->redrawControl('flashes');
    } else {
        $this->redirect('shipping');
    }
}

    public function handleEditShippingOption(int $shippingOptionId): void
    {
        $option = $this->orderFacade->getShippingOptionById($shippingOptionId);
        if (!$option) {
            $this->flashMessage('Možnost dopravy neexistuje.', 'error');
            $this->redirect('shipping');
        }

        $this['shippingOptionEditForm']->setDefaults([
            'id' => $option->id,
            'vendor_id' => $option->vendor_id,
            'name' => $option->name,
            'cost' => $option->cost,
            'cost_eur' => $option->cost_eur,
        ]);

        if ($this->isAjax()) {
            $this->redrawControl('shippingOptionEditForm');
        }
    }

    public function handleDeleteShippingOption(int $shippingOptionId): void
    {
        try {
            $this->orderFacade->deleteShippingOption($shippingOptionId);
            $this->flashMessage('Možnost dopravy byla úspěšně smazána!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->shippingOptions = $this->orderFacade->getAllShippingOptions();
            $this->redrawControl('shippingOptionsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('shipping');
        }
    }

    public function createComponentPaymentMethodForm(): Form
    {
        $form = new Form;
        $lang = $this->template->lang ?? 'cs';
        $form->addSelect('vendor_id', 'Dopravce:', $this->orderFacade->getVendors($lang))
            ->setRequired('Prosím, vyberte dopravce.');
        $form->addText('code', 'Kód platby:')
            ->setRequired('Prosím, zadejte kód platby, stejné jako název, bez diakritiky.')
            ->addRule($form::PATTERN, 'Kód může obsahovat pouze písmena, čísla a podtržítko.', '[a-zA-Z0-9_]+');
        $form->addText('name', 'Název platební metody:')
            ->setRequired('Prosím, zadejte název platební metody.');
        $form->addText('price', 'Cena (CZK):')
            ->setRequired('Prosím, zadejte cenu.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');
        $form->addText('price_eur', 'Cena (EUR):')
            ->setRequired('Prosím, zadejte cenu v EUR.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');
        $shippingOptions = $this->orderFacade->getAllShippingOptions($lang)->fetchAll();
        $shippingOptionsArray = [];
        foreach ($shippingOptions as $option) {
            $vendorName = $this->orderFacade->getVendorNameById($option->vendor_id, $lang);
            $shippingOptionsArray[$option->id] = "$vendorName: $option->name";
        }
        $form->addMultiSelect('shipping_option_ids', 'Platné u:', $shippingOptionsArray);
        $form->addSubmit('save', 'Přidat platební metodu');
        $form->onSuccess[] = [$this, 'paymentMethodFormSucceeded'];
        return $form;
    }

    public function paymentMethodFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->orderFacade->addPaymentMethod(
                $values['vendor_id'],
                $values['code'],
                $values['name'],
                (float)$values['price'],
                $values['shipping_option_ids'] ?? [],
                (float)$values['price_eur']
            );
            $this->flashMessage('Platební metoda byla úspěšně přidána!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->paymentMethods = $this->orderFacade->getAllPaymentMethods();
            $this->redrawControl('paymentMethodsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('shipping');
        }
    }

    public function createComponentPaymentMethodEditForm(): Form
    {
        $form = new Form;
        $lang = $this->template->lang ?? 'cs';
        $form->addHidden('id');
        $form->addSelect('vendor_id', 'Dopravce:', $this->orderFacade->getVendors($lang))
            ->setRequired('Prosím, vyberte dopravce.');
        $form->addText('code', 'Kód platby:')
            ->setRequired('Prosím, zadejte kód platby.')
            ->addRule($form::PATTERN, 'Kód může obsahovat pouze písmena, čísla a podtržítko.', '[a-zA-Z0-9_]+');
        $form->addText('name', 'Název platební metody:')
            ->setRequired('Prosím, zadejte název platební metody.');
        $form->addText('price', 'Cena (CZK):')
            ->setRequired('Prosím, zadejte cenu.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');
        $form->addText('price_eur', 'Cena (EUR):')
            ->setRequired('Prosím, zadejte cenu v EUR.')
            ->addRule($form::FLOAT, 'Cena musí být platné číslo.')
            ->setDefaultValue('0.00');
        $shippingOptions = $this->orderFacade->getAllShippingOptions($lang)->fetchAll();
        $shippingOptionsArray = [];
        foreach ($shippingOptions as $option) {
            $vendorName = $this->orderFacade->getVendorNameById($option->vendor_id, $lang);
            $shippingOptionsArray[$option->id] = "$vendorName: $option->name";
        }
        $form->addMultiSelect('shipping_option_ids', 'Možnosti dopravy:', $shippingOptionsArray);
        $form->addSubmit('save', 'Upravit platební metodu');
        $form->onSuccess[] = [$this, 'paymentMethodEditFormSucceeded'];
        return $form;
    }

    public function paymentMethodEditFormSucceeded(Form $form, array $values): void
    {
        try {
            $this->orderFacade->updatePaymentMethod(
                (int)$values['id'],
                $values['vendor_id'],
                $values['code'],
                $values['name'],
                (float)$values['price'],
                $values['shipping_option_ids'] ?? [],
                (float)$values['price_eur']
            );
            $this->flashMessage('Platební metoda byla úspěšně upravena!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->paymentMethods = $this->orderFacade->getAllPaymentMethods();
            $this->redrawControl('paymentMethodsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('shipping');
        }
    }

    public function handleEditPaymentMethod(int $paymentMethodId): void
    {
        $method = $this->orderFacade->getPaymentMethodById($paymentMethodId);
        if (!$method) {
            $this->flashMessage('Platební metoda neexistuje.', 'error');
            $this->redirect('shipping');
        }

        $this['paymentMethodEditForm']->setDefaults([
            'id' => $method->id,
            'vendor_id' => $method->vendor_id,
            'code' => $method->code,
            'name' => $method->name,
            'price' => $method->price,
            'price_eur' => $method->price_eur,
            'shipping_option_ids' => $this->orderFacade->getShippingOptionsForPaymentMethod($method->id),
        ]);

        if ($this->isAjax()) {
            $this->redrawControl('paymentMethodEditForm');
        }
    }

    public function handleDeletePaymentMethod(int $paymentMethodId): void
    {
        try {
            $this->orderFacade->deletePaymentMethod($paymentMethodId);
            $this->flashMessage('Platební metoda byla úspěšně smazána!', 'success');
        } catch (\Exception $e) {
            $this->flashMessage($e->getMessage(), 'error');
        }

        if ($this->isAjax()) {
            $this->template->paymentMethods = $this->orderFacade->getAllPaymentMethods();
            $this->redrawControl('paymentMethodsTable');
            $this->redrawControl('flashes');
        } else {
            $this->redirect('shipping');
        }
    } 

}