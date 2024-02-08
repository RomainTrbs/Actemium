<?php

namespace App\Repository;

use App\Entity\Poste;
use App\Entity\Status;
use App\Entity\Collaborateur;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Collaborateur>
 *
 * @method Collaborateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collaborateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collaborateur[]    findAll()
 * @method Collaborateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollaborateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collaborateur::class);
    }

    public function findByCollaborateurId(Collaborateur $collaborateur)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.collaborateur = :collaborateur')
            ->setParameter('collaborateur', $collaborateur)
            ->getQuery()
            ->getResult();
    }

    public function findAllByStatus(Status $status)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function findAllByRepresentant($userId)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.representant = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
    
    public function findAllByPoste(Poste $poste)
    {
        return $this->createQueryBuilder('u')
            ->join('u.poste', 'p') // Assurez-vous que le champ dans votre entité User pointe vers le bon champ de poste
            ->andWhere('p = :poste')
            ->setParameter('poste', $poste)
            ->getQuery()
            ->getResult();
    }

    public function findAllWithAffaires($userId)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.affaires', 'ac')  // Assurez-vous de remplacer 'affaires' par le nom réel de la relation dans votre entité Collaborator
            ->andWhere('ac.id IS NOT NULL')  // Vérifiez si la clé primaire de l'affaire n'est pas nulle pour garantir qu'il y a au moins une affaire associée
            ->andWhere('c.representant = :userId')  // Ajoutez la condition pour vérifier l'ID du représentant du collaborateur
            ->setParameter('userId', $userId)
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithAffairesOnly()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.affaires', 'ac')  // Assurez-vous de remplacer 'affaires' par le nom réel de la relation dans votre entité Collaborator
            ->andWhere('ac.id IS NOT NULL')  // Vérifiez si la clé primaire de l'affaire n'est pas nulle pour garantir qu'il y a au moins une affaire associée
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllInArray(array $collaboratorIds)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id IN (:collaboratorIds)')
            ->setParameter('collaboratorIds', $collaboratorIds)            
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
    

//    /**
//     * @return Collaborateur[] Returns an array of Collaborateur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Collaborateur
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
