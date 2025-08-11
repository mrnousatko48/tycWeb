<?php
declare(strict_types=1);

namespace App\UI\Admin\Edit;

use Nette\Application\UI\Presenter;
use Nette\Application\UI\Form;
use App\Model\PageFacade;
use Nette\Http\FileUpload;
use App\Utils\ImageUploader;

class EditPresenter extends Presenter
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
        if (!$this->user->isLoggedIn() || !$this->user->isInRole('ADMIN')) {
            $this->flashMessage('Nemáš přístup🚫', 'danger');
            $this->redirect(':Front:Home:default');
        }
    }

    public function renderDefault(): void
    {
        $this->template->setFile(__DIR__ . '/Templates/default.latte');
    }

    public function renderLogos(): void
    {
        $this->template->logos = $this->pageFacade->getLogos();
        $this->template->setFile(__DIR__ . '/Templates/logos.latte');
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
        $this->template->customizations = $this->pageFacade->getCustomizations();
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

    public function renderLegalPages(): void
    {
        $this->template->pages = $this->pageFacade->getLegalPages();
        $this->template->setFile(__DIR__ . '/Templates/legalPages.latte');
    }

    public function actionEditLegal(string $section): void
    {
        $page = $this->pageFacade->getLegalPage($section);
        if (!$page) {
            $this->error('Stránka nenalezena', 404);
        }
        $this['legalForm']->setDefaults($page->toArray());
        $this->template->page = $page;
        $this->template->setFile(__DIR__ . '/Templates/editLegal.latte');
    }

    public function actionDeleteGalleryImage(int $id): void
    {
        $this->pageFacade->deleteGalleryImage($id);
        $this->flashMessage('Obrázek byl úspěšně odstraněn.', 'success');
        $this->redirect('gallery');
    }

    public function actionDeleteCustomization(int $id): void
    {
        $this->pageFacade->deleteCustomization($id);
        $this->flashMessage('Funkce byla úspěšně odstraněna.', 'success');
        $this->redirect('customization');
    }

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

    public function createComponentBannerForm(): Form
    {
        $banner = $this->pageFacade->getBannerSection();
        $fields = [
            'title' => ['type' => 'text', 'label' => 'Nadpis:', 'required' => true],
            'title_en' => ['type' => 'text', 'label' => 'Nadpis (EN):', 'required' => false],
            'description' => ['type' => 'textArea', 'label' => 'Popis:', 'required' => true],
            'description_en' => ['type' => 'textArea', 'label' => 'Popis (EN):', 'required' => false],
            'button_text' => ['type' => 'text', 'label' => 'Text tlačítka:', 'required' => true],
            'button_text_en' => ['type' => 'text', 'label' => 'Text tlačítka (EN):', 'required' => false],
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

    public function createComponentDurabilityForm(): Form
    {
        $durability = $this->pageFacade->getDurabilitySection();
        $fields = [
            'title' => ['type' => 'text', 'label' => 'Nadpis:', 'required' => true],
            'title_en' => ['type' => 'text', 'label' => 'Nadpis (EN):', 'required' => false],
            'description1' => ['type' => 'textArea', 'label' => 'Popis 1:', 'required' => true],
            'description1_en' => ['type' => 'textArea', 'label' => 'Popis 1 (EN):', 'required' => false],
            'description2' => ['type' => 'textArea', 'label' => 'Popis 2:', 'required' => true],
            'description2_en' => ['type' => 'textArea', 'label' => 'Popis 2 (EN):', 'required' => false],
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
            },
            'Změny byly uloženy',
            'Edit:durability'
        );

        $form['image']
            ->setHtmlAttribute('class', 'form-control')
            ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).');

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        return $form;
    }

    public function createComponentAddCustomizationForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Název funkce:')
             ->setRequired('Zadejte název funkce.')
             ->setHtmlAttribute('class', 'form-control');
        $form->addText('title_en', 'Název funkce (EN):')
             ->setHtmlAttribute('class', 'form-control');
        $form->addTextArea('description', 'Popis funkce:')
             ->setRequired('Zadejte popis funkce.')
             ->setHtmlAttribute('class', 'form-control');
        $form->addTextArea('description_en', 'Popis funkce (EN):')
             ->setHtmlAttribute('class', 'form-control');
        $form->addUpload('image', 'Obrázek:')
             ->setRequired('Vyberte obrázek.')
             ->setHtmlAttribute('class', 'form-control')
             ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).');
        $form->addSubmit('save', 'Přidat funkci')
             ->getControlPrototype()->addClass('btn btn-primary');

        $form->onSuccess[] = function (Form $form, $values) {
            try {
                $this->pageFacade->addCustomization($values->title, $values->description, $values->image, $values->title_en, $values->description_en);
                $this->flashMessage('Funkce byla úspěšně přidána.', 'success');
            } catch (\Exception $e) {
                $this->flashMessage('Chyba při přidávání funkce: ' . $e->getMessage(), 'danger');
            }
            $this->redirect('customization');
        };

        $form->getElementPrototype()->enctype = 'multipart/form-data';
        return $form;
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
        $form->addText('alt_text_en', 'Alternativní text (EN):')
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

    public function createComponentContactForm(): Form
    {
        $contact = $this->pageFacade->getContactInfo();
        $fields = [
            'name' => ['type' => 'text', 'label' => 'Jméno:', 'required' => true],
            'name_en' => ['type' => 'text', 'label' => 'Jméno (EN):', 'required' => false],
            'address' => ['type' => 'text', 'label' => 'Adresa:', 'required' => true],
            'address_en' => ['type' => 'text', 'label' => 'Adresa (EN):', 'required' => false],
            'ico' => ['type' => 'text', 'label' => 'IČO:', 'required' => true],
            'phone' => ['type' => 'text', 'label' => 'Telefon:', 'required' => true],
            'email' => ['type' => 'email', 'label' => 'Email:', 'required' => true],
            'map_embed' => ['type' => 'textArea', 'label' => 'Kód mapy:', 'required' => true],
        ];

        $form = $this->createEditForm(
            $contact ?? (object)[
                'name' => '',
                'name_en' => '',
                'address' => '',
                'address_en' => '',
                'ico' => '',
                'phone' => '',
                'email' => '',
                'map_embed' => '',
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

    public function createComponentLegalForm(): Form
    {
        $form = new Form;

        $form->addText('section_name', 'Název sekce:')
            ->setRequired('Zadejte název sekce.')
            ->addRule(Form::PATTERN, 'Pouze malá písmena a pomlčky.', '^[a-z-]+$')
            ->setHtmlAttribute('class', 'form-control')
            ->setHtmlAttribute('readonly', true);

        $form->addText('title', 'Titulek (CZ):')
            ->setRequired('Zadejte titulek.')
            ->setHtmlAttribute('class', 'form-control');

        $form->addText('title_en', 'Titulek (EN):')
            ->setHtmlAttribute('class', 'form-control');

        $form->addTextArea('content', 'Obsah (CZ):')
            ->setRequired('Zadejte obsah.')
            ->setHtmlAttribute('class', 'form-control wysiwyg-editor');

        $form->addTextArea('content_en', 'Obsah (EN):')
            ->setHtmlAttribute('class', 'form-control wysiwyg-editor');

        $form->addSubmit('save', 'Uložit')
            ->getControlPrototype()->addClass('btn btn-primary');

        $form->onSuccess[] = [$this, 'processLegalForm'];

        return $form;
    }

    public function processLegalForm(Form $form, \stdClass $values): void
    {
        if (empty(trim($values->content))) {
            $form->addError('Obsah (CZ) nesmí být prázdný.');
            $this->redrawControl('flashes');
            return;
        }

        try {
            $this->pageFacade->updateLegalPage($values->section_name, $values->title, $values->content, $values->title_en, $values->content_en);
            $this->flashMessage('Stránka byla úspěšně aktualizována.', 'success');
            $this->redirect('legalPages');
        } catch (\Exception $e) {
            error_log('LegalForm error: ' . $e->getMessage());
            $form->addError('Nepodařilo se uložit změny: ' . $e->getMessage());
            $this->redrawControl('flashes');
        }
    }

public function actionEditCustomization(int $id): void
{
    $customization = $this->pageFacade->getCustomization($id);
    if (!$customization) {
        $this->error('Funkce nenalezena', 404);
    }
    $this['editCustomizationForm']->setDefaults($customization->toArray());
    $this->template->editCustomization = $customization;
    $this->template->customizations = $this->pageFacade->getCustomizations(); // Added to set customizations
    $this->template->setFile(__DIR__ . '/Templates/customization.latte');
}

public function createComponentEditCustomizationForm(): Form
{
    $customization = $this->template->customization ?? (object)[
        'id' => null,
        'title' => '',
        'title_en' => '',
        'description' => '',
        'description_en' => '',
        'image_path' => null
    ];
    $fields = [
        'id' => ['type' => 'hidden', 'required' => true],
        'title' => ['type' => 'text', 'label' => 'Název funkce:', 'required' => true],
        'title_en' => ['type' => 'text', 'label' => 'Název funkce (EN):', 'required' => false],
        'description' => ['type' => 'textArea', 'label' => 'Popis funkce:', 'required' => true],
        'description_en' => ['type' => 'textArea', 'label' => 'Popis funkce (EN):', 'required' => false],
        'image' => ['type' => 'upload', 'label' => 'Obrázek:', 'required' => false],
    ];

    $form = $this->createEditForm(
        $customization,
        $fields,
        function ($values) {
            $this->pageFacade->updateCustomization((int)$values['id'], $values['title'], $values['description'], $values['image'], $values['title_en'], $values['description_en']);
        },
        'Funkce byla úspěšně aktualizována.',
        'Edit:customization'
    );

    $form['image']
        ->setHtmlAttribute('class', 'form-control')
        ->addRule(Form::IMAGE, 'Soubor musí být obrázek (JPEG, PNG, GIF, WebP).');

    $form->getElementPrototype()->enctype = 'multipart/form-data';
    return $form;
}
}