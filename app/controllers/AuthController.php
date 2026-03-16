<?php

class AuthController extends Controller
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $user = $userModel->findByEmail($_POST['email']);

            if ($user && password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user'] = $user;
                $this->redirect('');
            }

            $_SESSION['error'] = 'Невірний email або пароль.';
            $this->redirect('login');
        }

        $this->view('auth/login');
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();

            if ($userModel->findByEmail($_POST['email'])) {
                $_SESSION['error'] = 'Користувач з таким email вже існує.';
                $this->redirect('register');
            }

            $userModel->create($_POST);
            $_SESSION['success'] = 'Реєстрація успішна. Увійдіть у систему.';
            $this->redirect('login');
        }

        $this->view('auth/register');
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        $this->redirect('');
    }
}