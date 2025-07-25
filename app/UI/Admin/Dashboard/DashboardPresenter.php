<?php
declare(strict_types=1);

namespace App\UI\Admin\Dashboard;

use Nette;
use App\Model\OrderFacade;
use App\MailSender\MailSender;
use App\Model\EmailFacade;
use Nette\Application\UI\Form;

final class DashboardPresenter extends Nette\Application\UI\Presenter
{
    private OrderFacade $orderFacade;
    private MailSender $mailSender;
    private EmailFacade $emailFacade;

    public function __construct(OrderFacade $orderFacade, MailSender $mailSender, EmailFacade $emailFacade)
    {
        parent::__construct();
        $this->orderFacade = $orderFacade;
        $this->mailSender = $mailSender;
        $this->emailFacade = $emailFacade;
    }

    protected function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn() || !$this->getUser()->isInRole('ADMIN')) {
            $this->flashMessage('Nemáš oprávnění.', 'warning');
            $this->redirect(':Front:Sign:in', ['backlink' => $this->storeRequest()]);
        }
    }

    public function renderDefault(): void
    {
    }

    public function renderDetail(int $id): void
    {
        $orderData = $this->orderFacade->getOrderDetails($id);
        if (!$orderData) {
            $this->flashMessage('Objednávka nenalezena.', 'error');
            $this->redirect('orders');
        }
        $this->template->orderData = $orderData;
    }

    public function renderOrders(): void
    {
        $status = $this->getParameter('status');
        $this->template->orders = $this->orderFacade->getOrdersWithDetails($status);
        $this->template->currentStatus = $status;
    }

public function renderEmails(): void
{
    $id = $this->getParameter('id');
    $this->template->emailTemplates = $this->emailFacade->getAllTemplates();

    if ($id) {
        $template = $this->emailFacade->getTemplateById((int)$id);
        if ($template) {
            $this['emailTemplateForm']->setDefaults([
                'name' => $template['name'],
                'subject' => $template['subject'],
                'body' => $template['body'],
                'recipient_email' => $template['recipient_email'] ?? '',
                'admin_phone' => $template['admin_phone'] ?? '',
                'id' => $template['id'],
            ]);
            $this->template->emailTemplate = $template;
        }
    }
}


    public function handleChangeState(int $orderId, string $newState): void
    {
        try {
            $this->orderFacade->updateOrderState($orderId, $newState);
            $orderData = $this->orderFacade->getOrderDetails($orderId);
            $order = $orderData['order'];
            $recipientName = $order->firstname . ' ' . $order->lastname;
            $recipientEmail = $order->email;

            switch ($newState) {
                case 'ZAPLACENO':
                    $this->mailSender->sendPaymentConfirmationEmail($recipientEmail, $recipientName, $order);
                    break;
                case 'ODESLANO':
                    $this->mailSender->sendShippedEmail($recipientEmail, $recipientName, $order);
                    break;
                case 'DORUCENO':
                    $this->mailSender->sendReadyForPickupEmail($recipientEmail, $recipientName, $order);
                    break;
                case 'VYZVEDNUTO':
                    $this->mailSender->sendPickedUpEmail($recipientEmail, $recipientName, $order);
                    break;
            }

            $this->flashMessage('Stav objednávky byl aktualizován.', 'success');
        } catch (\Exception $e) {
            $this->flashMessage('Chyba: ' . $e->getMessage(), 'error');
        }
        $this->redirect('detail', $orderId);
    }

    protected function createComponentEmailTemplateForm(): Form
{
    $form = new Form;

    $form->addText('name', 'Název šablony:')
        ->setDisabled(true)
        ->setHtmlAttribute('class', 'form-control');

    $form->addText('subject', 'Předmět:')
        ->setRequired()
        ->setHtmlAttribute('class', 'form-control');

    $form->addTextArea('body', 'Tělo e-mailu:')
        ->setRequired()
        ->setHtmlAttribute('class', 'form-control')
        ->setHtmlAttribute('rows', 10);

    $form->addText('recipient_email', 'E-mail příjemce:')
        ->setHtmlAttribute('class', 'form-control');

    $form->addText('admin_phone', 'Telefon:')
        ->setHtmlAttribute('class', 'form-control');

    $form->addHidden('id');

    $form->addSubmit('submit', 'Uložit')
        ->setHtmlAttribute('class', 'btn btn-primary mt-3');

    $form->onSuccess[] = function (Form $form, \stdClass $values): void {
        $this->emailFacade->updateTemplate((int)$values->id, [
            'subject' => $values->subject,
            'body' => $values->body,
            'recipient_email' => $values->recipient_email ?: null,
            'admin_phone' => $values->admin_phone ?: null,
        ]);
        $this->flashMessage('Šablona uložena.', 'success');
        $this->redirect('emails');
    };

    return $form;
}

/**
 * Handle file download for a case's user upload
 */
public function handleDownloadFile(int $caseId): void
{
    if (!$this->getUser()->isLoggedIn() || !$this->getUser()->isInRole('ADMIN')) {
        $this->flashMessage('Nemáš oprávnění.', 'error');
        $this->redirect('orders');
    }

    $uploadData = $this->orderFacade->getUserUploadFilePath($caseId);
    if (!$uploadData) {
        $this->flashMessage('Soubor nenalezen.', 'error');
        $this->redirect('detail', $this->getParameter('id'));
    }

    $filePath = $_SERVER['DOCUMENT_ROOT'] . $uploadData['file_path'];
    if (!file_exists($filePath)) {
        $this->flashMessage('Soubor nenalezen na serveru.', 'error');
        $this->redirect('detail', $this->getParameter('id'));
    }

    $response = new \Nette\Application\Responses\FileResponse(
        $filePath,
        $uploadData['original_filename'],
        mime_content_type($filePath)
    );
    $this->sendResponse($response);
}
}