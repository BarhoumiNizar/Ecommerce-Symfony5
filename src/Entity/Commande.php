<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     */
    private $client;

    /**
     * @ORM\Column(type="array")
     */
    private $produit = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $date_commande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?array
    {
        return $this->produit;
    }

    public function setProduit(array $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getDateCommande(): ?string
    {
        return $this->date_commande;
    }

    public function setDateCommande(string $date_commande): self
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

}
