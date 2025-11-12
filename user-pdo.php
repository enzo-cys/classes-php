<?php

class Userpdo
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $isConnected = false;
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=classes', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die('Erreur de connexion PDO: ' . $e->getMessage());
        }
    }

    public function register($login, $password, $email, $firstname, $lastname)
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$login, $passwordHash, $email, $firstname, $lastname])) {
            return $this->connect($login, $password) ? $this->getAllInfos() : null;
        }
        return null;
    }

    public function connect($login, $password)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->login = $user['login'];
            $this->email = $user['email'];
            $this->firstname = $user['firstname'];
            $this->lastname = $user['lastname'];
            $this->isConnected = true;
            return true;
        }
        return false;
    }

    public function disconnect()
    {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
        $this->isConnected = false;
    }

    public function delete()
    {
        if ($this->isConnected && $this->id) {
            $stmt = $this->pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
            $stmt->execute([$this->id]);
            $this->disconnect();
        }
    }

    public function update($login, $password, $email, $firstname, $lastname)
    {
        if ($this->isConnected && $this->id) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare("UPDATE utilisateurs 
              SET login=?, password=?, email=?, firstname=?, lastname=? 
              WHERE id=?");

            if ($stmt->execute([$login, $passwordHash, $email, $firstname, $lastname, $this->id])) {
                $this->connect($login, $password);
                return true;
            }
        }
        return false;
    }

    public function isConnected()
    {
        return $this->isConnected;
    }

    public function getAllInfos()
    {
        if ($this->isConnected) {
            return [
                "id" => $this->id,
                "login" => $this->login,
                "email" => $this->email,
                "firstname" => $this->firstname,
                "lastname" => $this->lastname,
            ];
        }
        return null;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }
}

// // Exemples de test (à activer/décommenter pour tester)
// $user = new Userpdo();
// $user->register('Marie', 'secret', 'marie@mail.com', 'Marie', 'Curie');
// $user->connect('Marie', 'secret');
// print_r($user->getAllInfos());
// $user->update('Marie2', 'newsecr', 'marie2@mail.com', 'Marie', 'Curie-2');
// $user->delete();