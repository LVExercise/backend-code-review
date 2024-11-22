<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Finds messages by their status.
     *
     * @param  string|null  $status The status to filter messages by.
     * @return Message[] Returns an array of Message objects
     */
    public function findByStatus(?string $status = null): array
    {
        /**
         * COMMENT:
         * - renamed the method since it was called "by" to more truthful name findByStatus
         * - changed the attribute that method is accepting. We don't need the whole Response object. We need just the status. And repository should handle the DB communication and not the HTTP stuff
         * - changed the way we're querying the DB. It was unsafe and some malicious user could do the sql injection. Instead, we'll use parameterized approach
         * - also with the usage of the queryBuilder we don't have to use else part anymore to retrieve all the messages in case if the status is not provided, since it's a default behaviour of the queryBuilder
         */
        $queryBuilder = $this->createQueryBuilder(alias: 'm');

        if ($status) {
            $queryBuilder
                ->where('m.status = :status')
                ->setParameter(key: 'status', value: $status);
        }

        /**
         * COMMENT: added this annotation to clarify expected type to PHPStan
         */
        /** @var Message[] $result */
        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
}
