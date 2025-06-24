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
    
        // Ulož do databáze
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
        // Najdi uživatele v databázi
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
            $row->username,
            $row->role,
            ['email' => $row->email]
        );
    }

}

class DuplicateNameException extends \Exception
{
}

