<?php

namespace App\Service;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PlayerService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlayerRepository $playerRepository
    ) {}
    
    public function createPlayer(Player $player): Player
    {
        $this->entityManager->persist($player);
        $this->entityManager->flush();
        return $player;
    }
    
    public function getAllPlayers(int $page, int $limit): array
    {
        $query = $this->playerRepository->createQueryBuilder('p')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);
        
        $paginator = new Paginator($query);
        $total = count($paginator);
        
        return [
            'players' => iterator_to_array($paginator->getIterator()),
            'total' => $total
        ];
    }
    
    public function getPlayerById(int $id): ?Player
    {
        return $this->playerRepository->find($id);
    }
}