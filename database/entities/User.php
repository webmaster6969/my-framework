<?php

namespace Database\entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "datetime")]
    private \DateTime $created_at;

    #[ORM\Column(type: "datetime")]
    private \DateTime $updated_at;

    public function __construct(string $name, string $email, string $password)
    {
        $this->name       = $name;
        $this->email      = $email;
        $this->password   = $password;
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    // Здесь можно добавить геттеры/сеттеры при необходимости
}