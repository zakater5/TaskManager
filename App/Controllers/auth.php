<?php

namespace App\Controllers;

class Auth {
    private $db; // globalna spremenljivka za podatkovno bazo katera je potem dostopna celem klasa (ampak le tem klasu ker je private)

    public function __construct($pdo) { // konstruktor kateri pripravi instanco podatkovne baze za uporabo v klasu
        $this->db = $pdo; // poda objekt podatkovne baze globalni spremenljivki v tem kalsu
    }

    public function login() { // funkcijo za login katera je poklicana na zahtevo /login [POST]
        session_start(); // zacne sejo (morem klicati to funkcijo vsakic ko delam s sejami)

        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) { // preverim ce je uporanik ze trenutno prijavljen
            header('Location: /'); // ce je prijavljen ga redirektam na home page
            exit; // zakljucim
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // preverim ali je zahteva ss metodo POST
            // Get username and password from POST data
            $username = $_POST['username'];
            $password = $_POST['password'];

            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->execute(['username' => $username]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($user && $password === $user['password']) { // ce sta vpisano geslo in geslo katero je v podat. bazi ista
                    // Login successful
                    $_SESSION['loggedin'] = true; // set-am spremenljivko loggedin na true, pomeni da je zdaj uporabnik prijavljen
                    $_SESSION['username'] = $user['username']; // set-am ime (username) na ime uporabnika
                    header('Location: /'); // ga redirektam na home page (dashboard)
                    exit; // zakljucim
                } else {
                    // Invalid username or password
                    $error = 'Invalid username or password'; // uporabniku poslem error da je neki narobe napisu
                    require __DIR__ . '/../Views/login.php'; // serviram login.php
                }
            } catch (\PDOException $e) { // ce pride do napake
                die("Error executing query: " . $e->getMessage()); // die funkcija terminira skripto
            }
        } else { // ce je zahteva s metodo GET, mu serviram obrazec za prijavo
            require __DIR__ . '/../Views/login.php';
        }
    }

    public function logout() { // ce je klicana funkcija za odjavo (/logout [POST]), uporabnika odjavim in ga poslem na home page
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
