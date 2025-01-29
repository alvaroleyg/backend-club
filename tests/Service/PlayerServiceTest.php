<?php

namespace App\Tests\Service;

use App\Service\PlayerService;
use App\Entity\Player;
use App\Entity\Club;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class PlayerServiceTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject&EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var \PHPUnit\Framework\MockObject\MockObject&PlayerRepository */
    private PlayerRepository $playerRepository;

    private PlayerService $playerService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->playerRepository = $this->createMock(PlayerRepository::class);

        $this->playerService = new PlayerService(
            $this->entityManager,
            $this->playerRepository
        );
    }

    /**
     * @covers \App\Service\PlayerService::deletePlayer
     */
    public function testDeletePlayerInClub(): void
    {
        $player = (new Player())->setClub(new Club());

        $this->playerRepository->method('find')->willReturn($player);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No se puede eliminar el jugador porque pertenece a un club.');

        $this->playerService->deletePlayer(1);
    }
}