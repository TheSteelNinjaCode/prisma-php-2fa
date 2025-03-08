<?php

declare(strict_types=1);

namespace Lib\Prisma\Classes;

use DateTime;

class UserData
{
    public ?string $id;
    public ?string $name;
    public ?string $email;
    public ?string $password;
    public DateTime|string|null $emailVerified;
    public ?string $google2faSecret;
    public bool $isGoogle2faActive;
    public ?string $image;
    public DateTime|string $createdAt;
    public DateTime|string $updatedAt;
    public ?int $roleId;
    public ?UserRoleData $userRole;

    public function __construct(
        bool $isGoogle2faActive = false,
        DateTime|string $createdAt = new DateTime(),
        DateTime|string $updatedAt = new DateTime(),
        ?string $id = null,
        ?string $name = null,
        ?string $email = null,
        ?string $password = null,
        DateTime|string|null $emailVerified = null,
        ?string $google2faSecret = null,
        ?string $image = null,
        ?int $roleId = null,
        ?UserRoleData $userRole = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->emailVerified = $emailVerified;
        $this->google2faSecret = $google2faSecret;
        $this->isGoogle2faActive = $isGoogle2faActive;
        $this->image = $image;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->roleId = $roleId;
        $this->userRole = $userRole;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'emailVerified' => $this->emailVerified ? $this->emailVerified->format('Y-m-d H:i:s') : null,
            'google2faSecret' => $this->google2faSecret,
            'isGoogle2faActive' => $this->isGoogle2faActive,
            'image' => $this->image,
            'createdAt' => $this->createdAt ? $this->createdAt->format('Y-m-d H:i:s') : null,
            'updatedAt' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null,
            'roleId' => $this->roleId,
            'userRole' => $this->userRole
        ];
    }
}