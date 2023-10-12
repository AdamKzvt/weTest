<?php

namespace App\Repository;

use App\Entity\Donnees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Donnees>
 *
 * @method Donnees|null find($id, $lockMode = null, $lockVersion = null)
 * @method Donnees|null findOneBy(array $criteria, array $orderBy = null)
 * @method Donnees[]    findAll()
 * @method Donnees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DonneesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Donnees::class);
    }

    public function findBySearchTerm(string $searchTerm)
    {
        $queryBuilder = $this->createQueryBuilder('e'); // 'e' est un alias pour votre entité
        
        if ($searchTerm) {
            $queryBuilder
                ->where('e.Nsimul LIKE :term')
                ->orWhere('e.temperature LIKE :term')
                ->orWhere('e.velocity LIKE :term')
                ->orWhere('e.flow LIKE :term')
                ->orWhere('e.energy LIKE :term')
                ->orWhere('e.failure LIKE :term')
                // Pour les dates, la recherche pourrait ne pas fonctionner comme prévu.
                // Commenter ces lignes ou ajustez-les en fonction de vos besoins.
                // ->orWhere('e.start LIKE :term')
                // ->orWhere('e.duration LIKE :term')
                // La recherche sur des clés étrangères (comme moduleId) pourrait nécessiter une jointure ou une approche différente.
                // Commentez cette ligne ou ajustez-la en fonction de vos besoins.
                // ->orWhere('e.moduleId = :term')
                ->setParameter('term', '%' . $searchTerm . '%');
        }
    
        return $queryBuilder->getQuery()->getResult();
    }
    

        public function findAllOrderedByDateDesc()
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.start', 'DESC')
            ->getQuery()
            ->getResult();
    }


//    public function findOneBySomeField($value): ?Donnees
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
