<?php

namespace App\Entity;

use App\Repository\ClienteSectorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClienteSectorRepository::class)
 */
class ClienteSector
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cliente::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_cliente;

    /**
     * @ORM\ManyToOne(targetEntity=Sector::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_sector;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCliente(): ?Cliente
    {
        return $this->id_cliente;
    }

    public function setIdCliente(?Cliente $id_cliente): self
    {
        $this->id_cliente = $id_cliente;

        return $this;
    }

    public function getIdSector(): ?Sector
    {
        return $this->id_sector;
    }

    public function setIdSector(?Sector $id_sector): self
    {
        $this->id_sector = $id_sector;

        return $this;
    }
}
