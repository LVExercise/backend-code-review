<?php

namespace App\Tests\Message;

use App\Entity\Message;
use App\Message\SendMessage;
use App\Message\SendMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class SendMessageHandlerTest extends TestCase
{

    private SendMessageHandler $handler;
    private MockObject $manager;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(EntityManagerInterface::class);

        $this->handler = new SendMessageHandler($this->manager);
    }

    function test_invoke_should_persist_message(): void
    {
        $sendMessage = new SendMessage('Hello, world!');

        $this->manager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Message::class));

        $this->manager
            ->expects($this->once())
            ->method('flush');

        $this->handler->__invoke($sendMessage);
    }

    function test_invoke_should_generate_and_set_uuid(): void
    {
        $sendMessage = new SendMessage('Testing UUID generation');

        $this->manager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (Message $message) {
                $uuid = $message->getUuid();
                return $uuid !== null && Uuid::isValid($uuid);
            }));

        $this->manager
            ->expects($this->once())
            ->method('flush');

        $this->handler->__invoke($sendMessage);
    }
}