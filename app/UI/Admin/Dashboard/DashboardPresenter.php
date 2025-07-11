<?php
declare(strict_types=1);

namespace App\UI\Admin\Dashboard;

use Nette;
use App\Model\OrderFacade;
use App\MailSender\MailSender;

final class DashboardPresenter extends Nette\Application\UI\Presenter
{
    private OrderFacade $orderFacade;
    private MailSender $mailSender;

    public function __construct(OrderFacade $orderFacade, MailSender $mailSender)
    {
        parent::__construct();
        $this->orderFacade = $orderFacade;
        $this->mailSender = $mailSender;
    }

    protected function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->flashMessage('Nemáš oprávnění.', 'warning');
            $this->redirect(':Front:Sign:in', ['backlink' => $this->storeRequest()]);
        }

        if (!$this->getUser()->isInRole('ADMIN')) {
            $this->flashMessage('Nemáš oprávnění.', 'warning');
            $this->redirect(':Front:Sign:in');
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
        } catch (\InvalidArgumentException $e) {
            $this->flashMessage('Chyba: ' . $e->getMessage(), 'error');
        } catch (\Throwable $e) {
            $this->flashMessage('Chyba při odesílání e-mailu: ' . $e->getMessage(), 'error');
            error_log("Error sending email for order $orderId: " . $e->getMessage());
        }
        $this->redirect('detail', $orderId);
    }
}