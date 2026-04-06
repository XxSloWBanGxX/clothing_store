<?php

require_once __DIR__ . '/../core/Model.php';

class ProductImage extends Model
{
    public function getByProductId($productId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM product_images
            WHERE product_id = :product_id
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM product_images
            WHERE id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($productId, $imagePath, $sortOrder = 0)
    {
        $stmt = $this->db->prepare("
            INSERT INTO product_images (product_id, image_path, sort_order)
            VALUES (:product_id, :image_path, :sort_order)
        ");

        return $stmt->execute([
            'product_id' => $productId,
            'image_path' => $imagePath,
            'sort_order' => $sortOrder
        ]);
    }

    public function deleteByProductId($productId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM product_images
            WHERE product_id = :product_id
        ");
        return $stmt->execute(['product_id' => $productId]);
    }

    public function deleteById($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM product_images
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }

    public function getNextSortOrder($productId)
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(MAX(sort_order), 0) + 1 AS next_order
            FROM product_images
            WHERE product_id = :product_id
        ");
        $stmt->execute(['product_id' => $productId]);
        $row = $stmt->fetch();

        return (int)($row['next_order'] ?? 1);
    }
}