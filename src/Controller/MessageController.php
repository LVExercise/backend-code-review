<?php
declare(strict_types=1);

namespace App\Controller;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @see MessageControllerTest
 * TODO: review both methods and also the `openapi.yaml` specification
 *       Add Comments for your Code-Review, so that the developer can understand why changes are needed.
 */
class MessageController extends AbstractController
{
    /**
     * TODO: cover this method with tests, and refactor the code (including other files that need to be refactored)
     */

    /**
     * @param  Request  $request
     * @param  MessageRepository  $messageRepository
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/messages', name: 'messages', methods: ['GET'])]
    public function list(Request $request, MessageRepository $messageRepository): JsonResponse
    {
        /**
         * COMMENT:
         *  - added name and method for the route for consistency
         *  - changed messages attribute to messageRepository to stop the confusion and questioning is the messages variable reassigned
         *  - changed messages to formattedMessages so everyone who's reading the code know that formattedMessages are different from messages and that we did some kind of manipulation on them
         *  - changed foreach to array_map
         *  - changed the expected Response to JsonResponse so it can automatically handle the headers part and the error throwing
         *  - added try/catch block since we're communicating with the DB so many things can go wrong (connection failed, couldn't retrieve the data etc.)
         *  - after removing response from repository added check in controller to ensure that correct cast is set to status
         */
        try {
            $status = $this->getStatusFromRequest($request);
            $messages = $messageRepository->findByStatus($status);

            $formattedMessages = array_map(function ($message) {
                return [
                    'uuid'   => $message->getUuid(),
                    'text'   => $message->getText(),
                    'status' => $message->getStatus(),
                ];
            }, $messages);

            return new JsonResponse(
                data: ['messages' => $formattedMessages],
                status: Response::HTTP_OK
            );
        } catch (Exception $exception) {
            /**
             * COMMENT: Response is in this format because of the consistency. In the real life app we wouldn't return an error. We would log the error for our internal monitoring system and instead we could throw some custom exception that would handle somewhere else
             */
            return new JsonResponse(
                data: ['error' => $exception->getMessage()],
                status: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    #[Route('/messages/send', methods: ['POST'])]
    public function send(Request $request, MessageBusInterface $bus): JsonResponse
    {
        /**
         * COMMENT:
         * - changed method verb to POST since we're sending the message
         * - changed query to request since the text we're sending will be from body and not from query. I was using json in the postman
         * - changed Response to JsonResponse to have the consistence throughout the code
         * - changed status 204 (no content). Could've left it like this, it's valid status since we're not returning any data, but when creating something use 201 status code
         */
        $text = json_decode(json: $request->getContent(), associative: true);

        if (!is_array($text) || empty($text['text'])) {
            return new JsonResponse(
                data: ['message' => 'Text is required'],
                status: Response::HTTP_BAD_REQUEST
            );
        }

        $bus->dispatch(new SendMessage($text['text']));

        return new JsonResponse(
            data: ['message' => 'Successfully sent'],
            status: Response::HTTP_CREATED
        );
    }

    /**
    * Extracts the status query parameter from the request.
    *
    * @param Request $request
    * @return string|null
    */
    protected function getStatusFromRequest(Request $request): ?string
    {
        /**
         * COMMENT: extracted this to a method so it would be easier to test
         */
        $status = $request->query->get('status');

        return is_string($status) ? $status : null;
    }
}