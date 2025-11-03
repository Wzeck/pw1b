<?php
session_start();
header('Content-Type: application/json'); // Retorno JSON

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["status" => "erro", "mensagem" => "Acesso negado. Faça login primeiro."]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "agenda_etec");
if ($conn->connect_error) {
    echo json_encode(["status" => "erro", "mensagem" => "Falha na conexão: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_sala = $_POST["id_sala"];
    $data_reserva = $_POST["data_reserva"];
    $hora_inicio = $_POST["hora_inicio"];
    $hora_fim = $_POST["hora_fim"];
    $id_usuario = $_SESSION["usuario_id"];

    if (!preg_match("/^\d{2}:\d{2}$/", $hora_inicio) || !preg_match("/^\d{2}:\d{2}$/", $hora_fim)) {
        echo json_encode(["status" => "erro", "mensagem" => "Horário inválido."]);
        exit;
    }

    $inicio = strtotime($hora_inicio);
    $fim = strtotime($hora_fim);

    $limite_inicio = strtotime("08:00");
    $limite_fim = strtotime("22:00");

    if ($inicio < $limite_inicio || $fim > $limite_fim || $fim <= $inicio) {
        echo json_encode(["status" => "erro", "mensagem" => "Reservas só podem ser feitas das 08:00 às 22:00."]);
        exit;
    }

    if (date("i", $inicio) % 30 != 0 || date("i", $fim) % 30 != 0) {
        echo json_encode(["status" => "erro", "mensagem" => "Reservas devem ser em intervalos de 30 minutos."]);
        exit;
    }

    // Verifica conflitos de horário
    $verifica = $conn->prepare("
        SELECT * FROM reservas 
        WHERE id_sala = ? 
          AND data_reserva = ? 
          AND (hora_inicio < ? AND hora_fim > ?)
    ");
    $verifica->bind_param("isss", $id_sala, $data_reserva, $hora_fim, $hora_inicio);
    $verifica->execute();
    $resultado = $verifica->get_result();

    if ($resultado->num_rows > 0) {
        echo json_encode(["status" => "erro", "mensagem" => "Já existe uma reserva nesse horário."]);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO reservas (id_sala, id_usuario, data_reserva, hora_inicio, hora_fim)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisss", $id_sala, $id_usuario, $data_reserva, $hora_inicio, $hora_fim);

        if ($stmt->execute()) {
            echo json_encode(["status" => "ok", "mensagem" => "Reserva feita com sucesso!"]);
        } else {
            echo json_encode(["status" => "erro", "mensagem" => "Erro ao salvar reserva."]);
        }
        $stmt->close();
    }
    $verifica->close();
} else {
    echo json_encode(["status" => "erro", "mensagem" => "Acesso inválido."]);
}

$conn->close();
?>
