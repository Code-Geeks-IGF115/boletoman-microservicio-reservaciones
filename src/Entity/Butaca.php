<?php

namespace App\Entity;

use App\Repository\ButacaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ButacaRepository::class)]
class Butaca
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ver_butacas','comprar_butacas'])]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    #[Groups(['ver_butacas','comprar_butacas'])]
    private ?string $codigoButaca = null;
    
    #[Groups(['ver_butacas'])]
    #[ORM\ManyToOne(inversedBy: 'butacas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Celda $celda = null;

    #[ORM\ManyToOne(inversedBy: 'butacas')]
    private ?CategoriaButaca $categoriaButaca = null;

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


    public function getDetalleCompraID(): ?int
    {
        return $this->detalle_compra_id;
    }

    public function setDetalleCompraID(?int $detalle_compra_id): self
    {
        $this->detalle_compra_id = $detalle_compra_id;

        return $this;
    }

    public function getCategoriaButaca(): ?CategoriaButaca
    {
        return $this->categoriaButaca;
    }

    public function setCategoriaButaca(?CategoriaButaca $categoriaButaca): self
    {
        $this->categoriaButaca = $categoriaButaca;

        return $this;

    }


}
