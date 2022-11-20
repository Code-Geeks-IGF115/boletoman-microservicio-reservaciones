<?php

namespace App\Entity;

use App\Repository\DisponibilidadRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DisponibilidadRepository::class)]
class Disponibilidad
{
    #[Groups(['comprar_butacas'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[Groups(['ver_disponibilidad','comprar_butacas'])]
    #[ORM\Column(length: 100)]
    private ?string $disponible = null;

    #[Groups(['comprar_butacas'])]
    #[ORM\Column(nullable: true)]
    private ?int $idEvento = null;

    #[Groups(['comprar_butacas'])]
    #[ORM\Column]
    #[ORM\JoinColumn(nullable: true)]
    private ?int $idDetalleCompra = null;

    #[Groups(['comprar_butacas'])]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Butaca $butaca = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisponible(): ?string
    {
        return $this->disponible;
    }

    public function setDisponible(string $disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getIdEvento(): ?int
    {
        return $this->idEvento;
    }

    public function setIdEvento(?int $idEvento): self
    {
        $this->idEvento = $idEvento;

        return $this;
    }

    public function getIdDetalleCompra(): ?int
    {
        return $this->idDetalleCompra;
    }

    public function setIdDetalleCompra(int $idDetalleCompra): self
    {
        $this->idDetalleCompra = $idDetalleCompra;

        return $this;
    }

    public function getButaca(): ?Butaca
    {
        return $this->butaca;
    }

    public function setButaca(?Butaca $butaca): self
    {
        $this->butaca = $butaca;

        return $this;
    }
}
