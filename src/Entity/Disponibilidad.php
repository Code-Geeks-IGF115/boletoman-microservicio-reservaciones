<?php

namespace App\Entity;
use Doctrine\ORM\Mapping\UniqueConstraint;
use App\Repository\DisponibilidadRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DisponibilidadRepository::class)]
class Disponibilidad
{
    
    #[ORM\Id]
    #[Groups(['ver_butacas','comprar_butacas'])]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['ver_butacas', 'ver_disponibilidad','comprar_butacas'])]
    private ?string $disponible = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['ver_butacas', 'comprar_butacas'])]
    private ?string $idEvento = null;
    

    #[ORM\Column(nullable: true)]
    #[Groups(['ver_butacas', 'comprar_butacas'])]
    // #[ORM\JoinColumn(nullable: true)]
    private ?int $idDetalleCompra = null;

    #[ORM\ManyToOne]
    #[Groups(['ver_butacas', 'comprar_butacas'])]
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
