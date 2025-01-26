<?php

namespace App\Service;

use App\Entity\Club;
use App\Entity\Player;
use App\Entity\Coach;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use App\Repository\CoachRepository;
use App\Exception\InsufficientBudgetException;
use App\Exception\AlreadyInClubException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        private ValidatorInterface $validator
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

    private function calculateTotalSalaries(Club $club): float
    {
        $total = 0;

        foreach ($club->getPlayers() as $player) {
            $total += $player->getSalary();
        }

        foreach ($club->getCoaches() as $coach) {
            $total += $coach->getSalary();
        }

        return $total;
    }

    public function addPlayerToClub(int $clubId, int $playerId, float $salary): void
    {
        $club = $this->clubRepository->find($clubId);
        if (!$club) {
            throw new NotFoundHttpException("Club no encontrado");
        }

        $player = $this->playerRepository->find($playerId);
        if (!$player) {
            throw new NotFoundHttpException("Jugador no encontrado");
        }

        $club = $this->clubRepository->find($clubId);
        $player = $this->playerRepository->find($playerId);

        if ($player->getClub()) {
            throw new AlreadyInClubException();
        }

        $player->setClub($club);
        $player->setSalary($salary);

        $errors = $this->validator->validate($player, null, ['Club']);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $totalSalaries = $this->calculateTotalSalaries($club) + $salary;
        if ($totalSalaries > $club->getBudget()) {
            throw new InsufficientBudgetException();
        }

        $this->entityManager->flush();
    }

    public function addCoachToClub(int $clubId, int $coachId, float $salary): void
    {
        $club = $this->clubRepository->find($clubId);
        $coach = $this->coachRepository->find($coachId);

        if ($coach->getClub()) {
            throw new AlreadyInClubException();
        }

        $coach->setClub($club);
        $coach->setSalary($salary);

        $errors = $this->validator->validate($coach, null, ['Club']);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException((string) $errors);
        }

        $totalSalaries = $this->calculateTotalSalaries($club) + $salary;
        if ($totalSalaries > $club->getBudget()) {
            throw new InsufficientBudgetException();
        }

        $this->entityManager->flush();
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

    public function updateClubBudget(int $clubId, float $newBudget): void
    {
        if ($newBudget < 0) {
            throw new \InvalidArgumentException("El presupuesto no puede ser negativo");
        }

        $club = $this->clubRepository->find($clubId);

        $currentSalaries = $this->calculateTotalSalaries($club);

        if ($newBudget < $currentSalaries) {
            throw new InsufficientBudgetException(
                "El nuevo presupuesto no puede ser menor a los salarios actuales ($currentSalaries)"
            );
        }

        $club->setBudget($newBudget);
        $this->entityManager->flush();
    }
}
