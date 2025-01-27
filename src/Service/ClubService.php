<?php

namespace App\Service;

use App\Entity\Club;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use App\Repository\CoachRepository;
use App\Exception\InsufficientBudgetException;
use App\Exception\AlreadyInClubException;
use Doctrine\ORM\EntityManagerInterface;

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
        CoachRepository $coachRepository
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

    public function addPlayerToClub(int $clubId, int $playerId, float $salary): void
    {
        $club = $this->clubRepository->find($clubId);
        $player = $this->playerRepository->find($playerId);

        if ($player->getClub()) {
            throw new AlreadyInClubException();
        }

        $totalSalaries = $this->calculateTotalSalaries($club) + $salary;

        if ($totalSalaries > $club->getBudget()) {
            throw new InsufficientBudgetException();
        }

        $player->setClub($club);
        $player->setSalary($salary);
        $this->entityManager->flush();
    }

    public function addCoachToClub(int $clubId, int $coachId, float $salary): void
    {
        $club = $this->clubRepository->find($clubId);
        $coach = $this->coachRepository->find($coachId);

        if ($coach->getClub()) {
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

    public function updateClubBudget(int $clubId): float
    {
        $club = $this->clubRepository->find($clubId);

        if (!$club) {
            throw new \InvalidArgumentException("Club no encontrado");
        }

        $clubBudget = $club->getBudget();
        $currentSalaries = $this->calculateTotalSalaries($club);
        $newBudget = $clubBudget - $currentSalaries;

        $club->setBudget($newBudget);
        $this->entityManager->flush();

        return $newBudget;
    }

    public function removePlayerFromClub(int $clubId, int $playerId): void
    {
        $club = $this->clubRepository->find($clubId);
        $player = $this->playerRepository->find($playerId);

        if ($player->getClub() !== $club) {
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

        if ($coach->getClub() !== $club) {
            throw new \InvalidArgumentException("El entrenador no pertenece a este club");
        }

        $coach->setClub(null);
        $coach->setSalary(0);
        $this->entityManager->flush();
    }

    public function getClubPlayers(int $clubId, ?string $filter, int $page, int $limit): array
    {
        $club = $this->clubRepository->find($clubId);

        return $this->playerRepository->findByClubWithFilter(
            $club,
            $filter,
            $page,
            $limit
        );
    }
}
