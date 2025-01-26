<?php

namespace App\Service;

use App\Entity\Coach;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class CoachService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CoachRepository $coachRepository
    ) {}
    
    public function createCoach(Coach $coach): Coach
    {
        $this->entityManager->persist($coach);
        $this->entityManager->flush();
        return $coach;
    }
    
    public function getAllCoaches(int $page, int $limit): array
    {
        $query = $this->coachRepository->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        
        $paginator = new Paginator($query);
        $total = count($paginator);
        
        return [
            'coaches' => iterator_to_array($paginator->getIterator()),
            'total' => $total
        ];
    }
    
    public function getCoachById(int $id): ?Coach
    {
        return $this->coachRepository->find($id);
    }
}