<?php

namespace App\Entity;

use App\Repository\FavorieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FavorieRepository::class)
 */
class Favorie
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
    private $favories = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFavories(): ?array
    {
        return $this->favories;
    }

    public function setFavories(array $favories): self
    {
        $this->favories = $favories;

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
