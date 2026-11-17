<?php
require_once 'config.php';
verificaAdmin();

$msg = '';
$tipo_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['salvar'])) {
        $usuario = trim($_POST['usuario']);
        $senha = md5(trim($_POST['senha']));
        $perfil = $_POST['perfil'];
        
        try {
            $supabase->insert('usuarios', ['usuario' => $usuario, 'senha' => $senha, 'perfil' => $perfil]);
            $msg = "Usuário criado com sucesso!";
            $tipo_msg = 'success';
        } catch (Exception $e) {
            $msg = "Erro: " . $e->getMessage();
            $tipo_msg = 'error';
        }
    }
    
    if (isset($_POST['excluir'])) {
        $id = (int)$_POST['excluir'];
        $supabase->delete('usuarios', ['id' => 'eq.' . $id]);
        $msg = "Usuário excluído!";
        $tipo_msg = 'success';
    }
}

$usuarios = $supabase->select('usuarios', 'id, usuario, perfil', [], 'usuario.asc');

topo("Cadastro de Usuários");
?>
<a href="index.php" class="btn">← Voltar</a>

<?php if ($msg): ?>
    <div class="alert alert-<?= $tipo_msg ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2 style="margin-top:20px;">👤 Novo Usuário</h2>
<form method="POST">
    <div class="form-group">
        <label>Usuário:</label>
        <input type="text" name="usuario" required>
    </div>
    <div class="form-group">
        <label>Senha:</label>
        <input type="text" name="senha" required>
    </div>
    <div class="form-group">
        <label>Perfil:</label>
        <select name="perfil" required>
            <option value="aluno">Usuário Comum (Aluno)</option>
            <option value="admin">Administrativo</option>
        </select>
    </div>
    <button type="submit" name="salvar" class="btn btn-success">💾 Salvar</button>
</form>

<h2 style="margin-top:40px;">📋 Usuários Cadastrados</h2>
<table>
    <thead><tr><th>ID</th><th>Usuário</th><th>Perfil</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['usuario']) ?></td>
            <td><?= $u['perfil'] === 'admin' ? 'Administrativo' : 'Usuário Comum' ?></td>
            <td>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir?');">
                    <input type="hidden" name="excluir" value="<?= $u['id'] ?>">
                    <button type="submit" class="btn btn-sair btn-sm">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php rodape(); ?>