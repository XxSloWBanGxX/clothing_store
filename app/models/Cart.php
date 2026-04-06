<?php

require_once __DIR__ . '/../core/Model.php';

class Cart extends Model
{
    public function getItemsByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                ci.*,
                p.name,
                p.price,
                p.image,
                p.stock,
                c.name AS category_name
            FROM cart_items ci
            JOIN products p ON p.id = ci.product_id
            JOIN categories c ON c.id = p.category_id
            WHERE ci.user_id = :user_id
            ORDER BY ci.id DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getItem($userId, $productId, $selectedSize = null, $selectedColorName = null)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM cart_items
            WHERE user_id = :user_id
              AND product_id = :product_id
              AND (
                    (selected_size = :selected_size)
                    OR (selected_size IS NULL AND :selected_size IS NULL)
                  )
              AND (
                    (selected_color_name = :selected_color_name)
                    OR (selected_color_name IS NULL AND :selected_color_name IS NULL)
                  )
            LIMIT 1
        ");

        $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
            'selected_size' => $selectedSize,
            'selected_color_name' => $selectedColorName
        ]);

        return $stmt->fetch();
    }

    public function getItemById($id, $userId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM cart_items
            WHERE id = :id AND user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId
        ]);
        return $stmt->fetch();
    }

    public function add($userId, $productId, $quantity = 1, $selectedSize = null, $selectedColorName = null, $selectedColorHex = null)
    {
        $existing = $this->getItem($userId, $productId, $selectedSize, $selectedColorName);

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE cart_items
                SET quantity = quantity + :quantity
                WHERE id = :id
            ");
            return $stmt->execute([
                'quantity' => $quantity,
                'id' => $existing['id']
            ]);
        }

        $stmt = $this->db->prepare("
            INSERT INTO cart_items (
                user_id,
                product_id,
                selected_size,
                selected_color_name,
                selected_color_hex,
                quantity
            )
            VALUES (
                :user_id,
                :product_id,
                :selected_size,
                :selected_color_name,
                :selected_color_hex,
                :quantity
            )
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'product_id' => $productId,
            'selected_size' => $selectedSize,
            'selected_color_name' => $selectedColorName,
            'selected_color_hex' => $selectedColorHex,
            'quantity' => $quantity
        ]);
    }

    public function updateQuantityByCartItemId($cartItemId, $userId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeByCartItemId($cartItemId, $userId);
        }

        $stmt = $this->db->prepare("
            UPDATE cart_items
            SET quantity = :quantity
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute([
            'quantity' => $quantity,
            'id' => $cartItemId,
            'user_id' => $userId
        ]);
    }

    public function removeByCartItemId($cartItemId, $userId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM cart_items
            WHERE id = :id AND user_id = :user_id
        ");
        return $stmt->execute([
            'id' => $cartItemId,
            'user_id' => $userId
        ]);
    }

    public function clear($userId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM cart_items
            WHERE user_id = :user_id
        ");
        return $stmt->execute(['user_id' => $userId]);
    }

    public function countItems($userId)
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(quantity), 0) AS total
            FROM cart_items
            WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return (int)($row['total'] ?? 0);
    }
}