<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\ClubService;
use App\Entity\Club;

/**
 * @Route("/api/clubs")
 */
class ClubController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     */
    public function createClub(Request $request, ClubService $clubService, ValidatorInterface $validator): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $club = new Club();
        $club->setName($data['name'] ?? '');
        $club->setBudget($data['budget'] ?? 0);
        
        // Validar la entidad
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
        
        $clubService->addPlayerToClub(
            $id,
            $data['playerId'],
            $data['salary']
        );
        
        return $this->json(null, 204);
    }

    /**
     * @Route("/{id}/coaches", methods={"POST"})
     */
    public function addCoachToClub(int $id, Request $request, ClubService $clubService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $clubService->addCoachToClub(
            $id,
            $data['coachId'],
            $data['salary']
        );
        
        return $this->json(null, 204);
    }

    /**
     * @Route("/{id}/budget", methods={"PATCH"})
     */
    public function updateClubBudget(int $id, Request $request, ClubService $clubService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $clubService->updateClubBudget(
            $id,
            $data['newBudget']
        );
        
        return $this->json(null, 204);
    }

    /**
     * @Route("/{clubId}/players/{playerId}", methods={"DELETE"})
     */
    public function removePlayerFromClub(int $clubId, int $playerId, ClubService $clubService): JsonResponse
    {
        $clubService->removePlayerFromClub($clubId, $playerId);
        return $this->json(null, 204);
    }

    /**
     * @Route("/{clubId}/coaches/{coachId}", methods={"DELETE"})
     */
    public function removeCoachFromClub(int $clubId, int $coachId, ClubService $clubService): JsonResponse
    {
        $clubService->removeCoachFromClub($clubId, $coachId);
        return $this->json(null, 204);
    }

    /**
     * @Route("/{id}/players", methods={"GET"})
     */
    public function listClubPlayers(int $id, Request $request, ClubService $clubService): JsonResponse {
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