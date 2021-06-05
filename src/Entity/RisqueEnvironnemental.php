<?php

namespace App\Entity;

use App\Repository\RisqueEnvironnementalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RisqueEnvironnementalRepository::class)
 */
class RisqueEnvironnemental
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
