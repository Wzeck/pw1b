<!DOCTYPE html>
<html>
<head>
    <title>User Details</title>
</head>
<body>
    <h1>User Details</h1>
    
    <?php if ($user): ?>
        <p><strong>ID:</strong> <?= $user['id'] ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <?php else: ?>
        <p>User not found</p>
    <?php endif; ?>
    
    <a href="/project/user/index">Back to List</a>
</body>
</html>