<?php

class User
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    private $isConnected = false;
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = new mysqli('localhost', 'root', '', 'classes');
        if ($this->mysqli->connect_error) {
            die('Erreur de connexion MySQL: ' . $this->mysqli->connect_error);
        }
    }

    public function register($login, $password, $email, $firstname, $lastname)
    {
        $login = $this->mysqli->real_escape_string($login);
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $email = $this->mysqli->real_escape_string($email);
        $firstname = $this->mysqli->real_escape_string($firstname);
        $lastname = $this->mysqli->real_escape_string($lastname);

        $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname)
                VALUES ('$login', '$passwordHash', '$email', '$firstname', '$lastname')";
        if ($this->mysqli->query($sql)) {
            return $this->connect($login, $password) ? $this->getAllInfos() : null;
        }
        return null;
    }

    public function connect($login, $password)
    {
        $login = $this->mysqli->real_escape_string($login);
        $sql = "SELECT * FROM utilisateurs WHERE login='$login'";
        $result = $this->mysqli->query($sql);
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->login = $row['login'];
                $this->email = $row['email'];
                $this->firstname = $row['firstname'];
                $this->lastname = $row['lastname'];
                $this->isConnected = true;
                return true;
            }
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
            $id = intval($this->id);
            $sql = "DELETE FROM utilisateurs WHERE id='$id'";
            $this->mysqli->query($sql);
            $this->disconnect();
        }
    }

    public function update($login, $password, $email, $firstname, $lastname)
    {
        if ($this->isConnected && $this->id) {
            $login = $this->mysqli->real_escape_string($login);
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $email = $this->mysqli->real_escape_string($email);
            $firstname = $this->mysqli->real_escape_string($firstname);
            $lastname = $this->mysqli->real_escape_string($lastname);
            $id = intval($this->id);

            $sql = "UPDATE utilisateurs SET 
                login='$login',
                password='$passwordHash',
                email='$email',
                firstname='$firstname',
                lastname='$lastname'
                WHERE id='$id'";

            if ($this->mysqli->query($sql)) {
                // Rafraîchit les attributs de l'objet
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
// $user = new User();
// $user->register('Tom13', 'azerty', 'thomas@gmail.com', 'Thomas', 'DUPONT');
// $user->connect('Tom13', 'azerty');
// print_r($user->getAllInfos());
// $user->update('TomUpdated', 'newpwd', 'tom2@gmail.com', 'Tom', 'Updated');
// $user->delete();