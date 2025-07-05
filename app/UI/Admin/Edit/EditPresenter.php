<?php
declare(strict_types=1);

namespace App\UI\Admin\Edit;

use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use App\Model\PageFacade;
use App\Utils\ImageUploader;

/**
 * LandingPresenter provides admin pages for editing landing page sections.
 */
final class EditPresenter extends Presenter
{
    private PageFacade $pageFacade;

    public function __construct(PageFacade $pageFacade)
    {
        parent::__construct();
        $this->pageFacade = $pageFacade;
    }

    protected function startup(): void
    {
        parent::startup();
        if (!$this->user->isLoggedIn()) {
            $this->flashMessage('Nemáš přístup🚫', 'danger');
            $this->redirect(':Front:Home:default');
        }
    }

    public function renderDefault(): void
    {
        // Default dashboard view with navigation buttons
    }

    public function renderBanner(): void
    {
        $this->template->banner = $this->pageFacade->getBannerSection();
        $this->template->setFile(__DIR__ . '/Templates/banner.latte');
    }

    public function renderDurability(): void
    {
        $this->template->durability = $this->pageFacade->getDurabilitySection();
        $this->template->setFile(__DIR__ . '/Templates/durability.latte');
    }

    public function renderCustomization(): void
    {
        $id = $this->getParameter('id');
        if ($id) {
            $this->template->feature = $this->pageFacade->getCustomizationFeature((int)$id);
        }
        $this->template->customization = $this->pageFacade->getCustomizationSection();
        $this->template->setFile(__DIR__ . '/Templates/customization.latte');
    }

    public function renderGallery(): void
    {
        $this->template->galleryImages = $this->pageFacade->getGalleryImages();
        $this->template->setFile(__DIR__ . '/Templates/gallery.latte');
    }

    public function renderContact(): void
    {
        $this->template->contact = $this->pageFacade->getContactInfo();
        $this->template->setFile(__DIR__ . '/Templates/contact.latte');
    }

    public function actionDeleteGalleryImage(int $id): void
    {
        $this->pageFacade->deleteGalleryImage($id);
        $this->flashMessage('Obrázek byl úspěšně odstraněn.', 'success');
        $this->redirect('gallery');
    }

    private function createEditForm(
        object $entity,
        array $fields,
        callable $updateCallback,
        string $flashMessage,
        string $redirectDestination
    ): Form {
        $form = new Form;
        foreach ($fields as $name => $config) {
            $method = 'add' . ucfirst($config['type']);
            $field = $form->$method($name, $config['label'] ?? '');
            
            if (array_key_exists('default', $config)) {
                $field->setDefaultValue($config['default']);
            } else {
                $field->setDefaultValue($entity->{$name} ?? '');
            }
            
            if (isset($config['htmlType'])) {
                $field->setHtmlType($config['htmlType']);
            }
            
            if (!empty($config['required'])) {
                $field->setRequired();
            }
            
            if ($config['type'] !== 'hidden') {
                $field->getControlPrototype()->addClass('form-control');
            }
        }
        
        $form->addSubmit('save', 'Uložit')
             ->getControlPrototype()->addClass('btn btn-primary');
        
        $form->onSuccess[] = function (Form $form, $values) use ($entity, $updateCallback, $flashMessage, $redirectDestination): void {
            $updateCallback($entity->id ?? null, (array)$values);
            $this->flashMessage($flashMessage, 'success');
            $this->redirect($redirectDestination);
        };
    
        return $form;
    }

    public function createComponentBannerForm(): Form
    {
        $banner = $this->pageFacade->getBannerSection();
        $fields = [
            'title'       => ['type' => 'text',     'label' => 'Nadpis:',       'required' => true],
            'description' => ['type' => 'textArea', 'label' => 'Popis:',        'required' => true],
            'button_text' => ['type' => 'text',     'label' => 'Text tlačítka:', 'required' => true],
            'button_link' => ['type' => 'text',     'label' => 'Odkaz tlačítka:', 'required' => true],
        ];

        $form = $this->createEditForm(
            (object)$banner,
            $fields,
            function ($id, $values) use ($banner) {
                $image = $values['image'];
                $values['image_path'] = ImageUploader::uploadImage($image, 'Uploads/banner', $banner['image_path'] ?? null);
                $this->pageFacade->updateBannerSection($id, $values);
            },
            'Banner sekce byla úspěšně aktualizována.',
            'Editbanner'
        );

        $form->addUpload('image', 'Obrázek pozadí:')
             ->setHtmlAttribute('class', 'form-control')
             ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).');

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        return $form;
    }

    public function createComponentDurabilityForm(): Form
    {
        $durability = $this->pageFacade->getDurabilitySection();
        $fields = [
            'title'        => ['type' => 'text',     'label' => 'Nadpis:',       'required' => true],
            'description1' => ['type' => 'textArea', 'label' => 'Popis 1:',      'required' => true],
            'description2' => ['type' => 'textArea', 'label' => 'Popis 2:',      'required' => false],
        ];

        $form = $this->createEditForm(
            (object)$durability,
            $fields,
            function ($id, $values) use ($durability) {
                $image = $values['image'];
                $values['image_path'] = ImageUploader::uploadImage($image, 'Uploads/durability', $durability['image_path'] ?? null);
                $this->pageFacade->updateDurabilitySection($id, $values);
            },
            'Sekce Odolnost byla úspěšně aktualizována.',
            'Editdurability'
        );

        $form->addUpload('image', 'Obrázek:')
             ->setHtmlAttribute('class', 'form-control')
             ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).');

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        return $form;
    }

    public function createComponentCustomizationForm(): Form
    {
        $id = (int)$this->getParameter('id');
        $feature = $id ? $this->pageFacade->getCustomizationFeature($id) : null;
        if (!$feature && $id) {
            $this->error('Funkce nenalezena');
        }

        $fields = [
            'title'       => ['type' => 'text',     'label' => 'Název funkce:', 'required' => true],
            'description' => ['type' => 'textArea', 'label' => 'Popis:',        'required' => true],
        ];

        $form = $this->createEditForm(
            $feature ?? (object)['id' => null],
            $fields,
            function ($id, $values) use ($feature) {
                $image = $values['image'];
                $values['image_path'] = ImageUploader::uploadImage($image, 'Uploads/customization', $feature->image_path ?? null);
                if ($id) {
                    $this->pageFacade->updateCustomizationFeature($id, $values);
                } else {
                    $this->pageFacade->addCustomizationFeature($values);
                }
            },
            $id ? 'Funkce byla aktualizována.' : 'Funkce byla přidána.',
            'Editcustomization'
        );

        $form->addHidden('id')->setDefaultValue($feature->id ?? null);
        $form->addUpload('image', 'Obrázek:')
             ->setHtmlAttribute('class', 'form-control')
             ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).');

        $form->addText('button_text', 'Text tlačítka:')
             ->setHtmlAttribute('class', 'form-control');
        $form->addText('button_link', 'Odkaz tlačítka:')
             ->setHtmlAttribute('class', 'form-control');

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        $form->setAction($this->link('Editcustomization', ['id' => $feature->id ?? null]));
        return $form;
    }

    public function createComponentContactForm(): Form
    {
        $contact = $this->pageFacade->getContactInfo();
        $fields = [
            'name'      => ['type' => 'text',     'label' => 'Jméno:',      'required' => true],
            'address'   => ['type' => 'text',     'label' => 'Adresa:',     'required' => true],
            'ico'       => ['type' => 'text',     'label' => 'IČO:',        'required' => true],
            'phone'     => ['type' => 'text',     'label' => 'Telefon:',    'required' => true],
            'email'     => ['type' => 'text',     'label' => 'Email:',      'required' => true],
            'map_embed' => ['type' => 'textArea', 'label' => 'Kód vložení mapy:', 'required' => true],
        ];

        return $this->createEditForm(
            $contact,
            $fields,
            fn($id, $values) => $this->pageFacade->updateContactInfo($id, $values),
            'Kontaktní informace byly úspěšně aktualizovány.',
            'Editcontact'
        );
    }

    public function createComponentGalleryForm(): Form
    {
        $form = new Form;

        $form->addUpload('image', 'Obrázek:')
             ->setRequired('Vyberte obrázek.')
             ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).')
             ->setHtmlAttribute('class', 'form-control');

        $form->addText('alt_text', 'Alternativní text:')
             ->setRequired('Zadejte alternativní text.')
             ->setHtmlAttribute('class', 'form-control');

        $form->addText('ordering', 'Pořadí:')
             ->setRequired('Zadejte pořadí.')
             ->setHtmlType('number')
             ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('save', 'Nahrát obrázek')
             ->getControlPrototype()->addClass('btn btn-primary');

        $form->onSuccess[] = function (Form $form, $values) {
            try {
                $image = $values->image;
                $imagePath = ImageUploader::uploadImage($image, 'Uploads/gallery', null);
                if (!$imagePath && $image->isOk()) {
                    throw new \Exception('Nepodařilo se nahrát obrázek.');
                }

                $this->pageFacade->addGalleryImage(
                    $imagePath,
                    $values->alt_text,
                    (int)$values->ordering
                );

                $this->flashMessage('Obrázek byl úspěšně nahrán.', 'success');
            } catch (\Exception $e) {
                $this->flashMessage('Nastala chyba při nahrávání obrázku: ' . $e->getMessage(), 'danger');
            }

            $this->redirect('gallery');
        };

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        return $form;
    }

    public function createComponentGalleryOrderForm(): Form
    {
        $form = new Form;

        $form->addHidden('order')
             ->setRequired('Pořadí obrázků není definováno.');

        $form->addSubmit('save', 'Uložit pořadí')
             ->getControlPrototype()->addClass('btn btn-primary');

        $form->onSuccess[] = function (Form $form, $values) {
            try {
                $order = json_decode($values->order, true);
                if (!is_array($order)) {
                    throw new \Exception('Neplatné pořadí obrázků.');
                }
                $this->pageFacade->updateGalleryOrder($order);
                $this->flashMessage('Pořadí obrázků bylo úspěšně aktualizováno.', 'success');
            } catch (\Exception $e) {
                $this->flashMessage('Nastala chyba při ukládání pořadí: ' . $e->getMessage(), 'danger');
            }
            $this->redirect('gallery');
        };

        return $form;
    }
}