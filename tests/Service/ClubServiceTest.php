<?php

namespace App\Tests\Service;

use App\Service\ClubService;
use App\Entity\Club;
use App\Entity\Player;
use App\Repository\ClubRepository;
use App\Repository\PlayerRepository;
use App\Repository\CoachRepository;
use App\Exception\InsufficientBudgetException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ClubServiceTest extends TestCase
{
    /** @var EntityManagerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $entityManager;
    /** @var EventDispatcherInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $eventDispatcher;
    /** @var ClubRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $clubRepository;
    /** @var PlayerRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $playerRepository;
    /** @var CoachRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $coachRepository;
    private ClubService $clubService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->clubRepository = $this->createMock(ClubRepository::class);
        $this->playerRepository = $this->createMock(PlayerRepository::class);
        $this->coachRepository = $this->createMock(CoachRepository::class);

        $this->clubService = new ClubService(
            $this->entityManager,
            $this->eventDispatcher,
            $this->clubRepository,
            $this->playerRepository,
            $this->coachRepository
        );
    }

    /**
     * @covers \App\Service\ClubService::addPlayerToClub
     */
    public function testInsufficientBudget(): void
    {
        $club = (new Club())->setBudget(500);
        $player = (new Player())->setSalary(1000);

        $this->clubRepository->method('find')->willReturn($club);
        $this->playerRepository->method('find')->willReturn($player);

        $this->expectException(InsufficientBudgetException::class);

        $this->clubService->addPlayerToClub(1, 1);
    }
}
