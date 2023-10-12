<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Donnees;
use App\Form\ModuleType;
use App\Form\ModuleChoiceType;
use App\Repository\ModuleRepository;
use App\Repository\DonneesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(ModuleRepository $moduleRepository, DonneesRepository $donneesRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $modules = $moduleRepository->findAll();
        $latestSimulation = $entityManager->getRepository(Donnees::class)->findOneBy([], ['id' => 'DESC']);


        $module = new Module();
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($module);
                $entityManager->flush();
        
                $this->addFlash('success', 'Module ajouté avec succès!');
                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'Un module avec ce nom existe déjà.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur inattendue est survenue.');
            }
        }
        

        // Initialisation de formChooseModule
        $formChooseModule = $this->createForm(ModuleChoiceType::class, null, ['modules' => $modules]);
        $formChooseModule->handleRequest($request);

        if ($formChooseModule->isSubmitted() && $formChooseModule->isValid()) {
            $this->addFlash('success', 'Simulation terminée avec succès!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'module' => $module,
            'modules' => $modules,
            'form' => $form->createView(),
            'formChooseModule' => $formChooseModule->createView(),
            'latestSimulation' => $latestSimulation
        ]);
    }

    #[Route('/simulate', name: 'app_simulate', methods: ['POST'])]
    public function simulate(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier tout le contenu de la requête
        $requestData = $request->request->all();

        // Récupérer la durée réelle de la simulation du formulaire
        $simulationDurationMs = isset($requestData['real_simulation_duration']) ? intval($requestData['real_simulation_duration']) : null;
        $simulationDurationSeconds = $simulationDurationMs / 1000; // Convertissez les millisecondes en secondes

        // Récupérer le module
        $chosenModuleId = $requestData['module_choice']['module'];

        // Vérifier la valeur et le type de chosenModuleId
        $chosenModule = $entityManager->getRepository(Module::class)->find($chosenModuleId);
        if (!$chosenModule) {
            return $this->json(['status' => 'error', 'message' => 'Module not found']);
        }

        // Mettre à jour l'état de simulation du module
        $chosenModule->setIsRunning(true);

        $failureOccurred = isset($requestData['failureOccurred']) ? intval($requestData['failureOccurred']) : 0;

        // Commencez la simulation
        $donnees = new \App\Entity\Donnees();
        $moduleNom = $chosenModule->getNom();
        $donnees->setNsimul($moduleNom);
        $donnees->setTemperature(rand(-10, 40));
        $donnees->setVelocity(rand(1, 1000));
        $donnees->setFlow(rand(100, 10000));
        $donnees->setEnergy(rand(1, 100));

        // Utilisez la valeur de failureOccurred pour définir l'état de panne
        $donnees->setFailure($failureOccurred);
        $donnees->setStart(new \DateTime('now', new \DateTimeZone('Europe/Paris')));

        // Utilisez la durée réelle pour définir la durée de la simulation
        $donnees->setDuration(new \DateTime('@' . $simulationDurationSeconds));
        $donnees->setNsimul($moduleNom);
        $donnees->setModuleId($chosenModule);

        $entityManager->persist($donnees);
        $entityManager->flush();

        return $this->json([
            'status' => 'success',
            'donneesId' => $donnees->getId(),
            'simulationDuration' => $simulationDurationSeconds
        ]);
    }


    #[Route('/update-duration', name: 'app_update_duration', methods: ['POST'])]
    public function updateDuration(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $donneesId = $data['donneesId'];
        $duration = $data['duration'];

        $donnees = $entityManager->getRepository(Donnees::class)->find($donneesId);
        if (!$donnees) {
            return $this->json(['status' => 'error', 'message' => 'Donnees not found']);
        }

        $donnees->setDuration(new \DateTime('@' . $duration));
        $entityManager->persist($donnees);
        $entityManager->flush();

        return $this->json(['status' => 'success']);
    }



    #[Route('/stop-simulation/{moduleId}', name: 'stop_simulation', methods: ['POST'])]
    public function stopSimulation($moduleId, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le module par son ID
        $module = $entityManager->getRepository(Module::class)->find($moduleId);

        if (!$module) {
            return $this->json(['status' => 'error', 'message' => 'Module not found']);
        }

        // Mettre à jour l'état de simulation du module
        $module->setIsRunning(false);

        // Enregistrez les modifications dans la BDD
        $entityManager->flush();

        return $this->json(['status' => 'success']);
    }


    #[Route('/latest-simulation', name: 'latest_simulation', methods: ['GET'])]
    public function getLatestSimulation(EntityManagerInterface $entityManager): Response
    {
        // Récupérez la dernière entité Donnees
        $donnees = $entityManager->getRepository(Donnees::class)->findOneBy([], ['id' => 'desc']);

        if (!$donnees) {
            return $this->json(['status' => 'error', 'message' => 'Aucune donnée de simulation trouvée']);
        }

        // Transformez l'entité en tableau pour l'envoyer en JSON
        $data = [
            'latestSimulation' => [
                'Nsimul' => $donnees->getNsimul(),
                'Temperature' => $donnees->getTemperature(),
                'Velocity' => $donnees->getVelocity(),
                'Flow' => $donnees->getFlow(),
                'Energy' => $donnees->getEnergy(),
                'Failure' => $donnees->getFailure(),
                'Start' => $donnees->getStart()->format('Y-m-d\TH:i:s'),
                'Duration' => $donnees->getDuration()->format('H:i:s')
            ]
        ];

        return $this->json($data);
    }


}
