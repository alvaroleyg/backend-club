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

/**
 * @Route("/api/players")
 */
class PlayerController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     */
    public function createPlayer(
        Request $request,
        PlayerService $playerService,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $player = new Player();
        $player->setName($data['name'] ?? '');
        $player->setAge($data['age'] ?? 0);
        $player->setSalary($data['salary'] ?? 0);

        $errors = $validator->validate($player, null, $player->getClub() ? ['Club'] : []);

        if (count($errors) > 0) {
            return $this->json(['errors' => $this->formatErrors($errors)], 400);
        }

        $playerService->createPlayer($player);

        return $this->json($player, 201);
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function listPlayers(Request $request, PlayerService $playerService): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 33);

        $result = $playerService->getAllPlayers($page, $limit);

        return $this->json([
            'data' => $result['players'],
            'total' => $result['total'],
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function getPlayerDetails(int $id, PlayerService $playerService): JsonResponse
    {
        $player = $playerService->getPlayerById($id);

        if (!$player) {
            return $this->json(['error' => 'Jugador no encontrado'], 404);
        }

        return $this->json($player);
    }

    private function formatErrors($errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return $errorMessages;
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function deletePlayer(int $id, PlayerService $playerService): JsonResponse
    {
        try {
            $playerService->deletePlayer($id);
            return $this->json(null, 204);
        } catch (NotFoundHttpException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        }
    }
}
