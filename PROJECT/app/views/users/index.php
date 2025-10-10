<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
</head>
<body>
    <h1>Users</h1>

    <!-- Link que leva para o formulário de criar novo usuário -->
    <a href="/project/user/create">Create New User</a>
    
    <!-- Tabela que mostra todos os usuários -->
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
            <th>Delete</th>
        </tr>

        <!-- Aqui ele vai repetir uma linha pra cada usuário -->
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>

            <!-- Botão pra ver detalhes do usuário -->
            <td>
                <a href="/project/user/show/<?= $user['id'] ?>">View</a>
            </td>

            <!-- Botão pra deletar o usuário -->
            <td>
                <a href="/project/user/delete/<?= $user['id'] ?>">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
