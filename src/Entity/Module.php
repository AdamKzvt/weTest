<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255, unique: true)]
    private ?string $Nom = null;
    

    #[ORM\Column]
    private ?bool $isRunning = false;

    #[ORM\OneToMany(mappedBy: 'module_id', targetEntity: Donnees::class, orphanRemoval: true)]
    private Collection $donnEs;

    public function __construct()
    {
        $this->donnEs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    /**
     * @return Collection<int, Donnees>
     */
    public function getDonnEs(): Collection
    {
        return $this->donnEs;
    }

    public function addDonnE(Donnees $donnE): static
    {
        if (!$this->donnEs->contains($donnE)) {
            $this->donnEs->add($donnE);
            $donnE->setModuleId($this);
        }

        return $this;
    }

    public function removeDonnE(Donnees $donnE): static
    {
        if ($this->donnEs->removeElement($donnE)) {
            // set the owning side to null (unless already changed)
            if ($donnE->getModuleId() === $this) {
                $donnE->setModuleId(null);
            }
        }

        return $this;
    }

    public function getIsRunning(): ?bool
    {
        return $this->isRunning;
    }

    public function setIsRunning(bool $isRunning): static
    {
        $this->isRunning = $isRunning;

        return $this;
    }
}
