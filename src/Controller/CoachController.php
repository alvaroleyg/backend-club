<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Exception\CoachNotFoundException;
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

        return $this->json([
            'message' => 'Entrenador creado exitosamente',
            'coach' => $coach // Clave explícita
        ], 201);
    }

    /**
     * @Route("", methods={"GET"})
     */
    public function getAllCoaches(Request $request, CoachService $coachService): JsonResponse
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
     * @Route("/{id}", methods={"DELETE"})
     */
    public function deleteCoach(int $id, CoachService $coachService): JsonResponse
    {
        try {
            $coachService->deleteCoach($id);
            return $this->json(['message' => 'Entrenador eliminado exitosamente'], 200);
        } catch (CoachNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

    private function formatErrors($errors): array
    {
        $errorsResponse = [];

        foreach ($errors as $error) {
            $errorsResponse[$error->getPropertyPath()] = $error->getMessage();
        }

        return $errorsResponse;
    }
}
