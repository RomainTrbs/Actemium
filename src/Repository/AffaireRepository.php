<?php

namespace App\Repository;

use App\Entity\Affaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Affaire>
 *
 * @method Affaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Affaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Affaire[]    findAll()
 * @method Affaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affaire::class);
    }

    public function findAllByCollaborateur($id)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.collaborateur = :id')
            ->setParameter('id', $id)
            ->orderBy('i.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllOnGoingByCollaborateur($userId)
    {
        return $this->createQueryBuilder('affaire')
            ->join('affaire.collaborateur', 'collaborateur')
            ->where('collaborateur.representant = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('collaborateur.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllOnGoingByClient($userId, array $collaborators, $isSuperAdmin = false)
    {
        $queryBuilder = $this->createQueryBuilder('affaire')
            ->join('affaire.collaborateur', 'collaborateur')
            ->andWhere('collaborateur IN (:collaborators)')
            ->setParameter('collaborators', $collaborators);

            if (!$isSuperAdmin) {
                $queryBuilder->andWhere('collaborateur.representant = :userId');
                $queryBuilder->setParameter('userId', $userId);
            }
            return $queryBuilder
            ->orderBy('affaire.client', 'ASC')  // Specify the complete path to the 'client' field
            ->getQuery()
            ->getResult();
    }

    public function findAllOnGoingByDate($userId, array $collaborators, $selectAllCollaborators = false)
    {
        $queryBuilder = $this->createQueryBuilder('affaire')
            ->join('affaire.collaborateur', 'collaborateur')
            ->andWhere('collaborateur IN (:collaborators)')
            ->setParameter('collaborators', $collaborators);

        if (!$selectAllCollaborators) {
            $queryBuilder->andWhere('collaborateur.representant = :userId');
            $queryBuilder->setParameter('userId', $userId);
        }

        return $queryBuilder
            ->orderBy('affaire.date_debut', 'ASC')
            ->getQuery()
            ->getResult();
    }


    

    public function findAllByUser($userId)
    {
        return $this->createQueryBuilder('affaire')
            ->join('affaire.collaborateur', 'collaborateur')
            ->where('collaborateur.representant = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('collaborateur.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
