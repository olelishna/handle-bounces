<?php

namespace App\Repository\HandleBounced;

use App\Entity\HandleBounced\SuppressedMail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuppressedMail>
 *
 * @method SuppressedMail|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuppressedMail|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuppressedMail[]    findAll()
 * @method SuppressedMail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuppressedMailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuppressedMail::class);
    }

    public function save(SuppressedMail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SuppressedMail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
