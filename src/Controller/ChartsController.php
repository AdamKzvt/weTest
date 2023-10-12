<?php

namespace App\Controller;

use App\Repository\ModuleRepository;
use App\Repository\DonneesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChartsController extends AbstractController
{
    #[Route('/charts', name: 'app_charts')]
    public function index(DonneesRepository $donneesRepository, ModuleRepository $moduleRepository): Response
    {
        $donneesRaw = $donneesRepository->findAll();
        $modules = $moduleRepository->findAll();

        $preparedData = [];
        foreach ($donneesRaw as $donnee) {
            $preparedData[] = [
                'Nsimul' => $donnee->getNsimul(),
                'temperature' => $donnee->getTemperature(),
                'velocity' => $donnee->getVelocity(),
                'flow' => $donnee->getFlow(),
                'energy' => $donnee->getEnergy(),
                'failure' => $donnee->getFailure(),
                'start' => $donnee->getStart(),
                'duration' => $donnee->getDuration(),
                'moduleId' => $donnee->getModuleId()->getId()  // Important
            ];
        }

        return $this->render('charts/index.html.twig', [
            'donnees' => $preparedData,
            'modules' => $modules,
        ]);
    }
}
