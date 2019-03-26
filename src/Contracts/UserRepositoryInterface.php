<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Contracts;

use Lcobucci\JWT\Token;
use MyParcelCom\Contacts\Contact;

interface UserRepositoryInterface
{
    /**
     * Create a new user model.
     *
     * @param string  $username
     * @param string  $password
     * @param Contact $contact
     * @param string  $status
     * @return UserInterface
     */
    public function make(
        string $username,
        string $password,
        Contact $contact,
        string $status
    ): UserInterface;

    /**
     * Persist the user model.
     *
     * @param UserInterface $user
     * @return UserInterface
     */
    public function persist(UserInterface $user): UserInterface;

    /**
     * Create and persist a new user model.
     *
     * @param string  $username
     * @param string  $password
     * @param Contact $contact
     * @param string  $status
     * @return UserInterface
     */
    public function create(
        string $username,
        string $password,
        Contact $contact,
        string $status
    ): UserInterface;

    /**
     * Create an authenticated user model from an access token.
     *
     * @param Token $token
     * @return UserInterface
     */
    public function makeAuthenticatedUser(Token $token): UserInterface;
}
