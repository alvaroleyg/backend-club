<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\CoachService;
use App\Entity\Coach;

/**
 * @Route("/api/coaches")
 */
class CoachController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     */
    public function createCoach(
        Request $request,
        CoachService $coachService,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $coach = new Coach();
        $coach->setName($data['name'] ?? '');
        $coach->setAge($data['age'] ?? 0);
        $coach->setSalary($data['salary'] ?? 0);
        
        $errors = $validator->validate($coach, null, $coach->getClub() ? ['Club'] : []);
        
        if (count($errors) > 0) {
            return $this->json(['errors' => $this->formatErrors($errors)], 400);
        }
        
        $coachService->createCoach($coach);
        
        return $this->json($coach, 201);
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function listCoaches(Request $request, CoachService $coachService): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        
        $result = $coachService->getAllCoaches($page, $limit);
        
        return $this->json([
            'data' => $result['coaches'],
            'total' => $result['total'],
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function getCoachDetails(int $id, CoachService $coachService): JsonResponse
    {
        $coach = $coachService->getCoachById($id);
        
        if (!$coach) {
            return $this->json(['error' => 'Entrenador no encontrado'], 404);
        }
        
        return $this->json($coach);
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