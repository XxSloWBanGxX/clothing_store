<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        $this->view('auth/login', [
            'title' => 'Вхід',
            'errors' => [],
            'old' => []
        ]);
    }

    public function register()
    {
        $this->view('auth/register', [
            'title' => 'Реєстрація',
            'errors' => [],
            'old' => []
        ]);
    }

    public function loginPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=login');
        }

        $login = trim($_POST['login'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $errors = [];

        if ($login === '') {
            $errors['login'] = 'Введи email або username';
        }

        if ($password === '') {
            $errors['password'] = 'Введи пароль';
        }

        if (!empty($errors)) {
            $this->view('auth/login', [
                'title' => 'Вхід',
                'errors' => $errors,
                'old' => ['login' => $login]
            ]);
            return;
        }

        $user = $this->userModel->findByEmailOrUsername($login);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->view('auth/login', [
                'title' => 'Вхід',
                'errors' => ['general' => 'Невірний email, username або пароль'],
                'old' => ['login' => $login]
            ]);
            return;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'is_verified' => $user['is_verified']
        ];

        if ($user['role'] === 'admin') {
            $this->redirect('index.php?url=admin');
            return;
        }

        $this->redirect('index.php?url=profile');
    }

    public function registerPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?url=register');
        }

        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Введи імʼя';
        }

        if ($username === '') {
            $errors['username'] = 'Введи username';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            $errors['username'] = 'Username: 3-20 символів, тільки букви, цифри, _';
        }

        if ($email === '') {
            $errors['email'] = 'Введи email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Некоректний email';
        }

        if ($phone === '') {
            $errors['phone'] = 'Введи номер телефону';
        } elseif (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
            $errors['phone'] = 'Некоректний номер телефону';
        }

        if ($password === '') {
            $errors['password'] = 'Введи пароль';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Пароль має містити мінімум 6 символів';
        }

        if ($confirmPassword === '') {
            $errors['confirm_password'] = 'Підтверди пароль';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Паролі не співпадають';
        }

        if ($this->userModel->findByUsername($username)) {
            $errors['username'] = 'Такий username вже зайнятий';
        }

        if ($this->userModel->findByEmail($email)) {
            $errors['email'] = 'Користувач з таким email вже існує';
        }

        if ($this->userModel->findByPhone($phone)) {
            $errors['phone'] = 'Користувач з таким номером вже існує';
        }

        if (!empty($errors)) {
            $this->view('auth/register', [
                'title' => 'Реєстрація',
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'phone' => $phone
                ]
            ]);
            return;
        }

        $this->userModel->create($name, $username, $email, $phone, $password);

        $user = $this->userModel->findByEmail($email);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'is_verified' => $user['is_verified']
        ];

        $this->redirect('index.php?url=profile');
    }

    public function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
        $this->redirect('index.php');
    }
}