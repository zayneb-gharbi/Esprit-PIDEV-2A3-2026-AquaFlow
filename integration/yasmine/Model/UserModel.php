<?php
/**
 * Model/UserModel.php
 * Attributs privés + getters/setters uniquement (aucun SQL, aucune logique métier)
 */
class UserModel
{
    private int $id = 0;
    private string $username = '';
    private string $email = '';
    private string $password = '';
    private string $role = 'citoyen';
    private string $status = 'actif';
    private string $createdAt = '';

    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getRole(): string { return $this->role; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): string { return $this->createdAt; }

    public function setId(int $id): void { $this->id = $id; }
    public function setUsername(string $username): void { $this->username = $username; }
    public function setEmail(string $email): void { $this->email = $email; }
    public function setPassword(string $password): void { $this->password = $password; }
    public function setRole(string $role): void { $this->role = $role; }
    public function setStatus(string $status): void { $this->status = $status; }
    public function setCreatedAt(string $createdAt): void { $this->createdAt = $createdAt; }

    public static function fromArray(array $row): self
    {
        $u = new self();
        $u->setId((int) ($row['id'] ?? 0));
        $u->setUsername((string) ($row['username'] ?? ''));
        $u->setEmail((string) ($row['email'] ?? ''));
        $u->setPassword((string) ($row['password'] ?? ''));
        $u->setRole((string) ($row['role'] ?? 'citoyen'));
        $u->setStatus((string) ($row['status'] ?? 'actif'));
        $u->setCreatedAt((string) ($row['created_at'] ?? ''));
        return $u;
    }
}
