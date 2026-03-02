<?php

class Contact
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $email = null;
    private ?string $phone_number = null;

    // Accesseurs (Getters)

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }

    public function getEmail(): ?string { return $this->email; }

    public function getPhoneNumber(): ?string { return $this->phone_number; }

    // Mutateurs (Setters)

    public function setId(?int $id): void { $this->id = $id; }

    public function setName(?string $name): void { $this->name = $name; }

    public function setEmail(?string $email): void { $this->email = $email; }

    public function setPhoneNumber(?string $phone_number): void { $this->phone_number = $phone_number; }

    // Conversion en chaîne

    public function __toString(): string
    {
        return sprintf(
            "[%d] %s - %s (%s)",
            $this->id ?? 0,
            $this->name ?? 'Pas de nom',
            $this->email ?? 'Pas d\'email',
            $this->phone_number ?? 'Pas de numéro de tél'
        );
    }
}