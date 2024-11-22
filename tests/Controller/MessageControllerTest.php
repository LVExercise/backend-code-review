<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\MessageController;
use App\Entity\Message;
use App\Message\SendMessage;
use App\Repository\MessageRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{

    use InteractsWithMessenger;

    private Request $request;
    private MessageRepository&MockObject $messageRepository;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->messageRepository = $this->createMock(MessageRepository::class);
    }
    
    function test_list_with_valid_status(): void
    {
        $message1 = new Message();
        $message1->setUuid('1efa8449-c5d9-618c-99ad-2f716bb7425e');
        $message1->setText('message 1');
        $message1->setStatus('sent');

        $message2 = new Message();
        $message2->setUuid('1efa8449-c5d9-6088-9ea2-2f716bb7425e');
        $message2->setText('message 2');
        $message2->setStatus('sent');

        $query = new InputBag(['status' => 'sent']);
        $this->request->query = $query;

        $this->messageRepository
            ->expects($this->once())
            ->method('findByStatus')
            ->with('sent')
            ->willReturn([$message1,$message2]);

        $controller = new MessageController();
        $response = $controller->list($this->request, $this->messageRepository);

        $this->assertInstanceOf(expected: JsonResponse::class, actual: $response);
        $this->assertEquals(expected: 200, actual: $response->getStatusCode());
        $this->assertEquals(
            [
                'messages' => [
                    ['uuid' => '1efa8449-c5d9-618c-99ad-2f716bb7425e', 'text' => 'message 1', 'status' => 'sent'],
                    ['uuid' => '1efa8449-c5d9-6088-9ea2-2f716bb7425e', 'text' => 'message 2', 'status' => 'sent'],
                ],
            ],
            json_decode(json: (string)$response->getContent(), associative: true)
        );
    }

    function test_list_when_throws_exception(): void
    {
        $query = new InputBag(['status' => 'sent']);
        $this->request->query = $query;

        $this->messageRepository
            ->expects($this->once())
            ->method('findByStatus')
            ->with('sent')
            ->willThrowException(new \Exception());

        $controller = new MessageController();
        $response = $controller->list($this->request, $this->messageRepository);

        $this->assertInstanceOf(expected: JsonResponse::class, actual: $response);
        $this->assertEquals(expected: 500, actual:  $response->getStatusCode());
    }

    function test_list_when_status_not_provided(): void
    {
        $message1 = new Message();
        $message1->setUuid('1efa8449-c5d9-618c-99ad-2f716bb7425e');
        $message1->setText('message 1');
        $message1->setStatus('sent');

        $message2 = new Message();
        $message2->setUuid('1efa8449-c5d9-6088-9ea2-2f716bb7425e');
        $message2->setText('message 2');
        $message2->setStatus('sent');

        $message3 = new Message();
        $message3->setUuid('1efa8449-c5d9-111-9ea2-2f716bb7425e');
        $message3->setText('message 3');
        $message3->setStatus('read');

        $message4 = new Message();
        $message4->setUuid('1efa8449-c5d9-2345-9ea2-2f716bb7425e');
        $message4->setText('message 4');
        $message4->setStatus('read');

        $query = new InputBag(['status' => 'sent']);
        $this->request->query = $query;

        $this->messageRepository
            ->expects($this->once())
            ->method('findByStatus')
            ->willReturn([$message1,$message2,$message3,$message4]);

        $controller = new MessageController();
        $response = $controller->list($this->request, $this->messageRepository);

        $this->assertInstanceOf(expected: JsonResponse::class, actual: $response);
        $this->assertEquals(expected: 200, actual: $response->getStatusCode());
        $this->assertEquals(
            [
                'messages' => [
                    ['uuid' => '1efa8449-c5d9-618c-99ad-2f716bb7425e', 'text' => 'message 1', 'status' => 'sent'],
                    ['uuid' => '1efa8449-c5d9-6088-9ea2-2f716bb7425e', 'text' => 'message 2', 'status' => 'sent'],
                    ['uuid' => '1efa8449-c5d9-111-9ea2-2f716bb7425e', 'text' => 'message 3', 'status' => 'read'],
                    ['uuid' => '1efa8449-c5d9-2345-9ea2-2f716bb7425e', 'text' => 'message 4', 'status' => 'read'],
                ],
            ],
            /**
             * COMMENT: cast to string only because of PHPStan
             */
            json_decode(json: (string)$response->getContent(), associative: true)
        );
    }

    function test_test_that_it_sends_a_message(): void
    {
        $client = static::createClient();

        $data = json_encode(['text' => 'Hello World']);

        if ($data === false) {
            throw new \RuntimeException('Failed to encode data to JSON');
        }

        $client->request(
            method: 'POST',
            uri:'/messages/send',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $data
        );

        $this->assertResponseStatusCodeSame(expectedCode: 201);
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(messageClass: SendMessage::class, times:  1);
    }

    function test_send_message_with_empty_text(): void
    {
        $client = static::createClient();

        $data = json_encode(['text' => '']);

        if ($data === false) {
            throw new \RuntimeException('Failed to encode data to JSON');
        }

        $client->request(
            method: 'POST',
            uri: '/messages/send',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $data
        );

        $this->assertResponseStatusCodeSame(expectedCode: 400);
    }

    function test_send_message_with_missing_text(): void
    {
        $client = static::createClient();

        /**
         * COMMENT: added this check because of PHPStan
         */
        $data = json_encode([]);

        if ($data === false) {
            throw new \RuntimeException('Failed to encode data to JSON');
        }

        $client->request(
            method: 'POST',
            uri: '/messages/send',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: $data
        );

        $this->assertResponseStatusCodeSame(expectedCode: 400);
    }
}