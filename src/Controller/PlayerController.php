<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Service\PlayerService;
use App\Entity\Player;
use App\Exception\PlayerNotFoundException;

/**
 * @Route("/api/players")
 */
class PlayerController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     */
    public function createPlayer(Request $request, PlayerService $playerService, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $player = new Player();
        $player->setName($data['name'] ?? '');
        $player->setAge($data['age'] ?? 0);
        $player->setSalary($data['salary'] ?? null);

        $errors = $validator->validate($player, null, $player->getClub() ? ['Club'] : []);

        if (count($errors) > 0) {
            return $this->json(['errors' => $this->formatErrors($errors)], 400);
        }

        $playerService->createPlayer($player);

        return $this->json([
            'message' => 'Jugador creado exitosamente',
            'player' => $player
        ], 201);
    }

    /**
    * @Route("", methods={"GET"})
    */
    public function getPlayers(PlayerService $playerService): JsonResponse
    {
        $players = $playerService->getAllPlayers();
        return $this->json(['Lista total de jugadores creados' => $players], 200); // Serialización directa
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function deletePlayer(int $id, PlayerService $playerService): JsonResponse
    {
        try {
            $playerService->deletePlayer($id);
            return $this->json(['message' => 'Jugador eliminado exitosamente'], 200);
        } catch (PlayerNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }

    private function formatErrors($errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return $errorMessages;
    }
}
