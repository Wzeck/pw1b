<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
</head>
<body>
    
</body>
</html>
<html>
<head>
    <title>Lista de usuários</title>
</head>
<body>
    <h1>Usuarios</h1>
    <a href="/project/user/create">Criar novo usuário</a>
    
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td>
                <a href="/project/user/show/<?= $user['id'] ?>">View</a>
                | <a href="/project/user/delete/<?= $user['id'] ?>" 
                     onclick="return confirm('Tem certeza de que deseja excluir este usuário?');">Delete</a>
                </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>