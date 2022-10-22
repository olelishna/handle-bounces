<?php

namespace App\Repository;

use App\Entity\Variable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Variable>
 *
 * @method Variable|null find($id, $lockMode = null, $lockVersion = null)
 * @method Variable|null findOneBy(array $criteria, array $orderBy = null)
 * @method Variable[]    findAll()
 * @method Variable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VariableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Variable::class);
    }

    public function save(Variable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Variable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Variable[] Returns an array of Variable objects
     */
    public function getAll(): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function get($name): mixed
    {
        try {
            /** @var Variable $var */
            $var = $this->createQueryBuilder('v')
                ->andWhere('v.name = :val')
                ->setParameter('val', $name)
                ->getQuery()
                ->getOneOrNullResult();

        } catch (NonUniqueResultException $exception) {
            die($exception->getMessage());
        }

        return $var?->getValue();
    }
}
