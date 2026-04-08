<?php

require_once __DIR__ . '/../core/Model.php';

class User extends Model
{
    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ");
        $stmt->execute(['email' => $email]);

        return $stmt->fetch();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE username = :username
            LIMIT 1
        ");
        $stmt->execute(['username' => $username]);

        return $stmt->fetch();
    }

    public function findByPhone($phone)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE phone = :phone
            LIMIT 1
        ");
        $stmt->execute(['phone' => $phone]);

        return $stmt->fetch();
    }

    public function findByEmailOrUsername($login)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE email = :login OR username = :login
            LIMIT 1
        ");
        $stmt->execute(['login' => $login]);

        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (
                name,
                username,
                email,
                phone,
                password,
                role,
                is_verified,
                verification_code
            ) VALUES (
                :name,
                :username,
                :email,
                :phone,
                :password,
                :role,
                :is_verified,
                :verification_code
            )
        ");

        $stmt->execute([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'user',
            'is_verified' => $data['is_verified'] ?? 0,
            'verification_code' => $data['verification_code'] ?? null
        ]);

        return $this->db->lastInsertId();
    }

    public function adminCreate($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (
                name,
                username,
                email,
                phone,
                password,
                role,
                is_verified,
                verification_code
            ) VALUES (
                :name,
                :username,
                :email,
                :phone,
                :password,
                :role,
                :is_verified,
                :verification_code
            )
        ");

        $stmt->execute([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'user',
            'is_verified' => $data['is_verified'] ?? 0,
            'verification_code' => $data['verification_code'] ?? null
        ]);

        return $this->db->lastInsertId();
    }

    public function updatePassword($id, $hashedPassword)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET password = :password
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'password' => $hashedPassword
        ]);
    }

    public function updateVerification($id, $isVerified, $verificationCode = null)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET is_verified = :is_verified,
                verification_code = :verification_code
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'is_verified' => $isVerified,
            'verification_code' => $verificationCode
        ]);
    }

    public function updateProfile($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET name = :name,
                username = :username,
                email = :email,
                phone = :phone
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone']
        ]);
    }

    public function getAll()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM users
            ORDER BY id DESC
        ");

        return $stmt->fetchAll();
    }

    public function countAll()
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) AS total
            FROM users
        ");

        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function countByRole($role)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM users
            WHERE role = :role
        ");
        $stmt->execute(['role' => $role]);

        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM users
            WHERE id = :id
        ");

        return $stmt->execute(['id' => $id]);
    }
}