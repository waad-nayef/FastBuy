<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dbName = 'ecommerce_db';

try {
    $bootstrapPdo = new PDO(
        "mysql:host=$host;charset=$charset",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $checkDb = $bootstrapPdo->query("SHOW DATABASES LIKE '$dbName'");
    if ($checkDb->rowCount() === 0) {

        $sqlFile = __DIR__ . '/initialize.sql';

        $sql = file_get_contents($sqlFile);
        $bootstrapPdo->exec($sql);
    }
} catch (PDOException $e) {
    die("Database initialization failed: " . $e->getMessage());
}

class Database
{
    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $dsn = "mysql:host=localhost;dbname=ecommerce_db;charset=utf8mb4";

        $this->conn = new PDO(
            $dsn,
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getAllUsers()
    {
        return $this->query("SELECT * FROM users");
    }

    public function getUserById($id)
    {
        return $this->query("SELECT * FROM users WHERE id = ?", [$id])->fetch();
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function createUser($first_name, $last_name, $email, $password, $role = 'user', $phone = null, $photo = null, $country = null, $city = null)
    {
        $sql = "INSERT INTO users (first_name, last_name, email, password, role, phone, photo, country, city)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->query($sql, [$first_name, $last_name, $email, $password, $role, $phone, $photo, $country, $city]);
    }

    public function updateUser($id, $data)
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->query($sql, $values);
    }

    public function deleteUser($id)
    {
        return $this->query("DELETE FROM users WHERE id = ?", [$id]);
    }

    public function getAllProducts()
    {
        return $this->query("SELECT * FROM products");
    }

    public function getTotalProductCount()
    {
        return $this->query("SELECT COUNT(*) as count FROM products")->fetch()['count'];
    }

    public function getProductById($id)
    {
        return $this->query("SELECT * FROM products WHERE id = ?", [$id])->fetch();
    }

    public function createProduct($name, $description, $short_description, $price, $stock, $discount, $image, $category_id)
    {
        $sql = "INSERT INTO products (name, description, short_description, price, stock, discount, image, category_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->query($sql, [$name, $description, $short_description, $price, $stock, $discount, $image, $category_id]);
    }

    public function updateProduct($id, $data)
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $sql = "UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->query($sql, $values);
    }

    public function updateProductStock($product_id, $quantity)
    {
        $product = $this->getProductById($product_id);
        if (!$product || $product['stock'] < $quantity) {
            return false;
        }

        return $this->query(
            "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?",
            [$quantity, $product_id, $quantity]
        );
    }

    public function getProductsByCategory($category_id)
    {
        return $this->query("SELECT * FROM products WHERE category_id = ?", [$category_id]);
    }

    public function getFilteredProducts($filters = [])
    {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND price >= ?";
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $keywords = explode(' ', $filters['search']);
            foreach ($keywords as $keyword) {
                $trimmed = trim($keyword);
                if (!empty($trimmed)) {
                    $searchParam = "%$trimmed%";
                    $sql .= " AND (name LIKE ? OR description LIKE ? OR short_description LIKE ?)";
                    $params[] = $searchParam;
                    $params[] = $searchParam;
                    $params[] = $searchParam;
                }
            }
        }

        if (isset($filters['in_stock']) && $filters['in_stock']) {
            $sql .= " AND stock > 0";
        }

        $orderBy = "created_at DESC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_low':
                    $orderBy = "price ASC";
                    break;
                case 'price_high':
                    $orderBy = "price DESC";
                    break;
                case 'name_asc':
                    $orderBy = "name ASC";
                    break;
                case 'name_desc':
                    $orderBy = "name DESC";
                    break;
                case 'newest':
                    $orderBy = "created_at DESC";
                    break;
                case 'oldest':
                    $orderBy = "created_at ASC";
                    break;
            }
        }
        $sql .= " ORDER BY " . $orderBy;

        return $this->query($sql, $params);
    }

    public function getUserOrders($user_id)
    {
        return $this->query(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC",
            [$user_id]
        );
    }

    public function searchProducts($keyword)
    {
        $keyword = "%$keyword%";
        return $this->query(
            "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?",
            [$keyword, $keyword]
        );
    }

    public function deleteProduct($id)
    {
        return $this->query("DELETE FROM products WHERE id = ?", [$id]);
    }

    public function getAllCategories()
    {
        return $this->query("SELECT * FROM categories");
    }

    public function getCategoryById($id)
    {
        return $this->query("SELECT * FROM categories WHERE id = ?", [$id])->fetch();
    }

    public function getProductCountByCategory($category_id)
    {
        return $this->query("SELECT COUNT(*) as count FROM products WHERE category_id = ?", [$category_id])->fetch()['count'];
    }

    public function createCategory($name, $description)
    {
        return $this->query("INSERT INTO categories (name, description) VALUES (?, ?)", [$name, $description]);
    }

    public function updateCategory($id, $data)
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        return $this->query("UPDATE categories SET " . implode(', ', $fields) . " WHERE id = ?", $values);
    }

    public function deleteCategory($id)
    {
        return $this->query("DELETE FROM categories WHERE id = ?", [$id]);
    }

    public function getAllOrders()
    {
        return $this->query("SELECT * FROM orders");
    }

    public function getOrderById($id)
    {
        return $this->query("SELECT * FROM orders WHERE id = ?", [$id])->fetch();
    }

    public function createOrder($user_id, $total_price, $status = 'pending')
    {
        return $this->query("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, ?)", [$user_id, $total_price, $status]);
    }

    public function createOrderFromCart($user_id, $cart_id)
    {
        try {
            $this->conn->beginTransaction();

            $cartItems = $this->getCartItems($cart_id);
            if (!$cartItems) {
                throw new Exception("cart is empty");
            }

            $total = 0;
            foreach ($cartItems as $item) {
                $product = $this->getProductById($item['product_id']);
                if (!$product || $product['stock'] < $item['quantity']) {
                    throw new Exception("no products available");
                }
                $total += ((float)$product['price']) * $item['quantity'];
            }

            $this->query("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')", [$user_id, $total]);
            $order_id = $this->conn->lastInsertId();

            foreach ($cartItems as $item) {
                $product = $this->getProductById($item['product_id']);
                $itemTotal = ((float)$product['price']) * $item['quantity'];

                $this->query(
                    "INSERT INTO order_items (order_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)",
                    [$order_id, $item['product_id'], $item['quantity'], $itemTotal]
                );

                $this->query("UPDATE products SET stock = stock - ? WHERE id = ?", [$item['quantity'], $item['product_id']]);
            }

            $this->clearCart($cart_id);
            $this->conn->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function updateOrder($id, $status)
    {
        $validStatuses = ['pending', 'approved', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        return $this->query("UPDATE orders SET status=? WHERE id = ?", [$status, $id]);
    }

    public function cancelOrder($order_id)
    {
        $order = $this->getOrderById($order_id);
        if (!$order || $order['status'] === 'delivered') {
            return false;
        }

        $items = $this->getOrderItems($order_id);
        foreach ($items as $item) {
            $this->query("UPDATE products SET stock = stock + ? WHERE id = ?", [$item['quantity'], $item['product_id']]);
        }

        return $this->updateOrder($order_id, 'cancelled');
    }

    public function getOrderItems($order_id)
    {
        return $this->query("SELECT * FROM order_items WHERE order_id = ?", [$order_id])->fetchAll();
    }

    public function getOrderWithItems($order_id)
    {
        return $this->query(
            "SELECT order_items.*, products.name, products.image 
             FROM order_items 
             JOIN products ON order_items.product_id = products.id
             WHERE order_items.order_id = ?",
            [$order_id]
        )->fetchAll();
    }

    public function getAllPayments()
    {
        return $this->query("SELECT * FROM payments");
    }

    public function getPaymentById($id)
    {
        return $this->query("SELECT * FROM payments WHERE id = ?", [$id])->fetch();
    }

    public function createPayment($order_id, $user_id, $amount, $provider, $payment_method, $status = 'pending', $paid_at = null)
    {
        return $this->query(
            "INSERT INTO payments (order_id, user_id, amount, provider, payment_method, status, paid_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$order_id, $user_id, $amount, $provider, $payment_method, $status, $paid_at]
        );
    }

    public function updatePayment($id, $status)
    {
        return $this->query(
            "UPDATE payments SET status = ?, paid_at = NOW() WHERE id = ?",
            [$status, $id]
        );
    }

    public function totalPayments()
    {
        $sql = "SELECT COALESCE(SUM(amount), 0) AS total FROM payments WHERE status = 'paid'";
        return $this->query($sql)->fetch();
    }

    public function createCart($user_id)
    {
        $this->query("INSERT INTO cart (user_id) VALUES (?)", [$user_id]);
        return $this->conn->lastInsertId();
    }

    public function getUserCarts($user_id)
    {
        return $this->query("SELECT * FROM cart WHERE user_id = ? ORDER BY id DESC", [$user_id])->fetchAll();
    }

    public function getCartByUserId($user_id)
    {
        return $this->query("SELECT * FROM cart WHERE user_id = ? ORDER BY id DESC LIMIT 1", [$user_id])->fetch();
    }

    public function getCartItems($cart_id)
    {
        return $this->query("SELECT * FROM cart_items WHERE cart_id = ?", [$cart_id])->fetchAll();
    }

    public function clearCart($cart_id)
    {
        return $this->query("DELETE FROM cart_items WHERE cart_id = ?", [$cart_id]);
    }

    public function getCartById($id)
    {
        return $this->query("SELECT * FROM cart WHERE id = ?", [$id])->fetch();
    }

    public function addToCart($cart_id, $product_id, $quantity)
    {
        $product = $this->getProductById($product_id);
        if (!$product || $product['stock'] < $quantity) {
            return false;
        }

        $existing = $this->query("SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?", [$cart_id, $product_id])->fetch();
        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            if ($newQuantity > $product['stock']) {
                return false;
            }
            return $this->updateCartItem($existing['id'], $newQuantity);
        }

        $total_price = ((float)$product['price']) * $quantity;
        $discount = (float)($product['discount'] ?? 0);
        $final_price = $discount > 0 ? $total_price - ($total_price * $discount / 100) : $total_price;

        return $this->query(
            "INSERT INTO cart_items (cart_id, product_id, quantity, total_price)
             VALUES (?, ?, ?, ?)",
            [$cart_id, $product_id, $quantity, $final_price]
        );
    }

    public function getCartItemsWithDetails($cart_id)
    {
        return $this->query(
            "SELECT cart_items.*, products.name, products.price, products.discount, products.image, products.stock 
             FROM cart_items 
             JOIN products ON cart_items.product_id = products.id
             WHERE cart_items.cart_id = ?",
            [$cart_id]
        )->fetchAll();
    }

    public function updateCartItem($cartItemId, $quantity)
    {
        $cartItem = $this->query("SELECT * FROM cart_items WHERE id = ?", [$cartItemId])->fetch();
        if (!$cartItem) {
            return false;
        }

        if ($quantity == (int)$cartItem['quantity']) {
            return true;
        }

        if ($quantity == 0) {
            return $this->removeFromCart($cartItemId);
        }

        $product = $this->getProductById($cartItem['product_id']);
        if (!$product) {
            return false;
        }

        $total_price = ((float)$product['price']) * $quantity;
        $discount = (float)($product['discount'] ?? 0);
        $final_price = $discount > 0 ? $total_price - ($total_price * $discount / 100) : $total_price;

        return $this->query(
            "UPDATE cart_items SET quantity = ?, total_price = ? WHERE id = ?",
            [$quantity, $final_price, $cartItemId]
        );
    }

    public function getCartTotal($cart_id)
    {
        return $this->query("SELECT SUM(total_price) AS total FROM cart_items WHERE cart_id = ?", [$cart_id])->fetch()['total'];
    }

    public function removeFromCart($cartItemId)
    {
        return $this->query("DELETE FROM cart_items WHERE id = ?", [$cartItemId]);
    }

    public function removeProductFromCart($cart_id, $product_id)
    {
        return $this->query("DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?", [$cart_id, $product_id]);
    }

    public function createWishlist($user_id)
    {
        $stmt = $this->query("SELECT * FROM wishlist WHERE user_id = ?", [$user_id]);
        if ($stmt->rowCount() > 0) {
            return false;
        }
        return $this->query("INSERT INTO wishlist (user_id) VALUES (?)", [$user_id]);
    }

    public function getWishlistByUserId($user_id)
    {
        return $this->query("SELECT * FROM wishlist WHERE user_id = ?", [$user_id])->fetch();
    }

    public function deleteWishlist($wishlist_id)
    {
        return $this->query("DELETE FROM wishlist WHERE id = ?", [$wishlist_id]);
    }

    public function addWishlistItem($wishlist_id, $product_id)
    {
        $stmt = $this->query("SELECT * FROM wishlist_items WHERE wishlist_id = ? AND product_id = ?", [$wishlist_id, $product_id]);
        if ($stmt->rowCount() > 0) {
            return false;
        }
        return $this->query("INSERT INTO wishlist_items (wishlist_id, product_id) VALUES (?, ?)", [$wishlist_id, $product_id]);
    }

    public function getWishlistItems($wishlist_id)
    {
        return $this->query("SELECT * FROM wishlist_items WHERE wishlist_id = ?", [$wishlist_id])->fetchAll();
    }

    public function removeFromWishlist($wishlist_id, $product_id)
    {
        return $this->query("DELETE FROM wishlist_items WHERE wishlist_id = ? AND product_id = ?", [$wishlist_id, $product_id]);
    }

    public function getWishlistItemsWithDetails($wishlist_id)
    {
        return $this->query(
            "SELECT wishlist_items.*, products.name, products.price, products.image, products.stock 
             FROM wishlist_items 
             JOIN products ON wishlist_items.product_id = products.id
             WHERE wishlist_items.wishlist_id = ?",
            [$wishlist_id]
        )->fetchAll();
    }

    public function addReview($user_id, $product_id, $rating, $comment = null)
    {
        return $this->query(
            "INSERT INTO reviews (user_id, product_id, rating, comment)
             VALUES (?, ?, ?, ?)",
            [$user_id, $product_id, $rating, $comment]
        );
    }

    public function getProductReviews($product_id)
    {
        return $this->query("SELECT * FROM reviews WHERE product_id = ?", [$product_id])->fetchAll();
    }

    public function getUserReviews($user_id)
    {
        return $this->query("SELECT * FROM reviews WHERE user_id = ?", [$user_id])->fetchAll();
    }

    public function deleteReview($review_id)
    {
        return $this->query("DELETE FROM reviews WHERE id = ?", [$review_id]);
    }

    public function getProductReviewsWithUsers($product_id)
    {
        return $this->query(
            "SELECT reviews.*, users.first_name, users.last_name, users.photo 
             FROM reviews 
             JOIN users ON reviews.user_id = users.id
             WHERE reviews.product_id = ?
             ORDER BY reviews.created_at DESC",
            [$product_id]
        )->fetchAll();
    }

    public function getProductAverageRating($product_id)
    {
        return $this->query(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
             FROM reviews WHERE product_id = ?",
            [$product_id]
        )->fetch();
    }

    public function countOf($tableName)
    {
        return $this->query("SELECT COUNT(*) AS 'total_$tableName' FROM $tableName")->fetch();
    }

    public function updateRememberToken($userId, $token)
    {
        $stmt = $this->conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        return $stmt->execute([$token, $userId]);
    }

    public function getUserByToken($token)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
}
