<?php
declare(strict_types=1);

namespace App\UI\Admin\Edit;

use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use App\Model\PageFacade;
use Nette\Http\FileUpload;
use App\Utils\ImageUploader;

/**
 * EditPresenter provides admin pages for editing landing page sections.
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

    /**
     * Default dashboard view with navigation buttons.
     */
    public function renderDefault(): void
    {
        $this->template->setFile(__DIR__ . '/Templates/default.latte');
    }

    public function renderLogos(): void
    {
        $this->template->logos = $this->pageFacade->getLogos();
        $this->template->setFile(__DIR__ . '/Templates/logos.latte');
    }

    /**
     * Render the Banner Section edit page.
     */
    public function renderBanner(): void
    {
        $this->template->banner = $this->pageFacade->getBannerSection();
        $this->template->setFile(__DIR__ . '/Templates/banner.latte');
    }

    /**
     * Render the Durability Section edit page.
     */
    public function renderDurability(): void
    {
        $this->template->durability = $this->pageFacade->getDurabilitySection();
        $this->template->setFile(__DIR__ . '/Templates/durability.latte');
    }

    /**
     * Render the Customization Section edit page.
     */
    public function renderCustomization(): void
    {
        $this->template->customizations = $this->pageFacade->getCustomizations();
        $this->template->setFile(__DIR__ . '/Templates/customization.latte');
    }

    /**
     * Render the Gallery Section edit page.
     */
    public function renderGallery(): void
    {
        $this->template->galleryImages = $this->pageFacade->getGalleryImages();
        $this->template->setFile(__DIR__ . '/Templates/gallery.latte');
    }

    /**
     * Render the Contact Section edit page.
     */
    public function renderContact(): void
    {
        $this->template->contact = $this->pageFacade->getContactInfo();
        $this->template->setFile(__DIR__ . '/Templates/contact.latte');
    }

    /**
     * Action to delete a gallery image.
     */
    public function actionDeleteGalleryImage(int $id): void
    {
        $this->pageFacade->deleteGalleryImage($id);
        $this->flashMessage('Obrázek byl úspěšně odstraněn.', 'success');
        $this->redirect('gallery');
    }

    /**
     * Action to delete a customization.
     */
    public function actionDeleteCustomization(int $id): void
    {
        $this->pageFacade->deleteCustomization($id);
        $this->flashMessage('Funkce byla úspěšně odstraněna.', 'success');
        $this->redirect('customization');
    }

    /**
     * Helper method to build an edit form.
     */
    private function createEditForm(
        object $entity,
        array $fields,
        callable $updateCallback,
        string $flashMessage,
        string $redirectDestination
    ): Form
    {
        $form = new Form;
        foreach ($fields as $name => $config) {
            $method = 'add' . ucfirst($config['type']);
            $field = $form->$method($name, $config['label'] ?? '');
            if (array_key_exists('default', $config)) {
                $field->setDefaultValue($config['default']);
            } else {
                $field->setDefaultValue($entity->$name ?? '');
            }
            if (!empty($config['required'])) {
                $field->setRequired();
            }
            if ($config['type'] !== 'hidden' && $config['type'] !== 'upload') {
                $field->getControlPrototype()->addClass('form-control');
            }
        }
        $form->addSubmit('save', 'Uložit')
             ->getControlPrototype()->addClass('btn btn-primary');
        $form->onSuccess[] = function (Form $form, $values) use ($updateCallback, $flashMessage, $redirectDestination): void {
            $updateCallback((array)$values);
            $this->flashMessage($flashMessage, 'success');
            $this->redirect($redirectDestination);
        };
        return $form;
    }

    /**
     * Create a form to edit the Banner Section.
     */
    /**
 * Create a form to edit the Banner Section.
 */
public function createComponentBannerForm(): Form
{
    $banner = $this->pageFacade->getBannerSection();
    $fields = [
        'title' => ['type' => 'text', 'label' => 'Nadpis:', 'required' => true],
        'description' => ['type' => 'textArea', 'label' => 'Popis:', 'required' => true],
        'button_text' => ['type' => 'text', 'label' => 'Text tlačítka:', 'required' => true],
        'button_link' => ['type' => 'text', 'label' => 'Odkaz tlačítka:', 'required' => true],
        'image' => [
            'type' => 'upload',
            'label' => 'Obrázek:',
            'required' => false,
        ],
    ];

    $form = $this->createEditForm(
        (object)$banner,
        $fields,
        function ($values) {
            $this->pageFacade->updateBannerSection((array)$values);
            $this->flashMessage('Banner byl úspěšně aktualizován.', 'success');
            $this->redirect('Edit:banner');
        },
        'Banner byl úspěšně aktualizován.',
        'Edit:banner'
    );

    $form['image']
        ->setHtmlAttribute('class', 'form-control')
        ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).');

    $form->getElementPrototype()->enctype = 'multipart/form-data';
    return $form;
}

    /**
     * Create a form to edit the Durability Section.
     */
    public function createComponentDurabilityForm(): Form
    {
        $durability = $this->pageFacade->getDurabilitySection();
        $fields = [
            'title' => ['type' => 'text', 'label' => 'Nadpis:', 'required' => true],
            'description1' => ['type' => 'textArea', 'label' => 'Popis 1:', 'required' => true],
            'description2' => ['type' => 'textArea', 'label' => 'Popis 2:', 'required' => true],
            'image' => [
                'type' => 'upload',
                'label' => 'Obrázek:',
                'required' => false,
            ],
        ];

    $form = $this->createEditForm(
        (object)$durability,
        $fields,
        function ($values) {
            $this->pageFacade->updateDurabilitySection((array)$values);
            $this->flashMessage('Změny byly uloženy', 'success');
            $this->redirect('Edit:banner');
        },
        'Změny byly uloženy',
        'Edit:banner'
    );

        $form['image']
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).');

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        return $form;
    }

    /**
     * Create a form to add a new customization.
     */
    public function createComponentAddCustomizationForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Název funkce:')
             ->setRequired('Zadejte název funkce.')
             ->setHtmlAttribute('class', 'form-control');
        $form->addTextArea('description', 'Popis funkce:')
             ->setRequired('Zadejte popis funkce.')
             ->setHtmlAttribute('class', 'form-control');
        $form->addUpload('image', 'Obrázek:')
             ->setRequired('Vyberte obrázek.')
             ->setHtmlAttribute('class', 'form-control')
             ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).');
        $form->addSubmit('save', 'Přidat funkci')
             ->getControlPrototype()->addClass('btn btn-primary');

        $form->onSuccess[] = function (Form $form, $values) {
            try {
                $this->pageFacade->addCustomization($values->title, $values->description, $values->image);
                $this->flashMessage('Funkce byla úspěšně přidána.', 'success');
            } catch (\Exception $e) {
                $this->flashMessage('Chyba při přidávání funkce: ' . $e->getMessage(), 'danger');
            }
            $this->redirect('customization');
        };

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        return $form;
    }

    /**
     * Create a form to add/edit Gallery images.
     */
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
                $this->pageFacade->addGalleryImage((array)$values);
                $this->flashMessage('Obrázek byl úspěšně nahrán.', 'success');
            } catch (\Exception $e) {
                $this->flashMessage('Nastala chyba při nahrávání obrázku: ' . $e->getMessage(), 'danger');
            }
            $this->redirect('gallery');
        };

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        return $form;
    }

    /**
     * Create a form to update gallery order.
     */
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

    /**
     * Create a form to edit the Contact Information.
     */
    public function createComponentContactForm(): Form
    {
        $contact = $this->pageFacade->getContactInfo();
        $fields = [
            'name' => ['type' => 'text', 'label' => 'Jméno:', 'required' => true],
            'address' => ['type' => 'text', 'label' => 'Adresa:', 'required' => true],
            'ico' => ['type' => 'text', 'label' => 'IČO:', 'required' => true],
            'phone' => ['type' => 'text', 'label' => 'Telefon:', 'required' => true],
            'email' => ['type' => 'email', 'label' => 'Email:', 'required' => true],
            'map_embed' => ['type' => 'textArea', 'label' => 'Kód mapy:', 'required' => true],
        ];

        $form = $this->createEditForm(
            $contact ?? (object)[
                'name' => '',
                'address' => '',
                'ico' => '',
                'phone' => '',
                'email' => '',
                'map_embed' => ''
            ],
            $fields,
            function ($values) use ($contact) {
                $id = $contact ? $contact->id : 1;
                $this->pageFacade->updateContactInfo($id, (array)$values);
            },
            'Kontaktní informace byly úspěšně aktualizovány.',
            'Edit:contact'
        );

        return $form;
    }

    public function createComponentLogoForm(): Form
    {
        $form = new Form;
        $form->addUpload('logo_light', 'Logo pro světlý režim:')
             ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).')
             ->setHtmlAttribute('class', 'form-control');
        $form->addUpload('logo_dark', 'Logo pro tmavý režim:')
             ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).')
             ->setHtmlAttribute('class', 'form-control');
        $form->addSubmit('save', 'Uložit loga')
             ->getControlPrototype()->addClass('btn btn-primary');

        $form->onSuccess[] = function (Form $form, $values) {
            try {
                if ($values->logo_light->isOk()) {
                    $this->pageFacade->updateLogo('light', $values->logo_light);
                }
                if ($values->logo_dark->isOk()) {
                    $this->pageFacade->updateLogo('dark', $values->logo_dark);
                }
                $this->flashMessage('Loga byla úspěšně aktualizována.', 'success');
            } catch (\Exception $e) {
                $this->flashMessage('Chyba při ukládání log: ' . $e->getMessage(), 'danger');
            }
            $this->redirect('logos');
        };

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        return $form;
    }
}