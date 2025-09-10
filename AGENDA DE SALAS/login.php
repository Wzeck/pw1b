<?php
            // 1. configuração de conexão
           $servername = "localhost:3306";
           $username = "root"; // padrão no XAMPP
           $password = ""; // padrão no XAMPP (sem a senha)
           $database = "CADASTRO_DB";
    
           // 2. criar conexão
           $conn = new mysqli($servername, $username, $password, $database);
    
            
            // 3. Chegar conexão
            if($conn->connect_error) {
                die ("falha na conexão: " . $conn->connect_error);
            }else {
                echo "conectou com sucesso!";
            }
    
            // Receber dados do formulário
            $email = $_POST["email"];
            $password = $_POST["password"];
           
            $stmt = $conn->prepare("INSERT INTO usuarios (email, 'password') VALUES (?,?)");
            $stmt-> bind_param("ss", $email, $password);
    
            if ($stmt->execute()) {
                echo "CRIOU O USUARIO COM SUCESSO $nome $email";
            }else {
                echo "DEU ESSE ERRO". $stmt->error; 
            }
    
            $stmt->close();
            $conn->close();
?>

