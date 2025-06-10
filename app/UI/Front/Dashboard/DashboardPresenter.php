<?php

declare(strict_types=1);

namespace App\UI\Front\Dashboard;

use App\Model\RepairFacade;
use App\Model\ChatFacade;
use App\Model\UserFacade;
use Nette\Application\UI\Presenter;

final class DashboardPresenter extends Presenter
{
    private RepairFacade $repairFacade;
    private ChatFacade $chatFacade;
    private UserFacade $userFacade;

    public function __construct(RepairFacade $repairFacade, ChatFacade $chatFacade, UserFacade $userFacade)
    {
        parent::__construct();
        $this->repairFacade = $repairFacade;
        $this->chatFacade = $chatFacade;
        $this->userFacade = $userFacade;
    }

    /**
     * Akce pro zobrazení seznamu oprav.
     */
    public function renderDefault(int $repairId = null): void
    {
        // Získání všech oprav
        $repairs = $this->repairFacade->getAllRepairs();
        $this->template->repairs = $repairs;
    
        // Pokud je zvolen repairId, načteme detail opravy
        if ($repairId !== null) {
            $repair = $this->repairFacade->getRepairById($repairId);
            $this->template->selectedRepair = $repair;
    
            // Mapa stavů na procenta
            $statusProgressMap = [
                'Pending' => 10,
                'Approved' => 20,
                'In Progress' => 40,
                'Waiting for Parts' => 50,
                'On Hold' => 50,
                'Completed' => 100,
                'Failed' => 100,
                'Cancelled' => 100,
                'Ready for Pickup' => 90,
                'Delivered' => 100,
            ];
    
            // Výpočet procent
            $this->template->progressPercentage = $statusProgressMap[$repair->status] ?? 0;
            
            // Načtení chat zpráv
            $this->template->chatMessages = $this->chatFacade->getChatMessages($repair->id);
        } else {
            $this->template->selectedRepair = null;
            $this->template->chatMessages = [];
            $this->template->progressPercentage = 0;
        }
    }

    public function handleGetMessages(int $repairId): void
    {
        $messages = $this->chatFacade->getChatMessages($repairId);
        $formattedMessages = [];
    
        foreach ($messages as $msg) {
            $sender = $this->userFacade->getUserById($msg->sender_id);
            $receiver = $this->userFacade->getUserById($msg->receiver_id);
    
            $formattedMessages[] = [
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'sender_name' => $sender ? explode(" ", $sender->name)[0] : "Neznámý", // Extract first name
                'receiver_name' => $receiver ? explode(" ", $receiver->name)[0] : "Neznámý",
                'message' => $msg->message,
                'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
            ];
        }
    
        $this->sendResponse(new \Nette\Application\Responses\JsonResponse($formattedMessages));
    }
    
    
    
    public function handleSendMessage(): void
    {
        $message = $this->getHttpRequest()->getPost('message');
        $repairId = $this->getHttpRequest()->getPost('repairId');
        $senderId = $this->getUser()->getId();
    
        $repair = $this->repairFacade->getRepairById((int)$repairId);
        if (!$repair) {
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse(['status' => 'error', 'message' => 'Repair not found']));
            return;
        }
    
        $receiverId = $repair->technitian_id;


    
        if ($message && $repairId && $senderId && $receiverId) {
            $this->chatFacade->addChatMessage((int)$repairId, $senderId, $receiverId, $message);
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse(['status' => 'success']));
        } else {
            $this->sendResponse(new \Nette\Application\Responses\JsonResponse(['status' => 'error']));
        }
    }
    
    
    
    
}
