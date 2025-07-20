<?php
declare(strict_types=1);

namespace App\UI\Admin\Dashboard;

use App\UI\Admin\BaseAdminPresenter;
use App\Model\OrderFacade;
use App\MailSender\MailSender;

final class DashboardPresenter extends BaseAdminPresenter
{
    public function __construct(
        private OrderFacade $orderFacade,
        private MailSender $mailSender
    ) {
        parent::__construct();
    }

    public function renderDefault(): void
    {
        $this->template->orders = $this->orderFacade->getOrdersWithDetails(null);
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
            if (!$orderData) {
                throw new \InvalidArgumentException('Objednávka nenalezena.');
            }

            $order = $orderData['order'];
            $recipientName = $order->firstname . ' ' . $order->lastname;
            $recipientEmail = $order->email;

            // Send email based on new state
            try {
                switch ($newState) {
                    case 'ZAPLACENO':
                        $this->mailSender->sendPaymentConfirmationEmail($recipientEmail, $recipientName, $order);
                        $this->flashMessage('E-mail s potvrzením platby odeslán.', 'success');
                        break;
                    case 'ODESLANO':
                        $this->mailSender->sendShippedEmail($recipientEmail, $recipientName, $order);
                        $this->flashMessage('E-mail o odeslání objednávky odeslán.', 'success');
                        break;
                    case 'DORUCENO':
                        $this->mailSender->sendReadyForPickupEmail($recipientEmail, $recipientName, $order);
                        $this->flashMessage('E-mail o připravení k vyzvednutí odeslán.', 'success');
                        break;
                    case 'VYZVEDNUTO':
                        $this->mailSender->sendPickedUpEmail($recipientEmail, $recipientName, $order);
                        $this->flashMessage('E-mail o vyzvednutí objednávky odeslán.', 'success');
                        break;
                    default:
                        throw new \InvalidArgumentException("Neplatný stav objednávky: $newState");
                }
            } catch (\Exception $e) {
                error_log("Error sending email for order $orderId (state: $newState): " . $e->getMessage());
                $this->flashMessage("Chyba při odesílání e-mailu: " . $e->getMessage(), 'warning');
            }

            $this->flashMessage('Stav objednávky byl aktualizován.', 'success');
        } catch (\InvalidArgumentException $e) {
            error_log("Invalid argument for order $orderId: " . $e->getMessage());
            $this->flashMessage('Chyba: ' . $e->getMessage(), 'error');
        } catch (\Throwable $e) {
            error_log("Unexpected error for order $orderId: " . $e->getMessage());
            $this->flashMessage('Neočekávaná chyba: ' . $e->getMessage(), 'error');
        }

        $this->redirect('detail', $orderId);
    }
}