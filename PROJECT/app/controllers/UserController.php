<?php
class UserController {
    private $model;
    
    public function __construct() {
        require_once 'app/models/User.php';
        $this->model = new User();
    }
    
    
    public function index() {
        
        $users = $this->model->getAll();
        
       
        require_once 'app/views/users/index.php';
    }
    
    public function show($id = null) {
        if (!$id) {
            header('Location: /project/user/index');
            return;
        }
        
        $user = $this->model->getById($id);
        
        if ($user) {
            require_once 'app/views/users/show.php';
        } else {
            http_response_code(404);
            echo "User not found";
        }
    }
    
    public function create() {
        if ($_POST) {
            $this->model->create([
                'name' => $_POST['name'],
                'email' => $_POST['email']
            ]);
            header('Location: /project/user/index');
        } else {
            
            echo '
            <form method="POST">
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit">Create User</button>
            </form>';
        }
    }
    
    //NOVA ACTION PARA EXCLUSÃO 
    public function delete($id = null) {
        if (!$id) {
            // Se não houver ID, apenas volta para a lista
            header('Location: /project/user/index');
            return;
        }
        
        // 1. Chama o Model para executar a exclusão
        $this->model->deleteById($id);
        
        // 2. Redireciona de volta para a lista de usuários
        header('Location: /project/user/index');
    }
    
}
?>