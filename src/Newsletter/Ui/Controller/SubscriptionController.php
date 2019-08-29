<?php
namespace App\Newsletter\Ui\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Newsletter\Application\CreateSubscriptionCommand;
use App\Newsletter\Application\ConfirmSubscriptionCommand;

class SubscriptionController extends AbstractController
{

    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @Route("/api/newsletter/subscription/create", name="newsletter/subscription/create", methods={"POST"})
     */
    public function create(Request $request): Response
    {   
        if (!$request->request->get('address_email')) {
                throw new \InvalidArgumentException('No all required confirmation data provided');
            }
        
        try {
            $this->messageBus->dispatch(
                new CreateSubscriptionCommand(
                    $request->get('email_address')
                )
            );
        } catch (\Throwable $ex) {
            return $this->json(['message' => $ex->getMessage() . $ex->getTraceAsString()]
                    , Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['status' => 'ok'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/newsletter/subscription/confirm", name="newsletter/subscription/confirm", methods={"GET"})
     */
    public function confirm(Request $request): Response
    {
        try {
            if (!$request->query->get('subscription_id') || !$request->query->get('address_email')) {
                throw new \InvalidArgumentException('No all required confirmation data provided');
            }

            $this->messageBus->dispatch(
                new ConfirmSubscriptionCommand(
                    $request->get('subscription_id'),
                    $request->get('email_address')
                )
            );
        } catch (\Throwable $ex) {
            return $this->json(['message' => $ex->getMessage() . $ex->getTraceAsString()]
                    , Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['status' => 'ok'], Response::HTTP_CREATED);
    }
}
