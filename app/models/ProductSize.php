<?php

require_once __DIR__ . '/../core/Model.php';

class ProductSize extends Model
{
    public function getByProductId($productId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM product_sizes
            WHERE product_id = :product_id
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll();
    }

    public function create($productId, $sizeLabel, $sortOrder = 0)
    {
        $stmt = $this->db->prepare("
            INSERT INTO product_sizes (product_id, size_label, sort_order)
            VALUES (:product_id, :size_label, :sort_order)
        ");

        return $stmt->execute([
            'product_id' => $productId,
            'size_label' => $sizeLabel,
            'sort_order' => $sortOrder
        ]);
    }

    public function deleteByProductId($productId)
    {
        $stmt = $this->db->prepare("DELETE FROM product_sizes WHERE product_id = :product_id");
        return $stmt->execute(['product_id' => $productId]);
    }
}