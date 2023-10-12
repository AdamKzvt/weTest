<?php

namespace App\Controller;

use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ModuleChoiceType;

class SimulationController extends AbstractController
{
    #[Route('/simulation', name: 'app_simulation')]
    public function chooseModule(Request $request, ModuleRepository $moduleRepository, EntityManagerInterface $entityManager)
    {
        $modules = $moduleRepository->findAll();

        $form = $this->createForm(ModuleChoiceType::class, null, ['modules' => $modules]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $module = $form->get('module')->getData();

            // ... exécutez ici le code pour la simulation ...


            // Début de la simulation
            $donnees = new \App\Entity\Donnees();
            $moduleNom = $module->getNom();
            $donnees->setNsimul($moduleNom); // Simuler des valeurs
            $donnees->setTemperature(rand(-10, 40));
            $donnees->setVelocity(rand(1, 100));
            $donnees->setFlow(rand(1, 100));
            $donnees->setEnergy(rand(1, 100));
            $failureProbability = rand(1, 100);
            $failureValue = ($failureProbability <= 20) ? 1 : 0;
            $donnees->setFailure($failureValue);
            $donnees->setStart(new \DateTime());
            $donnees->setDuration(new \DateTime('@'.rand(60, 3600))); // Durée en secondes
            $donnees->setModuleId($module);

            $entityManager->persist($donnees);
            $entityManager->flush();
            // Fin de la simulation

            $this->addFlash('success', 'Simulation terminée avec succès!');
                    


            return $this->redirectToRoute('app_simulation');
        }

        return $this->render('simulation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}