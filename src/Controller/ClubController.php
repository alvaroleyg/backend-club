<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\ClubService;
use App\Entity\Club;
use App\Exception\AlreadyInClubException;
use App\Exception\InsufficientBudgetException;

/**
 * @Route("/api/clubs")
 */
class ClubController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     */
    public function createClub(
        Request $request, 
        ClubService $clubService,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $club = new Club();
        $club->setName($data['name'] ?? '');
        $club->setBudget($data['budget'] ?? 0);
        
        $errors = $validator->validate($club);
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }
        
        $clubService->createClub($club);
        
        return $this->json($club, 201);
    }

    /**
     * @Route("/{id}/players", methods={"POST"})
     */
    public function addPlayerToClub(int $id, Request $request, ClubService $clubService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        try {
            $clubService->addPlayerToClub(
                $id,
                $data['playerId'],
                $data['salary']
            );
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
     * @Route("/{id}/coaches", methods={"POST"})
     */
    public function addCoachToClub(int $id, Request $request, ClubService $clubService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $clubService->addCoachToClub(
                $id,
                $data['coachId'],
                $data['salary']
            );
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
     * @Route("/{id}/budget", methods={"PATCH"})
     */
    public function updateClubBudget(int $id, ClubService $clubService): JsonResponse
    {
        try {
            $updatedBudget = $clubService->updateClubBudget($id);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error inesperado'], 500);
        }
    
        return $this->json([
            'message' => 'El presupuesto ha sido actualizado.',
            'currentBudget' => $updatedBudget
        ], 200);
    }

    /**
     * @Route("/{clubId}/players/{playerId}", methods={"DELETE"})
     */
    public function removePlayerFromClub(int $clubId, int $playerId, ClubService $clubService): JsonResponse
    {
        try {
            $clubService->removePlayerFromClub($clubId, $playerId);
            return $this->json(['message' => 'Jugador eliminado exitosamente'], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error inesperado'], 500);
        }
    }

    /**
     * @Route("/{clubId}/coaches/{coachId}", methods={"DELETE"})
     */
    public function removeCoachFromClub(int $clubId, int $coachId, ClubService $clubService): JsonResponse
    {
        try {
            $clubService->removeCoachFromClub($clubId, $coachId);
            return $this->json(['message' => 'Entrenador eliminado exitosamente'], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error inesperado'], 500);
        }
    }

    /**
     * @Route("/{id}/players", methods={"GET"})
     */
    public function listClubPlayers(
        int $id,
        Request $request,
        ClubService $clubService
    ): JsonResponse {
        $filter = $request->query->get('name');
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        
        $result = $clubService->getClubPlayers($id, $filter, $page, $limit);
        
        return $this->json([
            'data' => $result['players'],
            'total' => $result['total'],
            'page' => $page,
            'limit' => $limit
        ]);
    }
}