<?php

namespace App\Command;

use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Données; 

#[AsCommand(
    name: 'simulateModules',
    description: 'Add a short description for your command',
)]
class SimulateModulesCommand extends Command
{

        // Ces propriétés vont stocker les objets injectés
        private $moduleRepository;
        private $entityManager;
    
        // Le constructeur pour initialiser les propriétés
        public function __construct(ModuleRepository $moduleRepository, EntityManagerInterface $entityManager)
        {
            parent::__construct();
            $this->moduleRepository = $moduleRepository;
            $this->entityManager = $entityManager;
        }

        
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);
    
        // Demander à l'utilisateur de sélectionner un module
        $moduleId = $io->ask('Entrez l\'ID du module pour la simulation');
        $module = $this->moduleRepository->find($moduleId);
    
        if (!$module) {
            $io->error('Module introuvable.');
            return Command::FAILURE;
        }
    
        // Créez un nouvel objet Données et générez des valeurs aléatoires
        $donnees = new Données();
        $donnees->setDevice(rand(1, 100)); // Simuler des valeurs
        $donnees->setTemperature(rand(-10, 40));
        $donnees->setVelocity(rand(1, 100));
        $donnees->setFlow(rand(1, 100));
        $donnees->setEnergy(rand(1, 100));
        $donnees->setFailure(rand(0, 1)); // Supposons que 0 signifie aucun échec et 1 signifie un échec
        $donnees->setStart(new \DateTime());
        $donnees->setDuration(new \DateTime('@'.rand(60, 3600))); // Durée en secondes
        $donnees->setModuleId($module);
    
        $this->entityManager->persist($donnees);
        $this->entityManager->flush();
    
        $io->success('Simulation terminée avec succès!');
        return Command::SUCCESS;
    }
    
}
