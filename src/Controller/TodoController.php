<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PlanningRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class TodoController extends AbstractController
{
    /** @var PlanningRepository */
    private $planningRepository;

    /**
     * @param PlanningRepository $planningRepository
     */
    public function __construct(PlanningRepository $planningRepository)
    {
        $this->planningRepository = $planningRepository;
    }

    public function index()
    {
        $todoList = [];

        foreach ($this->planningRepository->findBy(['status' => 'planned']) as $planning) {
            $todoList[] = [
                'id' => $planning->getId(),
                'name' => $planning->getPlant()->getName(),
                'date' => $planning->getPlantAt()->format('Y-m-d'),
            ];
        }

        return $this->render('todo/index.html.twig', [
            'todoList' => $todoList
        ]);
    }
}
