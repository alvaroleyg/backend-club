<?php

namespace App\Service;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\PlayerNotFoundException;


class PlayerService
{
    private $entityManager;
    private $playerRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        PlayerRepository $playerRepository,
    ) {
        $this->entityManager = $entityManager;
        $this->playerRepository = $playerRepository;
    }

    public function createPlayer(Player $player): Player
    {
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        return $player;
    }

    public function getAllPlayers(): array
    {
        return $this->playerRepository->createQueryBuilder('p')
            ->select('p.id', 'p.name')
            ->getQuery()
            ->getArrayResult();
    }

    public function deletePlayer(int $id): void
    {
        $player = $this->playerRepository->find($id);

        if (!$player) {
            throw new PlayerNotFoundException();
        } else if ($player->getClub() !== null) {
            throw new \InvalidArgumentException('No se puede eliminar el jugador porque pertenece a un club.');
        }

        $this->entityManager->remove($player);
        $this->entityManager->flush();
    }
}
