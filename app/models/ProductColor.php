<?php

require_once __DIR__ . '/../core/Model.php';

class ProductColor extends Model
{
    public function getByProductId($productId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM product_colors
            WHERE product_id = :product_id
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function create($productId, $colorName, $colorHex = null, $sortOrder = 0)
    {
        $stmt = $this->db->prepare("
            INSERT INTO product_colors (product_id, color_name, color_hex, sort_order)
            VALUES (:product_id, :color_name, :color_hex, :sort_order)
        ");

        return $stmt->execute([
            'product_id' => $productId,
            'color_name' => $colorName,
            'color_hex' => $colorHex,
            'sort_order' => $sortOrder
        ]);
    }

    public function deleteByProductId($productId)
    {
        $stmt = $this->db->prepare("DELETE FROM product_colors WHERE product_id = :product_id");
        return $stmt->execute(['product_id' => $productId]);
    }
}