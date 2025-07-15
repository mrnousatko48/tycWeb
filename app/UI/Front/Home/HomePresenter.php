<?php
namespace App\UI\Front\Home;

use Nette;

class HomePresenter extends Nette\Application\UI\Presenter
{
    public function renderDefault(): void
    {
        $this->template->gltfPath = '/www/uploads/test/bez.gltf';
    }
}
