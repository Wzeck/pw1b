<?php

// Classe responsável por "controlar" o fluxo entre o usuário, o modelo e a visualização
class UserController {

    // Variável que vai guardar o modelo (UserModel)
    private $model;
    
    // Construtor: é chamado automaticamente quando criamos um novo UserController
    public function __construct() {

        // Inclui o arquivo do modelo (onde ficam as funções que acessam o banco)
        require_once __DIR__ . '/../models/User.php';
        
        // Cria um novo "modelo" para ser usado neste controller
        $this->model = new UserModel();
    }
    
    // Função que mostra todos os usuários cadastrados
    public function index() {

        // Pede para o modelo buscar todos os usuários no banco
        $users = $this->model->getAll();
        
        // Envia os dados ($users) para a página que vai exibir (a view)
        require_once __DIR__ . '/../views/users/index.php';
    }
    
    // Mostra os dados de UM usuário específico (pelo ID)
    public function show($id = null) {

        // Se não tiver um ID, volta para a página principal
        if (!$id) {
            header('Location: /project/user/index');
            return;
        }
        
        // Pega as informações do usuário com aquele ID
        $user = $this->model->getById($id);
        
        // Se encontrou o usuário, mostra a página dele
        if ($user) {
            require_once __DIR__ . '/../views/users/show.php';
        } else {

            // Se não achou, mostra erro 404
            http_response_code(404);
            echo "Usuário não encontrado.";
        }
    }
    
    // Cria um novo usuário no banco
    public function create() {

        // Se o formulário foi enviado (ou seja, veio via método POST)
        if ($_POST) {

            // Pede para o modelo inserir o novo usuário no banco
            $this->model->create([
                'name' => $_POST['name'],   // Nome digitado
                'email' => $_POST['email'] // Email digitado
            ]);

            // Depois de criar, redireciona para a lista de usuários
            header('Location: /project/user/index');
        } else {

            // Se não enviou nada ainda, mostra o formulário de criação
            echo '
            <form method="POST" style="display:flex; flex-direction:column; width:300px; gap:8px;">
                <input type="text" name="name" placeholder="Nome" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <button type="submit">Criar Usuário</button>
            </form>';
        }
    }

    // Apaga um usuário do banco
    public function delete($id = null) {
        
        // Se não passar ID, mostra uma mensagem de erro
        if (!$id) {
            echo "ID não fornecido.";
            return;
        }

        // Busca o usuário no banco para confirmar que ele existe
        $user = $this->model->getById($id);

        // Se não existir, mostra erro
        if (!$user) {
            echo "Usuário não encontrado.";
            return;
        }

        // Se existir, deleta e volta pra lista
        $this->model->delete($id);
        header('Location: /project/user/index');
    }
}
?>
