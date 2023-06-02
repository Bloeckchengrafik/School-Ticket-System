<?php

namespace modules\auth;

class User
{
    // This is the user
    public int $userID;
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $accountClass;
    public string $createdAt;

    function __construct($userID, $firstName, $lastName, $email, $accountClass, $createdAt) {
        $this->userID = $userID;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->accountClass = $accountClass;
        $this->createdAt = $createdAt;
    }
}