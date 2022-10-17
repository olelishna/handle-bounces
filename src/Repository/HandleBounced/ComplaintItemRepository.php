<?php

namespace App\Repository\HandleBounced;

use App\Entity\HandleBounced\ComplaintItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ComplaintItem>
 *
 * @method ComplaintItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComplaintItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComplaintItem[]    findAll()
 * @method ComplaintItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComplaintItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComplaintItem::class);
    }

    public function save(ComplaintItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ComplaintItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
