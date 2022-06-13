<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(name: "subject", referencedColumnName: "id", nullable: false)]
    private $subject;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "teacher", referencedColumnName: "id", nullable: false)]
    private $teacher;

    #[ORM\ManyToOne(targetEntity: LessonType::class)]
    #[ORM\JoinColumn(name: "lesson_type", referencedColumnName: "code", nullable: false)]
    private $lessonType;

    #[ORM\Column(type: 'integer')]
    private $dayOfWeek;

    #[ORM\Column(type: 'integer')]
    private $time;

    #[ORM\Column(type: 'integer')]
    private $studentsCount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getLessonType(): ?LessonType
    {
        return $this->lessonType;
    }

    public function setLessonType(?LessonType $lessonType): self
    {
        $this->lessonType = $lessonType;

        return $this;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getStudentsCount(): ?int
    {
        return $this->studentsCount;
    }

    public function setStudentsCount(int $studentsCount): self
    {
        $this->studentsCount = $studentsCount;

        return $this;
    }
}
