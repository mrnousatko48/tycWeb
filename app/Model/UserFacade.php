<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Security\Passwords;
use Nette\Security\Identity;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;

final class UserFacade implements Authenticator
{
    public const PasswordMinLength = 6;

    /** @var array<string, array{email: string, passwordHash: string, role: string}> */
    private array $users = [];

    public function __construct(
        private Passwords $passwords,
        private Explorer $database,
    ) {
        $this->database = $database;
    }


    /**
     * Adds a new user or throws exception if duplicate.
     *
     * @throws DuplicateNameException
     */
    public function add(
        string $username,
        string $firstname,
        string $lastname,
        string $email,
        string $password,
        string $role
    ): void {
        $existing = $this->database->table('users')
            ->where('username = ? OR email = ?', $username, $email)
            ->fetch();

        if ($existing) {
            if ($existing->username === $username) {
                throw new DuplicateNameException('Uživatelské jméno již existuje.');
            }
            if ($existing->email === $email) {
                throw new DuplicateNameException('Email již existuje.');
            }
        }

        $this->database->table('users')->insert([
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => $this->passwords->hash($password),
            'role' => $role,
        ]);
    }

    public function authenticate(string $username, string $password): Identity
    {
        $row = $this->database->table('users')
            ->where('username', $username)
            ->fetch();

        if (!$row) {
            throw new AuthenticationException('Uživatel neexistuje.');
        }

        if (!$this->passwords->verify($password, $row->password)) {
            throw new AuthenticationException('Nesprávné heslo.');
        }

        return new Identity(
            $row->id,
            $row->role,
            [
                'username' => $row->username,
                'email' => $row->email,
                'firstname' => $row->firstname,
                'lastname' => $row->lastname,
            ]
        );
    }
    public function getUsers(): \Nette\Database\Table\Selection
    {
        return $this->database->table('users');
    }

    public function getUserById(int $id): ?\Nette\Database\Table\ActiveRow
    {
        return $this->database->table('users')->get($id);
    }

    public function updateUser(int $id, \Nette\Utils\ArrayHash $values): void
    {
        $this->database->table('users')->get($id)->update((array) $values);
    }
}

class DuplicateNameException extends \Exception
{
}
