<?php
class Database
{
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $db   = 'ecommerce_db';
    private $user = 'root';
    private $pass = '';
    private $charset = 'utf8mb4';

    private function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
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

    // users
    public function getAllUsers()
    {
        return $this->query("SELECT * FROM users");
    }

    public function getUserById($id)
    {
        return $this->query("SELECT * FROM users WHERE id = ?", [$id])->fetch();
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

    // products
    public function getAllProducts()
    {
        return $this->query("SELECT * FROM products");
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


    public function deleteProduct($id)
    {
        return $this->query("DELETE FROM products WHERE id = ?", [$id]);
    }

    // categories 
    public function getAllCategories()
    {
        return $this->query("SELECT * FROM categories");
    }

    public function getCategoryById($id)
    {
        return $this->query("SELECT * FROM categories WHERE id = ?", [$id])->fetch();
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

    // orders
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

    public function updateOrder($id, $status)
    {
        return $this->query("UPDATE orders SET status=? WHERE id = ?", [$status, $id]);
    }

    //payments
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

    // // cart
    // public function createCart($user_id)
    // {
    //     $stmt = $this->query("SELECT * FROM cart WHERE user_id = ?", [$user_id]);
    //     if ($stmt->rowCount() > 0)
    //         return false;
    //     else {
    //         $this->query("INSERT INTO cart VALUES (?)", $user_id);
    //     }
    // }

    // public function getCartByUserId($user_id)
    // {
    //     return $this->query("SELECT * FROM cart WHERE user_id = ?", [$user_id])->fetch();
    // }

    // public function addToCart($cart_id, $product_id, $quantity)
    // {
    //     $stmt = $this->query("SELECT * FROM products WHERE id = ?", [$product_id]);
    //     $product = $stmt->fetch();
    //     if (!$product) {
    //         return false;
    //     }

    //     $total_price = ((float)$product['price']) * $quantity;
    //     return $this->query("INSERT INTO cart_items (cart_id, product_id,quantity,total_price) VALUES (?,?,?,?)", [$cart_id, $product_id, $quantity, $total_price]);
    // }

    // public function UpdateCartItem($cartItemId, $quantity)
    // {
    //     $cartItem = $this->query("SELECT * FROM cart_items WHERE id = ?", [$cartItemId]);

    //     if ($quantity == (int)$cartItem['quantity']) {
    //         return;
    //     }

    //     if ($quantity == 0) {
    //         $this->removeFromCart($cartItemId);
    //     }

    //     $stmt = $this->query("SELECT * FROM products WHERE id = ?", [$product_id]);
    //     $product = $stmt->fetch();
    //     if (!$product) {
    //         return false;
    //     }

    //     $total_price = ((float)$product['price']) * $quantity;
    //     return $this->query("INSERT INTO cart_items (cart_id, product_id,quantity,total_price) VALUES (?,?,?,?)", [$cart_id, $product_id, $quantity, $total_price]);
    // }

    // public function removeFromCart($cartItemId)
    // {
    //     return $this->query("DELETE FROM cart_items WHERE id = ?", [$cartItemId]);
    // }

}
