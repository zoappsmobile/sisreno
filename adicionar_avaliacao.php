<?php
require_once 'config.php';
verificaAdmin();

$turma_id = (int)($_GET['turma_id'] ?? 0);
if (!$turma_id) { header("Location: gerenciar_notas.php"); exit; }

$msg = '';
$tipo_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bimestre = (int)$_POST['bimestre'];
    $nome = trim($_POST['nome']);
    $nota_maxima = (float)$_POST['nota_maxima'];
    
    try {
        $supabase->insert('avaliacoes', [
            'turma_id' => $turma_id,
            'bimestre' => $bimestre,
            'nome' => $nome,
            'nota_maxima' => $nota_maxima
        ]);
        $msg = "Avaliação adicionada com sucesso!";
        $tipo_msg = 'success';
    } catch (Exception $e) {
        $msg = "Erro: " . $e->getMessage();
        $tipo_msg = 'error';
    }
}

$avaliacoes = $supabase->select('avaliacoes', 'id, bimestre, nome, nota_maxima', ['turma_id' => 'eq.' . $turma_id], 'bimestre.asc,id.asc');

topo("Adicionar Avaliação");
?>
<a href="gerenciar_notas.php?turma_id=<?= $turma_id ?>" class="btn">← Voltar</a>

<?php if ($msg): ?>
    <div class="alert alert-<?= $tipo_msg ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2 style="margin-top:20px;">➕ Adicionar Avaliação / Atividade</h2>
<form method="POST">
    <div class="form-group">
        <label>Avaliação Bimestral:</label>
        <select name="bimestre" required>
            <option value="">-- Selecione --</option>
            <option value="1">1ª Avaliação</option>
            <option value="2">2ª Avaliação</option>
            <option value="3">3ª Avaliação</option>
            <option value="4">4ª Avaliação</option>
        </select>
    </div>
    <div class="form-group">
        <label>Nome da Atividade Avaliativa:</label>
        <input type="text" name="nome" required placeholder="Ex: Prova, Trabalho, Seminário...">
    </div>
    <div class="form-group">
        <label>Valor Máximo da Nota:</label>
        <input type="number" name="nota_maxima" step="0.1" min="0.1" max="10" value="10.0" required>
    </div>
    <button type="submit" class="btn btn-success">💾 Salvar Avaliação</button>
</form>

<h2 style="margin-top:40px;">📋 Avaliações da Turma</h2>
<?php if (!empty($avaliacoes)): ?>
<table>
    <thead><tr><th>ID</th><th>Bimestre</th><th>Nome</th><th>Nota Máxima</th></tr></thead>
    <tbody>
    <?php foreach ($avaliacoes as $a): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><?= $a['bimestre'] ?>º Bimestre</td>
            <td><?= htmlspecialchars($a['nome']) ?></td>
            <td><?= number_format($a['nota_maxima'],1,',','.') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <p style="margin-top:15px; color:#64748b;">Nenhuma avaliação cadastrada.</p>
<?php endif; ?>
<?php rodape(); ?>