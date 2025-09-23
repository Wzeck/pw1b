<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
die("Acesso negado. Faça login primeiro.");
}

$conn = new mysqli("localhost:3306", "root", "", "agenda_etec");
if ($conn->connect_error) {
die("Falha na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_sala = $_POST["id_sala"];
    $data_reserva = $_POST["data_reserva"];
    $hora_inicio = $_POST["hora_inicio"];
    $hora_fim = $_POST["hora_fim"];
    $id_usuario = $_SESSION["usuario_id"];

    // 🔒 Validação 1: formato de hora válido
    if (!preg_match("/^\d{2}:\d{2}$/", $hora_inicio) || !preg_match("/^\d{2}:\d{2}$/", $hora_fim)) {
        die("Horário inválido.");
    }

    $inicio = strtotime($hora_inicio);
    $fim = strtotime($hora_fim);

    // 🔒 Validação 2: dentro do intervalo permitido (08:00–22:00)
    $limite_inicio = strtotime("08:00");
    $limite_fim = strtotime("22:00");
    if ($inicio < $limite_inicio || $fim > $limite_fim || $fim <= $inicio) {
        die("Erro: Reservas só podem ser feitas das 08:00 às 22:00, com horário final maior que o inicial.");
    }

    // 🔒 Validação 3: múltiplos de 30 minutos
    if (date("i", $inicio) % 30 != 0 || date("i", $fim) % 30 != 0) {
        die("Erro: Reservas só podem ser feitas em intervalos de 30 minutos (ex: 08:00, 08:30, 09:00...).");
    }

    // Verifica se já existe conflito de horário
    $verifica = $conn->prepare("SELECT * FROM reservas 
        WHERE id_sala = ? AND data_reserva = ? 
        AND (
            (hora_inicio < ? AND hora_fim > ?) OR
            (hora_inicio < ? AND hora_fim > ?) OR
            (hora_inicio >= ? AND hora_fim <= ?)
        )");

    $verifica->bind_param("isssssss", 
        $id_sala, $data_reserva, 
        $hora_fim, $hora_fim, 
        $hora_inicio, $hora_inicio, 
        $hora_inicio, $hora_fim
    );
    $verifica->execute();
    $resultado = $verifica->get_result();

    if ($resultado->num_rows > 0) {
        echo "Erro: Já existe uma reserva para essa sala nesse horário.";
    } else {
    // Insere nova reserva
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

$conn->close();
?>
