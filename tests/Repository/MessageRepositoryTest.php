<?php
declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    public function test_it_has_connection(): void
    {
        self::bootKernel();
        
        $messages = self::getContainer()->get(MessageRepository::class);

        /**
         * COMMENT: added this check since PHPStan isn’t aware of the method findByStatus that should be available on $messages object
         */
        $this->assertInstanceOf(MessageRepository::class, $messages);
        
        $this->assertSame([], $messages->findAll());
    }
}