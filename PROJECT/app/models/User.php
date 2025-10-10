<?php
class User {
    private $db;
    
    public function __construct() {
        require_once 'config/database.php';
        $this->db = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, 
            DB_USER, 
            DB_PASS
        );
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email) VALUES (?, ?)"
        );
        return $stmt->execute([$data['name'], $data['email']]);
    }
    
    //  NOVO MÉTODO PARA EXCLUSÃO
    public function deleteById($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
}
?>