<?php
namespace App\UI\Front\Endpoint;

use Nette;
use App\Model\RepairFacade;

class EndpointPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private RepairFacade $repairFacade,
	) {}

    public function actionManufacturers($typeId): void
    {
        $manufacturers = $this->repairFacade->getManufacturersByType($typeId);
        $this->sendJson($manufacturers);
    }

    public function actionModels($manufacturerId): void
    {

        $models = $this->repairFacade->getModelsByManufacturer($manufacturerId);
        bdump($models); // Debug the output
        $this->sendJson($models);
    }

    public function actionFaults($faultId): void
    {
        $faults = $this->repairFacade->getFaults($faultId);
        $this->sendJson($faults);
    }


}

