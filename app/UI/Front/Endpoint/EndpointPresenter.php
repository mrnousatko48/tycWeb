<?php
namespace App\UI\Front\Endpoint;

use Nette;
use App\Model\PageFacade;

class EndpointPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private PageFacade $pageFacade,
    ) {}

    public function actionManufacturers($typeId = null): void
    {
        $this->sendJson($this->pageFacade->getManufacturers($typeId));
        $this->terminate();
    }

    public function actionModels($manufacturerId): void
    {
        $this->sendJson($this->pageFacade->getModelsByManufacturer((int)$manufacturerId));
        $this->terminate();
    }
    
    public function actionModelColors($modelId): void
    {
        $this->sendJson($this->pageFacade->getColorsByModel((int)$modelId));
        $this->terminate();
    }

    public function actionModelFeatures($modelId): void
    {
        $this->sendJson($this->pageFacade->getFeaturesByModel((int)$modelId));
        $this->terminate();
    }

    public function actionModelPrice($modelId): void
    {
        $model = $this->pageFacade->getModelById((int)$modelId);
        $this->sendJson(['price' => $model ? (float)$model->price : 0.00]);
        $this->terminate();
    }
}