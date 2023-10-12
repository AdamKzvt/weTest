<?php

namespace App\Controller;

use App\Entity\Donnees;
use App\Repository\DonneesRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HistoriqueController extends AbstractController
{
    #[Route('/historique', name: 'app_historique')]
    public function index(DonneesRepository $donneesRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {

        $searchTerm = $request->query->get('term', ''); // Récupère le terme de la recherche ou une chaîne vide si non défini
        $donnees = $donneesRepository->findAllOrderedByDateDesc();
        $donnees = $paginatorInterface ->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            9
        );
        

        return $this->render('historique/index.html.twig', [
            'controller_name' => 'HistoriqueController',
            'donnees'=> $donnees,
            'searchTerm' => $searchTerm
        ]);
    }


    #[Route('/historique/search', name: 'app_historique_search')]
    public function search(DonneesRepository $donneesRepository, Request $request): Response
    {
        $term = $request->query->get('term', '');
        $donnees = $donneesRepository->findBySearchTerm($term);

        $data = [];
        foreach ($donnees as $donnée) {
            $data[] = [
                'id' => $donnée->getId(),
                'Nsimul' => $donnée->getNsimul(),
                'temperature' => $donnée->getTemperature(),
                'velocity' => $donnée->getVelocity(),
                'flow' => $donnée->getFlow(),
                'energy' => $donnée->getEnergy(),
                'failure' => $donnée->getFailure(),
                'start' => $donnée->getStart()->format('d-m-Y H:i:s'),
                'duration' => $donnée->getDuration()->format('H:i:s'),
                'moduleId' => $donnée->getModuleId()->getId()
            ];
        }

        return new JsonResponse($data);
    }

}
