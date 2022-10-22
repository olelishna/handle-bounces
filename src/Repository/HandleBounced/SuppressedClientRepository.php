<?php

namespace App\Repository\HandleBounced;

use App\Entity\HandleBounced\SuppressedClient;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuppressedClient>
 *
 * @method SuppressedClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuppressedClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuppressedClient[]    findAll()
 * @method SuppressedClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuppressedClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuppressedClient::class);
    }

    public function save(SuppressedClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SuppressedClient $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return SuppressedClient[] Returns an array of SuppressedClient objects
     */
    public function findAllNotUpdatedFrom(DateTime $updated): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.updated <= :date')
            ->setParameter('date', $updated)
            ->orderBy('s.score', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
