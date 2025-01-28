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

    public function addPlayerToClub(int $clubId, int $playerId): array
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
        $currentBudget = $club->getBudget();

        if ($salary > $currentBudget) {
            throw new InsufficientBudgetException();
        }

        $player->setClub($club);
        $club->setBudget($currentBudget - $salary);
        $this->entityManager->flush();

        return [
            'player' => $player,
            'club' => $club
        ];
    }

    public function addCoachToClub(int $clubId, int $coachId): array
    {
        $club = $this->clubRepository->find($clubId);
        $coach = $this->coachRepository->find($coachId);

        if (!$club) {
            throw new ClubNotFoundException();
        } else if (!$coach) {
            throw new CoachNotFoundException();
        } else if ($coach->getClub()) {
            throw new AlreadyInClubException();
        }

        $salary = $coach->getSalary();
        $currentBudget = $club->calculateTotalSalaries() + $salary;

        if ($salary > $club->getBudget()) {
            throw new InsufficientBudgetException();
        }

        $coach->setClub($club);
        $club->setBudget($currentBudget - $salary);
        $this->entityManager->flush();

        return [
            'coach' => $coach,
            'club' => $club
        ];
    }

    public function updateClubBudget(int $clubId, float $change): float
    {
        $club = $this->clubRepository->find($clubId);

        if (!$club) {
            throw new ClubNotFoundException();
        }

        $currentBudget = $club->getBudget();
        $newBudget = $currentBudget + $change;

        if ($newBudget < 0) {
            throw new InsufficientBudgetException('El club no tiene suficiente presupuesto para asumir ese déficit');
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

        $salary = $player->getSalary();
        $club->setBudget($club->getBudget() + $salary);

        $player->setClub(null);
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

        $salary = $coach->getSalary();
        $club->setBudget($club->getBudget() + $salary);

        $coach->setClub(null);
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
        $club = $this->clubRepository->find($clubId);
        if (!$club) {
            throw new ClubNotFoundException();
        }

        $queryBuilder = $this->playerRepository->createQueryBuilder('p')
            ->andWhere('p.club = :clubId')
            ->setParameter('clubId', $club)
            ->orderBy('p.id', 'ASC');

        $filterByName = $filters['name'] ?? null;
        if (!empty($filterByName)) {
            $queryBuilder
                ->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $filterByName . '%');
        }

        $query = $queryBuilder->getQuery();
        dump($query->getSQL(), $query->getParameters());
        $paginator = new Paginator($query);
        $query = $queryBuilder->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($query);
        $total = count($paginator);

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
