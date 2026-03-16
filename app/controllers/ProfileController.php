<?php

class ProfileController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('login');
        }

        $this->view('profile/index', ['user' => $_SESSION['user']]);
    }
}