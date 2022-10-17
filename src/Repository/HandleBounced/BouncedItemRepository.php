<?php

namespace App\Repository\HandleBounced;

use App\Entity\HandleBounced\BouncedItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BouncedItem>
 *
 * @method BouncedItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BouncedItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BouncedItem[]    findAll()
 * @method BouncedItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BouncedItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BouncedItem::class);
    }

    public function save(BouncedItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BouncedItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
