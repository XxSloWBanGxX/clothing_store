<?php

require_once __DIR__ . '/../core/Model.php';

class User extends Model
{
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public function findByPhone($phone)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = :phone LIMIT 1");
        $stmt->execute(['phone' => $phone]);
        return $stmt->fetch();
    }

    public function findByLogin($login)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM users
            WHERE email = :login OR username = :login
            LIMIT 1
        ");
        $stmt->execute(['login' => $login]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM users");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function countByRole($role)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM users WHERE role = :role");
        $stmt->execute(['role' => $role]);
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function create($name, $username, $email, $phone, $password, $role = 'user')
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $verificationCode = (string)rand(100000, 999999);

        $stmt = $this->db->prepare("
            INSERT INTO users (name, username, email, phone, password, role, is_verified, verification_code)
            VALUES (:name, :username, :email, :phone, :password, :role, 0, :verification_code)
        ");

        return $stmt->execute([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => $hashedPassword,
            'role' => $role,
            'verification_code' => $verificationCode
        ]);
    }

    public function adminCreate($data)
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            INSERT INTO users (name, username, email, phone, password, role, is_verified, verification_code)
            VALUES (:name, :username, :email, :phone, :password, :role, :is_verified, NULL)
        ");

        return $stmt->execute([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $hashedPassword,
            'role' => $data['role'],
            'is_verified' => $data['is_verified']
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}