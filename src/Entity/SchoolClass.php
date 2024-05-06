<?php

namespace App\Entity;

use App\Repository\SchoolClassRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchoolClassRepository::class)]
class SchoolClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $class_name = null;

    #[ORM\Column(length: 255)]
    private ?string $teacher_name = null;

    #[ORM\Column(nullable: true)]
    private ?string $promotion_year = null;

    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'school_class')]
    private Collection $students;

    #[ORM\OneToOne(mappedBy: 'SchoolClass', cascade: ['persist', 'remove'])]
    private ?Timetable $timetable = null;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClassName(): ?string
    {
        return $this->class_name;
    }

    public function setClassName(string $class_name): static
    {
        $this->class_name = $class_name;

        return $this;
    }

    public function getTeacherName(): ?string
    {
        return $this->teacher_name;
    }

    public function setTeacherName(string $teacher_name): static
    {
        $this->teacher_name = $teacher_name;

        return $this;
    }

    public function getPromotionYear(): ?int
    {
        return $this->promotion_year;
    }

    public function setPromotionYear(?int $promotion_year): static
    {
        $this->promotion_year = $promotion_year;

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->setSchoolClass($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getSchoolClass() === $this) {
                $student->setSchoolClass(null);
            }
        }

        return $this;
    }

    public function getTimetable(): ?Timetable
    {
        return $this->timetable;
    }

    public function setTimetable(?Timetable $timetable): static
    {
        // unset the owning side of the relation if necessary
        if ($timetable === null && $this->timetable !== null) {
            $this->timetable->setSchoolClass(null);
        }

        // set the owning side of the relation if necessary
        if ($timetable !== null && $timetable->getSchoolClass() !== $this) {
            $timetable->setSchoolClass($this);
        }

        $this->timetable = $timetable;

        return $this;
    }
}
