<?php
require_once 'config.php';
verificaAdmin();

$msg = '';
$tipo_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['salvar_turma'])) {
        $nome_turma = trim($_POST['nome_turma']);
        $qtd_alunos = (int)$_POST['qtd_alunos'];
        $criar_usuarios = isset($_POST['criar_usuarios']);
        
        try {
            $turma_result = $supabase->insert('turmas', ['nome' => $nome_turma]);
            $turma_id = $turma_result[0]['id'];
            
            for ($i = 0; $i < $qtd_alunos; $i++) {
                $nome_aluno = trim($_POST["nome_aluno_$i"] ?? '');
                $matricula = trim($_POST["matricula_$i"] ?? '');
                if ($nome_aluno === '' || $matricula === '') continue;
                
                $aluno_result = $supabase->insert('alunos', [
                    'turma_id' => $turma_id,
                    'nome' => $nome_aluno,
                    'matricula' => $matricula
                ]);
                
                if ($criar_usuarios) {
                    $aluno_id = $aluno_result[0]['id'];
                    $supabase->insert('usuarios', [
                        'usuario' => $matricula,
                        'senha' => md5($matricula),
                        'perfil' => 'aluno',
                        'aluno_id' => $aluno_id
                    ]);
                }
            }
            $msg = "Turma e alunos cadastrados com sucesso!";
            $tipo_msg = 'success';
        } catch (Exception $e) {
            $msg = "Erro: " . $e->getMessage();
            $tipo_msg = 'error';
        }
    }
    
    if (isset($_POST['excluir'])) {
        $id = (int)$_POST['excluir'];
        $supabase->delete('turmas', ['id' => 'eq.' . $id]);
        $msg = "Turma excluída!";
        $tipo_msg = 'success';
    }
}

$turmas = $supabase->select('turmas', 'id, nome', [], 'nome.asc');

topo("Cadastro de Turmas");
?>
<a href="index.php" class="btn">← Voltar</a>

<?php if ($msg): ?>
    <div class="alert alert-<?= $tipo_msg ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2 style="margin-top:20px;">🏫 Nova Turma</h2>
<form method="POST" id="formTurma">
    <div class="form-group">
        <label>Nome da Turma:</label>
        <input type="text" name="nome_turma" required>
    </div>
    <div class="form-group">
        <label>Quantidade de Alunos:</label>
        <input type="number" name="qtd_alunos" id="qtd_alunos" min="1" max="100" required>
        <button type="button" class="btn btn-warning" onclick="gerarCampos()" style="margin-top:10px;">Gerar Campos</button>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="criar_usuarios" checked> Criar usuários automaticamente (usuário e senha = matrícula)</label>
    </div>
    <div id="alunos_container"></div>
    <button type="submit" name="salvar_turma" class="btn btn-success">💾 Salvar Turma</button>
</form>

<script>
function gerarCampos() {
    const qtd = parseInt(document.getElementById('qtd_alunos').value);
    if (isNaN(qtd) || qtd < 1) { alert("Informe uma quantidade válida!"); return; }
    const container = document.getElementById('alunos_container');
    let html = '<h3 style="margin-top:20px;">Dados dos Alunos:</h3><table><thead><tr><th>#</th><th>Nome</th><th>Matrícula</th></tr></thead><tbody>';
    for (let i = 0; i < qtd; i++) {
        html += `<tr><td>${i+1}</td><td><input type="text" name="nome_aluno_${i}" required></td><td><input type="text" name="matricula_${i}" required></td></tr>`;
    }
    html += '</tbody></table>';
    container.innerHTML = html;
}
</script>

<h2 style="margin-top:40px;">📋 Turmas Cadastradas</h2>
<?php if (!empty($turmas)): ?>
<table>
    <thead><tr><th>ID</th><th>Nome</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($turmas as $t): ?>
        <tr>
            <td><?= $t['id'] ?></td>
            <td><?= htmlspecialchars($t['nome']) ?></td>
            <td>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Excluir esta turma?');">
                    <input type="hidden" name="excluir" value="<?= $t['id'] ?>">
                    <button type="submit" class="btn btn-sair btn-sm">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <p style="margin-top:15px; color:#64748b;">Nenhuma turma cadastrada.</p>
<?php endif; ?>
<?php rodape(); ?>