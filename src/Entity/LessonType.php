<?php

namespace App\Entity;

use App\Repository\LessonTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonTypeRepository::class)]
class LessonType
{

    const FIXED = 'FX';
    const CHOOSABLE = 'CH';

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 2)]
    private $code;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
