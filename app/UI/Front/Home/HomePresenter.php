<?php
declare(strict_types=1);

namespace App\UI\Front\Home;

use Nette\Application\UI\Form;
use App\UI\Front\BaseFrontPresenter;
use App\Model\ModelFacade;

final class HomePresenter extends BaseFrontPresenter
{
    private ModelFacade $modelFacade;

    public function injectModelFacade(ModelFacade $modelFacade): void
    {
        $this->modelFacade = $modelFacade;
    }

    public function renderDefault(): void
    {
        $this->template->banner = $this->pageFacade->getSectionContent('banner');
        $this->template->durability = $this->pageFacade->getSectionContent('durability');
        $this->template->customizations = $this->pageFacade->getCustomizations();
        $this->template->gallery = $this->pageFacade->getGalleryImages();
        $this->template->contact = $this->pageFacade->getContactInfo();
    }

    public function renderConfigurator(): void
    {
        $this->template->manufacturers = $this->modelFacade->getManufacturers();
    }

    public function renderLegal(string $section): void
    {
        $page = $this->pageFacade->getLegalPage($section);
        if (!$page) {
            $this->error('Stránka nenalezena', 404);
        }

        $titles = [
            'obchodni-podminky' => 'OPNX3D | Obchodní podmínky',
            'ochrana-osobnich-udaju' => 'OPNX3D | Ochrana osobních údajů',
            'reklamacni-rad' => 'OPNX3D | Reklamační řád',
            'odstoupeni-od-smlouvy' => 'OPNX3D | Odstoupení od smlouvy',
        ];

        $this->template->title = $titles[$section] ?? 'OPNX3D | Právní informace';
        $this->template->page = $page;
    }

protected function createComponentCaseForm(): Form
{
    $form = new Form;
    $form->setHtmlAttribute('enctype', 'multipart/form-data');

    $manufacturers = $this->modelFacade->getManufacturers();
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
        ->setHtmlAttribute('data-images-url', $this->link('Endpoint:modelImages', ['modelId' => '#']))
        ->setRequired('Prosím vyberte model.');

    $form->addHidden('color')->setRequired('Prosím vyberte barvu.');
    $form->addHidden('features')->setDefaultValue('{}');
    $form->addHidden('total_price')->setDefaultValue('0.00');
    
    // Add file upload field (still present for compatibility)
    $form->addUpload('user_file', 'Upload your 3D file:')
        ->setRequired(false); // No validation rules here; we'll handle it via AJAX

    // Add a hidden field to store the uploaded file ID
    $form->addHidden('user_upload_id')->setDefaultValue(null);

    $form->addSubmit('submit', 'Přidat do košíku');

    $form->onAnchor[] = function () use ($model, $manufacturer) {
        $model->setItems(
            $manufacturer->getValue()
                ? $this->modelFacade->getModelsByManufacturer((int)$manufacturer->getValue())
                : []
        );
    };

    $form->onSuccess[] = [$this, 'processForm'];

    return $form;
}

    public function processForm(Form $form): void
{
    $values = $form->getValues();
    try {
        $manufacturerId = (int)$values['manufacturer'];
        $modelId = (int)$values['model'];
        $color = $values['color'];
        $totalPrice = (float)$values['total_price'];
        $features = json_decode($values['features'], true);
        $userUploadId = $values['user_upload_id'] ? (int)$values['user_upload_id'] : null;

        if (!$manufacturerId || !$modelId || !$color) {
            throw new \Exception('Missing required fields: manufacturer, model, or color.');
        }

        if (!is_array($features)) {
            throw new \Exception('Invalid features format.');
        }

        $manufacturerName = $this->modelFacade->getManufacturerNameById($manufacturerId);
        $modelName = $this->modelFacade->getModelNameById($modelId);

        if (!$manufacturerName || !$modelName) {
            throw new \Exception('Invalid manufacturer or model selected.');
        }

        $caseData = [
            'manufacturer' => $manufacturerName,
            'model' => $modelName,
            'color' => $color,
            'total_price' => $totalPrice,
            'features' => $values['features'],
        ];

        if ($userUploadId) {
            $caseData['user_upload_id'] = $userUploadId;
        }

        $userId = $this->getUser()->isLoggedIn() ? $this->getUser()->getId() : null;
        $this->orderFacade->createCase($caseData, $userId);

        $this->flashMessage('Položka byla přidána do košíku.', 'success');
        $this->redirect('Cart:default');
    } catch (\Exception $e) {
        error_log('Error in processForm: ' . $e->getMessage());
        $this->flashMessage('Chyba při přidávání do košíku: ' . $e->getMessage(), 'error');
        $this->redirect('Cart:default');
    }
}

    public function renderGallery(): void
    {
        $this->template->gallery = $this->pageFacade->getGalleryImages();
    }

    public function actionUploadFile(): void
{
    if (!$this->isAjax()) {
        $this->error('This endpoint accepts only AJAX requests.', 400);
    }

    $request = $this->getHttpRequest();
    $file = $request->getFile('user_file');

    if (!$file || !$file->isOk()) {
        $this->sendJson([
            'success' => false,
            'error' => $file ? 'File upload failed: ' . $file->getError() : 'No file uploaded.',
        ]);
    }

    try {
        // Assuming modelId is not strictly required for upload; adjust as needed
        $modelId = $request->getPost('model') ? (int)$request->getPost('model') : null;
        $upload = $this->modelFacade->addUserUpload($modelId, $file, $file->getSanitizedName());
        
        if ($upload) {
            $this->sendJson([
                'success' => true,
                'upload_id' => $upload->id,
            ]);
        } else {
            throw new \Exception('Failed to process file upload.');
        }
    } catch (\Exception $e) {
        $this->sendJson([
            'success' => false,
            'error' => 'Upload error: ' . $e->getMessage(),
        ]);
    }
}
}