<?php

// Classe que fala diretamente com o banco de dados (MySQL)
class UserModel {

    private $db; // Guarda a conexão com o banco

    public function __construct() {

        // Importa as configurações do banco (host, nome, usuário e senha)
        require_once 'config/database.php';
        try {

            // Cria uma nova conexão com o banco usando PDO
            $this->db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, 
                DB_USER, 
                DB_PASS
            );

            // Configura para mostrar erros caso algo dê errado
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {

            // Se não conseguir conectar, mostra o erro
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }

    // Pega todos os usuários da tabela
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM users"); // Executa o SQL
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna tudo como array
    }

    // Pega um único usuário pelo ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?"); // Prepara o SQL
        $stmt->execute([$id]); // Substitui o "?" pelo ID
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna o resultado
    }

    // Cria (insere) um novo usuário
    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email) VALUES (?, ?)"
        );

        // Executa a query com os dados do formulário
        return $stmt->execute([$data['name'], $data['email']]);
    }

    // Deleta um usuário
    public function delete($id) {
        
        // Primeiro verifica se o usuário existe
        $user = $this->getById($id);
        if (!$user) {
            return false; // Se não existir, não faz nada
        }

        // Se existir, executa o comando de exclusão
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
