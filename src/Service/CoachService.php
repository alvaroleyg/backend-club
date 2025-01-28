<?php

namespace App\Service;

use App\Entity\Coach;
use App\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Exception\CoachNotFoundException;

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

    public function deleteCoach(int $id): void
    {
        $coach = $this->coachRepository->find($id);

        if (!$coach) {
            throw new CoachNotFoundException();
        } else if ($coach->getClub() !== null) {
            throw new \InvalidArgumentException('No se puede eliminar el entrenador porque pertenece a un club.');
        }

        $this->entityManager->remove($coach);
        $this->entityManager->flush();
    }
}
