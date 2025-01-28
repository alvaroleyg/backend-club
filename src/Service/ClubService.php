<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Club;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use App\Repository\CoachRepository;
use App\Exception\InsufficientBudgetException;
use App\Exception\ClubNotFoundException;
use App\Exception\PlayerNotFoundException;
use App\Exception\CoachNotFoundException;
use App\Exception\AlreadyInClubException;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ClubService
{
    private $entityManager;
    private $clubRepository;
    private $playerRepository;
    private $coachRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ClubRepository $clubRepository,
        PlayerRepository $playerRepository,
        CoachRepository $coachRepository,
    ) {
        $this->entityManager = $entityManager;
        $this->clubRepository = $clubRepository;
        $this->playerRepository = $playerRepository;
        $this->coachRepository = $coachRepository;
    }

    public function createClub(Club $club): Club
    {
        $this->entityManager->persist($club);
        $this->entityManager->flush();
        return $club;
    }

    public function addPlayerToClub(int $clubId, int $playerId): void
    {
        $club = $this->clubRepository->find($clubId);
        $player = $this->playerRepository->find($playerId);

        if (!$club) {
            throw new ClubNotFoundException();
        } else if (!$player) {
            throw new PlayerNotFoundException();
        } else if ($player->getClub()) {
            throw new AlreadyInClubException();
        }

        $salary = $player->getSalary();
        $totalSalaries = $club->calculateTotalSalaries() + $salary;

        if ($totalSalaries > $club->getBudget()) {
            throw new InsufficientBudgetException();
        }

        $player->setClub($club);
        $player->setSalary($salary);
        $this->entityManager->flush();
    }

    public function addCoachToClub(int $clubId, int $coachId): void
    {
        $club = $this->clubRepository->find($clubId);
        $coach = $this->coachRepository->find($coachId);
        $salary = $coach->getSalary();

        if (!$club) {
            throw new ClubNotFoundException();
        } else if (!$coach) {
            throw new CoachNotFoundException();
        } else if ($coach->getClub()) {
            throw new AlreadyInClubException();
        }

        $totalSalaries = $club->calculateTotalSalaries() + $salary;

        if ($totalSalaries > $club->getBudget()) {
            throw new InsufficientBudgetException();
        }

        $coach->setClub($club);
        $coach->setSalary($salary);
        $this->entityManager->flush();
    }

    private function calculateTotalSalaries(Club $club): float
    {
        $total = 0;

        foreach ($club->getPlayers() as $player) {
            $total += $player->getSalary() ?? 0;
        }

        foreach ($club->getCoaches() as $coach) {
            $total += $coach->getSalary() ?? 0;
        }

        return $total;
    }

    public function updateClubBudget(int $clubId, float $salaryImpact): float
    {
        $club = $this->clubRepository->find($clubId);

        if (!$club) {
            throw new ClubNotFoundException();
        }

        $currentBudget = $club->getBudget();
        $newBudget = $currentBudget + $salaryImpact;

        if ($newBudget < 0) {
            throw new \Exception('El presupuesto no puede ser negativo.');
        }

        $club->setBudget($newBudget);
        $this->entityManager->flush();

        return $newBudget;
    }

    public function removePlayerFromClub(int $clubId, int $playerId): void
    {
        $club = $this->clubRepository->find($clubId);
        $player = $this->playerRepository->find($playerId);

        if (!$club) {
            throw new ClubNotFoundException();
        } else if (!$player) {
            throw new PlayerNotFoundException();
        } else if ($player->getClub() !== $club) {
            throw new \InvalidArgumentException("El jugador no pertenece a este club");
        }

        $player->setClub(null);
        $player->setSalary(0);
        $this->entityManager->flush();
    }

    public function removeCoachFromClub(int $clubId, int $coachId): void
    {
        $club = $this->clubRepository->find($clubId);
        $coach = $this->coachRepository->find($coachId);

        if (!$club) {
            throw new ClubNotFoundException();
        } else if (!$coach) {
            throw new CoachNotFoundException();
        } else if ($coach->getClub() !== $club) {
            throw new \InvalidArgumentException("El entrenador no pertenece a este club");
        }

        $coach->setClub(null);
        $coach->setSalary(0);
        $this->entityManager->flush();
    }

    /**
     * Lista jugadores de un club con filtrado y paginación
     * 
     * @param int $clubId
     * @param int $page
     * @param int $limit
     * @return array $filters Ejemplo: ['name' => 'CR7']
     * @return array
     */
    public function getClubPlayers(int $clubId, int $page, int $limit, array $filters = []): array
    {
        // 1. Verificar que el club exista
        $club = $this->clubRepository->find($clubId);
        if (!$club) {
            throw new ClubNotFoundException();
        }

        // 2. Construir la consulta con filtros
        $queryBuilder = $this->playerRepository->createQueryBuilder('p')
            ->andWhere('p.club = :clubId')
            ->setParameter('clubId', $club)
            ->orderBy('p.id', 'ASC');

        // 2.1. Aplicar filtros dinámicos (ej: por nombre)
        $filterByName = $filters['name'] ?? null;
        if (!empty($filterByName)) {
            $queryBuilder
                ->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $filterByName . '%');
        }

        $query = $queryBuilder->getQuery();

        // Depuración: Imprime la consulta SQL y los parámetros
        dump($query->getSQL(), $query->getParameters());

        $paginator = new Paginator($query);

        // 3. Paginación
        $query = $queryBuilder->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($query);
        $total = count($paginator);

        // 4. Formatear resultados (solo id y nombre)
        $players = [];
        foreach ($paginator as $player) {
            $players[] = [
                'id' => $player->getId(),
                'name' => $player->getName(),
            ];
        }

        if (!empty($filterByName) && $total === 0) {
            throw new PlayerNotFoundException();
        }

        return [
            'players' => $players,
            'total' => $total,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
        ];
    }
}
