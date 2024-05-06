<?php

namespace App\Entity;

use App\Repository\TimetableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimetableRepository::class)]
class Timetable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var list<list> Timetable
     */
    #[ORM\Column(nullable: true)]
    private ?array $timetable = null;

    #[ORM\OneToOne(inversedBy: 'timetable', cascade: ['persist'])]
    private ?SchoolClass $SchoolClass = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimetable(): ?array
    {
        return $this->timetable;
    }

    public function setTimetable(?array $timetable): static
    {
        $this->timetable = $timetable;

        return $this;
    }

    public function getSchoolClass(): ?SchoolClass
    {
        return $this->SchoolClass;
    }

    public function setSchoolClass(?SchoolClass $SchoolClass): static
    {
        $this->SchoolClass = $SchoolClass;

        return $this;
    }
}
