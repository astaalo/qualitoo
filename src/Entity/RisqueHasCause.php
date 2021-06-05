<?php

namespace App\Entity;

use App\Repository\RisqueHasCauseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RisqueHasCauseRepository::class)
 */
class RisqueHasCause
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
