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
    }

    public function actionModels($manufacturerId): void
    {
        $this->sendJson($this->pageFacade->getModelsByManufacturer((int)$manufacturerId));
    }
    
    public function actionModelColors($modelId): void
    {
        $this->sendJson($this->pageFacade->getColorsByModel((int)$modelId));
    }

    public function actionModelFeatures($modelId): void
    {
        $this->sendJson($this->pageFacade->getFeaturesByModel((int)$modelId));
    }
}