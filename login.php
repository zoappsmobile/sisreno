<?php
require_once 'config.php';

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    
    // Busca usuário e faz join com a tabela alunos para pegar o nome
    $users = $supabase->select('usuarios', '*, alunos(nome)', [
        'usuario' => 'eq.' . $usuario,
        'senha' => 'eq.' . md5($senha)
    ]);
    
    if (!empty($users)) {
        $row = $users[0];
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['usuario_nome'] = $row['perfil'] === 'admin' ? $row['usuario'] : ($row['alunos']['nome'] ?? 'Aluno');
        $_SESSION['perfil'] = $row['perfil'];
        $_SESSION['aluno_id'] = $row['aluno_id'];
        header("Location: " . ($row['perfil'] === 'admin' ? 'index.php' : 'aluno_home.php'));
        exit;
    } else {
        $erro = "Usuário ou senha inválidos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - ReNo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-box">
        <img src="logo.php" alt="ReNo" style="height:70px;">
        <h2>Registro de Notas</h2>
        <?php if ($erro): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Usuário:</label>
                <input type="text" name="usuario" required autofocus>
            </div>
            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit" class="btn" style="width:100%;">Entrar</button>
        </form>
        
    </div>
</body>
</html>