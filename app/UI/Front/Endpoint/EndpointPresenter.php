<?php
namespace App\UI\Front\Endpoint;

use Nette;
use App\Model\modelFacade;
use App\Model\OrderFacade;

class EndpointPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private ModelFacade $modelFacade,
        private OrderFacade $orderFacade
    ) {}

    public function actionManufacturers($typeId = null): void
    {
        $this->sendJson($this->modelFacade->getManufacturers($typeId));
        $this->terminate();
    }

    public function actionModels($manufacturerId): void
    {
        $this->sendJson($this->modelFacade->getModelsByManufacturer((int)$manufacturerId));
        $this->terminate();
    }
    
    public function actionModelColors($modelId): void
    {
        $this->sendJson($this->modelFacade->getColorsByModel((int)$modelId));
        $this->terminate();
    }

    public function actionModelFeatures($modelId): void
    {
        $this->sendJson($this->modelFacade->getFeaturesByModel((int)$modelId));
        $this->terminate();
    }

    public function actionModelPrice($modelId): void
    {
        $model = $this->modelFacade->getModelById((int)$modelId);
        $this->sendJson(['price' => $model ? (float)$model->price : 0.00]);
        $this->terminate();
    }

public function actionModelImages($modelId): void
{
    if ($modelId && $this->modelFacade->getModelById((int)$modelId)) {
        $images = $this->modelFacade->getImagesByModel((int)$modelId);
    } else {
        $defaultImages = $this->modelFacade->getDefaultImages();
        $images = [];
        foreach ($defaultImages as $image) {
            $images[] = [
                'image_path' => $image->image_path,
                'alt_text' => 'Default Product Image ' . $image->id
            ];
        }
    }
    $this->sendJson($images);
    $this->terminate();
}

public function actionShippingOptions($vendor): void
{
    if (!$vendor || !is_numeric($vendor)) {
        $this->sendJson([]); // Return empty array for invalid vendor
        return;
    }
    $shippingOptions = $this->orderFacade->getShippingOptionsByVendor((int)$vendor);
    \Tracy\Debugger::barDump($shippingOptions, "Shipping Options JSON for Vendor: $vendor"); // Debug output
    $this->sendJson($shippingOptions);
}
public function actionPaymentMethods($vendor): void
{
    if (!$vendor || !is_numeric($vendor)) {
        $this->sendJson([]);
        return;
    }
    $paymentMethods = $this->orderFacade->getPaymentMethodsByVendor((int)$vendor);
    \Tracy\Debugger::barDump($paymentMethods, 'Payment Methods JSON for Vendor: ' . $vendor);
    $this->sendJson($paymentMethods);
}
}