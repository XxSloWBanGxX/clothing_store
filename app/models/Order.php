<?php

require_once __DIR__ . '/../core/Model.php';

class Order extends Model
{
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO orders (
                user_id,
                full_name,
                phone,
                email,
                city,
                address_line,
                delivery_method,
                payment_method,
                comment,
                total_amount,
                status
            ) VALUES (
                :user_id,
                :full_name,
                :phone,
                :email,
                :city,
                :address_line,
                :delivery_method,
                :payment_method,
                :comment,
                :total_amount,
                :status
            )
        ");

        $stmt->execute([
            'user_id' => $data['user_id'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'city' => $data['city'],
            'address_line' => $data['address_line'],
            'delivery_method' => $data['delivery_method'],
            'payment_method' => $data['payment_method'],
            'comment' => $data['comment'],
            'total_amount' => $data['total_amount'],
            'status' => $data['status'] ?? 'new'
        ]);

        return $this->db->lastInsertId();
    }

    public function addItem($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO order_items (
                order_id,
                product_id,
                product_name,
                product_price,
                quantity,
                selected_size,
                selected_color_name,
                selected_color_hex
            ) VALUES (
                :order_id,
                :product_id,
                :product_name,
                :product_price,
                :quantity,
                :selected_size,
                :selected_color_name,
                :selected_color_hex
            )
        ");

        return $stmt->execute([
            'order_id' => $data['order_id'],
            'product_id' => $data['product_id'],
            'product_name' => $data['product_name'],
            'product_price' => $data['product_price'],
            'quantity' => $data['quantity'],
            'selected_size' => $data['selected_size'],
            'selected_color_name' => $data['selected_color_name'],
            'selected_color_hex' => $data['selected_color_hex']
        ]);
    }

    public function findById($id, $userId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM orders
            WHERE id = :id AND user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute([
            'id' => $id,
            'user_id' => $userId
        ]);

        return $stmt->fetch();
    }

    public function getItemsByOrderId($orderId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM order_items
            WHERE order_id = :order_id
            ORDER BY id ASC
        ");
        $stmt->execute(['order_id' => $orderId]);

        return $stmt->fetchAll();
    }

    public function getAllOrders()
    {
        $stmt = $this->db->query("
            SELECT o.*, u.username
            FROM orders o
            LEFT JOIN users u ON u.id = o.user_id
            ORDER BY o.id DESC
        ");

        return $stmt->fetchAll();
    }

    public function getOrderWithItems($orderId)
    {
        $stmt = $this->db->prepare("
            SELECT o.*, u.username
            FROM orders o
            LEFT JOIN users u ON u.id = o.user_id
            WHERE o.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $order['items'] = $this->getItemsByOrderId($orderId);

        return $order;
    }

    public function updateStatus($orderId, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE orders
            SET status = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $orderId,
            'status' => $status
        ]);
    }

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM orders
            WHERE user_id = :user_id
            ORDER BY id DESC
        ");
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function getUserOrderWithItems($orderId, $userId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM orders
            WHERE id = :id AND user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute([
            'id' => $orderId,
            'user_id' => $userId
        ]);

        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $order['items'] = $this->getItemsByOrderId($orderId);

        return $order;
    }
}