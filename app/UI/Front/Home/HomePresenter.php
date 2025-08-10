<?php
declare(strict_types=1);

namespace App\UI\Front\Home;

use Nette\Application\UI\Form;
use App\UI\Front\BaseFrontPresenter;
use App\Model\ModelFacade;
use Nette\Application\AbortException;

final class HomePresenter extends BaseFrontPresenter
{
    private ModelFacade $modelFacade;

    public function injectModelFacade(ModelFacade $modelFacade): void
    {
        $this->modelFacade = $modelFacade;
    }

    public function renderDefault(): void
    {
        $this->pageFacade->setLang($this->template->lang);        
        $this->template->banner = $this->pageFacade->getSectionContent('banner');
        $this->template->durability = $this->pageFacade->getSectionContent('durability');
        $this->template->customizations = $this->pageFacade->getCustomizations();
        $this->template->gallery = $this->pageFacade->getGalleryImages();
        $this->template->contact = $this->pageFacade->getContactInfo();
    }

public function renderConfigurator(): void
{
    $this->template->manufacturers = $this->modelFacade->getManufacturers();
    $lang = $this->getParameter('lang', 'en'); // Get the current language from the URL
    $this->template->colorsUrl = $this->link('Endpoint:modelColors', ['lang' => $lang]) . '?modelId=#';
    $this->template->featuresUrl = $this->link('Endpoint:modelFeatures', ['lang' => $lang]) . '?modelId=#';
    $this->template->priceUrl = $this->link('Endpoint:modelPrice', ['lang' => $lang]) . '?modelId=#';
    $this->template->imagesUrl = $this->link('Endpoint:modelImages', ['lang' => $lang]) . '?modelId=#';
    $this->template->uploadUrl = $this->link('uploadFile');
    $this->template->defaultImagesUrl = $this->link('Endpoint:modelImages', ['lang' => $lang]);
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
        $form->setTranslator($this->translator);

        $manufacturers = $this->modelFacade->getManufacturers();
        $manufacturerItems = [];
        foreach ($manufacturers as $manufacturer) {
            $manufacturerItems[$manufacturer->id] = $manufacturer->name;
        }

        $manufacturer = $form->addSelect('manufacturer', 'form.manufacturer', $manufacturerItems)
            ->setPrompt('form.select_manufacturer')
            ->setHtmlAttribute('data-url', $this->link('Endpoint:manufacturers'))
            ->setRequired('Prosím vyberte výrobce.');

        $model = $form->addSelect('model', 'form.model')
            ->setPrompt('form.select_model')
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
        
        $form->addUpload('user_file', 'Upload your 3D file:')
            ->setRequired(false);

        $form->addHidden('user_upload_id')->setDefaultValue(null);

        $form->addHidden('lang')->setDefaultValue($this->getParameter('lang', 'en'));

        $form->addSubmit('submit', 'form.submit')
            ->setHtmlAttribute('id', 'frm-caseForm-submit');

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
        error_log('Form values: ' . print_r($values, true));

        try {
            $manufacturerId = (int)$values['manufacturer'];
            $modelId = (int)$values['model'];
            $color = $values['color'];
            $features = json_decode($values['features'], true);
            $userUploadId = isset($values['user_upload_id']) && $values['user_upload_id']
                ? (int)$values['user_upload_id']
                : null;
            $lang = $values['lang'] ?? 'en';

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

            // Calculate prices using existing ModelFacade methods
            $model = $this->modelFacade->getModelById($modelId);
            if (!$model) {
                throw new \Exception("Model ID $modelId not found.");
            }
            $basePriceCzk = (float)$model->price;
            $basePriceEur = (float)$model->price_eur;

            $featurePriceCzk = 0.00;
            $featurePriceEur = 0.00;
            $availableFeatures = $this->modelFacade->getFeaturesByModel($modelId, $lang);

            foreach ($features as $featureId => $featureName) {
                foreach ($availableFeatures as $featureGroup => $options) {
                    foreach ($options as $option) {
                        if ($option['name'] === $featureName) {
                            $featurePriceCzk += (float)$option['price'];
                            $featurePriceEur += (float)$option['price_eur'];
                            break;
                        }
                    }
                }
            }

            $totalPriceCzk = $basePriceCzk + $featurePriceCzk;
            $totalPriceEur = $basePriceEur + $featurePriceEur;

            $caseData = [
                'manufacturer' => $manufacturerName,
                'model' => $modelName,
                'color' => $color,
                'total_price' => $totalPriceCzk,
                'total_price_eur' => $totalPriceEur,
                'features' => $values['features'],
                'user_upload_id' => $userUploadId,
            ];

            error_log('Creating case with user_upload_id: ' . ($userUploadId ?? 'NULL'));

            $userId = $this->getUser()->isLoggedIn() ? $this->getUser()->getId() : null;
            $this->orderFacade->createCase($caseData, $userId);

            $this->flashMessage('Položka byla přidána do košíku.', 'success');
            error_log('Redirecting after success.');
            $this->redirect('Cart:default', ['lang' => $lang]);
            return;

        } catch (AbortException $e) {
            throw $e;

        } catch (\Exception $e) {
            $this->flashMessage('Chyba při přidávání do košíku: ' . $e->getMessage(), 'error');
            error_log('Redirecting after error: ' . $e->getMessage());
            $this->redirect('Cart:default', ['lang' => $values['lang'] ?? 'en']);
            return;
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

        try {
            $file = $this->getHttpRequest()->getFile('user_file');
            if (!$file || !$file->isOk()) {
                throw new \RuntimeException('File upload failed: ' . ($file ? $file->getError() : 'No file uploaded.'));
            }

            $upload = $this->modelFacade->addUserUpload($file, $file->getSanitizedName());
            if (!$upload || !isset($upload->id)) {
                throw new \RuntimeException('Failed to process file upload: missing ID');
            }

            $this->sendJson(['success' => true, 'upload_id' => $upload->id]);
            $this->terminate();

        } catch (AbortException $e) {
            throw $e;

        } catch (\Throwable $e) {
            error_log("upload error: " . $e->getMessage());
            $this->sendJson([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
            $this->terminate();
        }
    }
}