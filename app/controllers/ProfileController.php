<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('index.php?url=login');
        }

        $user = $this->userModel->findById($_SESSION['user']['id']);

        $this->view('profile/index', [
            'title' => 'Профіль',
            'user' => $user
        ]);
    }
}