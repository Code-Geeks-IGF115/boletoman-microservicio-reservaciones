<?php

namespace App\Entity;

use App\Repository\ButacaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ButacaRepository::class)]
class Butaca
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $codigoButaca = null;

    #[ORM\ManyToOne(inversedBy: 'butacas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Celda $celda = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigoButaca(): ?string
    {
        return $this->codigoButaca;
    }

    public function setCodigoButaca(string $codigoButaca): self
    {
        $this->codigoButaca = $codigoButaca;

        return $this;
    }

    public function getCelda(): ?Celda
    {
        return $this->celda;
    }

    public function setCelda(?Celda $celda): self
    {
        $this->celda = $celda;

        return $this;
    }

}
