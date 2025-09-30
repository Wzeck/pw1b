<?php
session_start(); // Inicia a sessão para identificar o usuário logado

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    die("Acesso negado. Faça login primeiro.");
}

// Conexão com o banco de dados MySQL
$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Pega os dados enviados do formulário
    $id_sala = $_POST["id_sala"];
    $data_reserva = $_POST["data_reserva"];
    $hora_inicio = $_POST["hora_inicio"];
    $hora_fim = $_POST["hora_fim"];
    $id_usuario = $_SESSION["usuario_id"];

    // 🔒 Validação 1: horário no formato correto (HH:MM)
    if (!preg_match("/^\d{2}:\d{2}$/", $hora_inicio) || !preg_match("/^\d{2}:\d{2}$/", $hora_fim)) {
        die("Horário inválido.");
    }

    // Converte para timestamps para facilitar comparação
    $inicio = strtotime($hora_inicio);
    $fim = strtotime($hora_fim);

    // 🔒 Validação 2: intervalo permitido (08:00–22:00)
    $limite_inicio = strtotime("08:00");
    $limite_fim = strtotime("22:00");
    if ($inicio < $limite_inicio || $fim > $limite_fim || $fim <= $inicio) {
        die("Erro: Reservas só podem ser feitas das 08:00 às 22:00, com horário final maior que o inicial.");
    }

    // 🔒 Validação 3: deve ser múltiplo de 30 minutos
    if (date("i", $inicio) % 30 != 0 || date("i", $fim) % 30 != 0) {
        die("Erro: Reservas só podem ser feitas em intervalos de 30 minutos (ex: 08:00, 08:30, 09:00...).");
    }

    // Verifica se já existe reserva que conflita com a escolhida
    $verifica = $conn->prepare("SELECT * FROM reservas 
        WHERE id_sala = ? AND data_reserva = ? 
        AND (
            (hora_inicio < ? AND hora_fim > ?) OR  -- início antes e término depois
            (hora_inicio < ? AND hora_fim > ?) OR  -- término depois do início
            (hora_inicio >= ? AND hora_fim <= ?)   -- dentro do intervalo já reservado
        )");

    // Substitui os "?" pelos valores
    $verifica->bind_param("isssssss", 
        $id_sala, $data_reserva, 
        $hora_fim, $hora_fim, 
        $hora_inicio, $hora_inicio, 
        $hora_inicio, $hora_fim
    );
    $verifica->execute();
    $resultado = $verifica->get_result();

    // Se encontrou conflito -> não deixa reservar
    if ($resultado->num_rows > 0) {
        echo "Erro: Já existe uma reserva para essa sala nesse horário.";
    } else {
        // Se não existe conflito -> insere a reserva no banco
        $stmt = $conn->prepare("INSERT INTO reservas (id_sala, id_usuario, data_reserva, hora_inicio, hora_fim) 
                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $id_sala, $id_usuario, $data_reserva, $hora_inicio, $hora_fim);

        if ($stmt->execute()) {
            echo "Reserva feita com sucesso!";
        } else {
            echo "Erro ao agendar: " . $stmt->error;
        }

        $stmt->close();
    }

    $verifica->close();
} else {
    echo "Acesso inválido.";
}

$conn->close(); // Fecha a conexão
?>
