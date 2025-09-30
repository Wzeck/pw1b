<?php
session_start();
$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) die("Falha na conexão: ".$conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    $stmt = $conn->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($password, $usuario["senha"])) {
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nome"] = $usuario["nome"];
            $_SESSION["usuario_tipo"] = $usuario["tipo"];
            header("Location: agendar.html"); // Redireciona para agendamento
            exit;
        } else {
            echo "Senha incorreta.";
        }
    } else {
        echo "E-mail não encontrado.";
    }
    $stmt->close();
} else {
    echo "Acesso inválido.";
}
$conn->close();
?>
