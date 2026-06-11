<?php

require_once __DIR__ . '/../core/Model.php';

class Product extends Model
{
    public function getFeatured($limit = 4)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.is_featured = 1
            ORDER BY p.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM products");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function countInStock()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM products WHERE stock > 0");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function countFeatured()
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM products WHERE is_featured = 1");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function getAllForAdmin()
    {
        $stmt = $this->db->query("
            SELECT p.*, c.name AS category_name
            FROM products p
            JOIN categories c ON c.id = p.category_id
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll();
    }

    public function countFiltered($filters = [])
    {
        $sql = "
            SELECT COUNT(*) as total
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND c.slug = :category";
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if ($filters['min_price'] !== null && $filters['min_price'] !== '') {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }

        if ($filters['max_price'] !== null && $filters['max_price'] !== '') {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        if (!empty($filters['in_stock'])) {
            $sql .= " AND p.stock > 0";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function getFiltered($filters = [], $limit = 12, $offset = 0)
    {
        $sql = "
            SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND c.slug = :category";
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if ($filters['min_price'] !== null && $filters['min_price'] !== '') {
            $sql .= " AND p.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }

        if ($filters['max_price'] !== null && $filters['max_price'] !== '') {
            $sql .= " AND p.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }

        if (!empty($filters['in_stock'])) {
            $sql .= " AND p.stock > 0";
        }

        $sort = $filters['sort'] ?? 'newest';

        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'name_asc':
                $sql .= " ORDER BY p.name ASC";
                break;
            default:
                $sql .= " ORDER BY p.id DESC";
                break;
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function findBySlug($slug)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE p.slug = :slug
            LIMIT 1
        ");
        $stmt->execute(['slug' => $slug]);

        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO products (category_id, name, slug, description, price, image, stock, is_featured)
            VALUES (:category_id, :name, :slug, :description, :price, :image, :stock, :is_featured)
        ");

        $stmt->execute([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'price' => $data['price'],
            'image' => $data['image'],
            'stock' => $data['stock'],
            'is_featured' => $data['is_featured']
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE products
            SET category_id = :category_id,
                name = :name,
                slug = :slug,
                description = :description,
                price = :price,
                image = :image,
                stock = :stock,
                is_featured = :is_featured
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
            'price' => $data['price'],
            'image' => $data['image'],
            'stock' => $data['stock'],
            'is_featured' => $data['is_featured']
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function slugExists($slug, $excludeId = null)
    {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT id FROM products WHERE slug = :slug AND id != :id LIMIT 1");
            $stmt->execute([
                'slug' => $slug,
                'id' => $excludeId
            ]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM products WHERE slug = :slug LIMIT 1");
            $stmt->execute(['slug' => $slug]);
        }

        return (bool)$stmt->fetch();
    }
}