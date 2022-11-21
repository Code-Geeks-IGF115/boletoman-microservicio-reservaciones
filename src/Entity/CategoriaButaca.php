<?php

namespace App\Entity;

use App\Repository\CategoriaButacaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriaButacaRepository::class)]
class CategoriaButaca
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['ver_evento','ver_categoria'])]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    #[Groups(['ver_evento','ver_categoria'])]
    private ?string $codigo = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: false)]
    #[Groups(['ver_evento','ver_categoria'])]
    private ?string $precioUnitario = null;

    #[ORM\Column(length: 25)]
    #[Groups(['ver_evento','ver_categoria', 'ver_sala_de_eventos'])]
    private ?string $nombre = null;
    
    #[ORM\ManyToOne(inversedBy: 'categoriaButacas')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['ver_categoria'])]
    private ?SalaDeEventos $salaDeEventos = null;
    
    #[ORM\OneToMany(mappedBy: 'categoriaButaca', targetEntity: Celda::class)]
    #[Groups(['ver_categoria','ver_evento'])]
    private Collection $celdas;

    #[ORM\OneToMany(mappedBy: 'categoriaButaca', targetEntity: Butaca::class)]
    private Collection $butacas;

    public function __construct()
    {
        $this->celdas = new ArrayCollection();
        $this->descuentos = new ArrayCollection();
        $this->butacas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getPrecioUnitario(): ?string
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario(?string $precioUnitario): self
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getSalaDeEventos(): ?SalaDeEventos
    {
        return $this->salaDeEventos;
    }

    public function setSalaDeEventos(?SalaDeEventos $salaDeEventos): self
    {
        $this->salaDeEventos = $salaDeEventos;

        return $this;
    }

    /**
     * @return Collection<int, Celda>
     */
    public function getCeldas(): Collection
    {
        return $this->celdas;
    }

    public function addCelda(Celda $celda): self
    {
        if (!$this->celdas->contains($celda)) {
            $this->celdas->add($celda);
            $celda->setCategoriaButaca($this);
        }

        return $this;
    }

    public function removeCelda(Celda $celda): self
    {
        if ($this->celdas->removeElement($celda)) {
            // set the owning side to null (unless already changed)
            if ($celda->getCategoriaButaca() === $this) {
                $celda->setCategoriaButaca(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Butaca>
     */
    public function getButacas(): Collection
    {
        return $this->butacas;
    }

    public function addButaca(Butaca $butaca): self
    {
        if (!$this->butacas->contains($butaca)) {
            $this->butacas->add($butaca);
            $butaca->setCategoriaButaca($this);
        }

        return $this;
    }

    public function removeButaca(Butaca $butaca): self
    {
        if ($this->butacas->removeElement($butaca)) {
            // set the owning side to null (unless already changed)
            if ($butaca->getCategoriaButaca() === $this) {
                $butaca->setCategoriaButaca(null);
            }
        }

        return $this;
    }
    
}
