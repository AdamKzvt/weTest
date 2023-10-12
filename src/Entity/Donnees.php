<?php

namespace App\Entity;

use App\Repository\DonneesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonneesRepository::class)]
class Donnees
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nsimul = null;

    #[ORM\Column]
    private ?int $temperature = null;

    #[ORM\Column]
    private ?int $velocity = null;

    #[ORM\Column]
    private ?int $flow = null;

    #[ORM\Column]
    private ?int $energy = null;

    #[ORM\Column]
    private ?int $failure = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $duration = null;

    #[ORM\ManyToOne(inversedBy: 'donnEs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?module $module_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(int $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getVelocity(): ?int
    {
        return $this->velocity;
    }

    public function setVelocity(int $velocity): static
    {
        $this->velocity = $velocity;

        return $this;
    }

    public function getFlow(): ?int
    {
        return $this->flow;
    }

    public function setFlow(int $flow): static
    {
        $this->flow = $flow;

        return $this;
    }

    public function getEnergy(): ?int
    {
        return $this->energy;
    }

    public function setEnergy(int $energy): static
    {
        $this->energy = $energy;

        return $this;
    }

    public function getFailure(): ?int
    {
        return $this->failure;
    }

    public function setFailure(int $failure): static
    {
        $this->failure = $failure;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeInterface $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getModuleId(): ?module
    {
        return $this->module_id;
    }

    public function setModuleId(?module $module_id): static
    {
        $this->module_id = $module_id;

        return $this;
    }

    /**
     * Get the value of Nsimul
     *
     * @return ?string
     */
    public function getNsimul(): ?string
    {
        return $this->Nsimul;
    }

    /**
     * Set the value of Nsimul
     *
     * @param ?string $Nsimul
     *
     * @return self
     */
    public function setNsimul(?string $Nsimul): self
    {
        $this->Nsimul = $Nsimul;

        return $this;
    }
}
