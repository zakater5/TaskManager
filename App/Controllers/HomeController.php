<?php

namespace App\Controllers;

class HomeController {
    public function index($vars) {
        session_start();

        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
            // If already logged in, redirect to home page
            require __DIR__ . '/../../Public/index.html'; // if logged in then serve dashboard / index.html
            exit;
        }
        // if not logged in serve welcome page, where user can login or possible make account
        require __DIR__ . '/../../Public/welcome.html';
    }
}
