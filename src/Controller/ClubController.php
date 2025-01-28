<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\ClubService;
use App\Entity\Club;
use App\Exception\ClubNotFoundException;
use App\Exception\PlayerNotFoundException;
use App\Exception\CoachNotFoundException;
use App\Exception\AlreadyInClubException;
use App\Exception\InsufficientBudgetException;
use Doctrine\ORM\Query\QueryException;

/**
 * @Route("/api/clubs")
 */
class ClubController extends AbstractController
{
    /**
     * @Route("", methods={"POST"}, name="create_club")
     */
    public function createClub(Request $request, ClubService $clubService, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $club = new Club();
        $club->setName($data['name']);
        $club->setBudget($data['budget']);

        $errors = $validator->validate($club);

        if (count($errors) > 0) {
            return $this->json(['errors' => $this->formatErrors($errors)], 400);
        }

        $clubService->createClub($club);

        return $this->json($club, 201);
    }

    /**
     * @Route("/{clubId}/players/{playerId}", methods={"POST"}, name="add_player_to_club")
     */
    public function addPlayerToClub(int $clubId, int $playerId, ClubService $clubService): JsonResponse
    {
        try {
            $clubService->addPlayerToClub($clubId, $playerId);
            return $this->json(['message' => 'Jugador añadido al club exitosamente'], 200);
        } catch (ClubNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (PlayerNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (AlreadyInClubException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (InsufficientBudgetException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error inesperado'], 500);
        }

        return $this->json(null, 204);
    }

    /**
     * @Route("/{clubId}/coaches/{coachId}", methods={"POST"}, name="add_coach_to_club")
     */
    public function addCoachToClub(int $clubId, int $coachId, ClubService $clubService): JsonResponse
    {
        try {
            $clubService->addCoachToClub($clubId, $coachId);
            return $this->json(['message' => 'Entrenador añadido al club exitosamente'], 200);
        } catch (ClubNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (CoachNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (AlreadyInClubException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (InsufficientBudgetException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error inesperado'], 500);
        }

        return $this->json(null, 204);
    }

    /**
     * @Route("/{id}/budget", methods={"PATCH"}, name="update_club_budget")
     */
    public function updateClubBudget(int $id, Request $request, ClubService $clubService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $delta = $data['delta'] ?? null;

        if ($delta === null || !is_numeric($delta)) {
            return $this->json(['error' => 'Se requiere el campo "delta" (número)'], 400);
        }

        try {
            $updatedBudget = $clubService->updateClubBudget($id, (float)$delta);
        } catch (ClubNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error inesperado'], 500);
        }

        return $this->json([
            'message' => 'Presupuesto actualizado.',
            'currentBudget' => $updatedBudget
        ]);
    }
    // public function updateClubBudget(int $id, ClubService $clubService): JsonResponse
    // {
    //     try {
    //         $updatedBudget = $clubService->updateClubBudget($id);
    //     } catch (ClubNotFoundException $e) {
    //         return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
    //     } catch (\Exception $e) {
    //         return $this->json(['error' => 'Error inesperado'], 500);
    //     }

    //     return $this->json([
    //         'message' => 'El presupuesto ha sido actualizado.',
    //         'currentBudget' => $updatedBudget
    //     ], 200);
    // }

    /**
     * @Route("/{clubId}/players/{playerId}", methods={"DELETE"}, name="remove_player_from_club")
     */
    public function removePlayerFromClub(int $clubId, int $playerId, ClubService $clubService): JsonResponse
    {
        try {
            $clubService->removePlayerFromClub($clubId, $playerId);
            return $this->json(['message' => 'Jugador eliminado del club exitosamente'], 200);
        } catch (ClubNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (PlayerNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error inesperado'], 500);
        }
    }

    /**
     * @Route("/{clubId}/coaches/{coachId}", methods={"DELETE"}, name="remove_coach_from_club")
     */
    public function removeCoachFromClub(int $clubId, int $coachId, ClubService $clubService): JsonResponse
    {
        try {
            $clubService->removeCoachFromClub($clubId, $coachId);
            return $this->json(['message' => 'Entrenador eliminado del club exitosamente'], 200);
        } catch (ClubNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (CoachNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error inesperado'], 500);
        }
    }

    /**
     * @Route("/{clubId}/players", methods={"GET"}, name="get_club_players")
     */
    public function getClubPlayers(int $clubId, Request $request, ClubService $clubService): JsonResponse
    {
        // Parámetros de la solicitud
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $filters = $request->query->all('filter');

        try {
            $result = $clubService->getClubPlayers($clubId, $page, $limit, $filters);
            return $this->json($result);
        } catch (ClubNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (PlayerNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], 404); // <-- Nuevo catch
        } catch (QueryException $e) {
            return $this->json(['error' => $e->getMessage()], 500);
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
