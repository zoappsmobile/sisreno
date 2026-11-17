<?php
require_once 'config.php';
verificaAdmin();

$turma_id = (int)($_GET['turma_id'] ?? 0);
if (!$turma_id) { header("Location: gerenciar_notas.php"); exit; }

$msg = '';
$tipo_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST['nota'] ?? [] as $key => $valor) {
            list($aluno_id, $avaliacao_id) = explode('_', $key);
            $nota = (float)$valor;
            
            // Upsert: insere ou atualiza se já existir (conflito em aluno_id + avaliacao_id)
            $supabase->upsert('notas', [
                'aluno_id' => $aluno_id,
                'avaliacao_id' => $avaliacao_id,
                'nota' => $nota
            ], 'aluno_id,avaliacao_id');
        }
        $msg = "Notas salvas com sucesso!";
        $tipo_msg = 'success';
    } catch (Exception $e) {
        $msg = "Erro: " . $e->getMessage();
        $tipo_msg = 'error';
    }
}

$alunos = $supabase->select('alunos', 'id, nome, matricula', ['turma_id' => 'eq.' . $turma_id], 'nome.asc');
$avaliacoes = $supabase->select('avaliacoes', 'id, bimestre, nome, nota_maxima', ['turma_id' => 'eq.' . $turma_id], 'bimestre.asc,id.asc');

topo("Lançar Notas");
?>
<a href="gerenciar_notas.php?turma_id=<?= $turma_id ?>" class="btn">← Voltar</a>

<?php if ($msg): ?>
    <div class="alert alert-<?= $tipo_msg ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2 style="margin-top:20px;">✏️ Lançar Notas</h2>

<?php if (empty($alunos) || empty($avaliacoes)): ?>
    <p style="margin-top:15px; color:#64748b;">Cadastre alunos e avaliações para esta turma antes de lançar notas.</p>
<?php else: ?>
<form method="POST">
    <table>
        <thead>
            <tr>
                <th>Aluno</th>
                <?php foreach ($avaliacoes as $av): ?>
                    <th title="Máx: <?= $av['nota_maxima'] ?>">
                        <?= $av['bimestre'] ?>º - <?= htmlspecialchars($av['nome']) ?><br>
                        <small>(max: <?= $av['nota_maxima'] ?>)</small>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($alunos as $al): ?>
            <tr>
                <td><?= htmlspecialchars($al['nome']) ?><br><small><?= htmlspecialchars($al['matricula']) ?></small></td>
                <?php foreach ($avaliacoes as $av): 
                    // Busca nota atual deste aluno nesta avaliação
                    $nota_atual = $supabase->fetchOne('notas', 'nota', [
                        'aluno_id' => 'eq.' . $al['id'],
                        'avaliacao_id' => 'eq.' . $av['id']
                    ]);
                    $valor = $nota_atual ? $nota_atual['nota'] : 0;
                ?>
                    <td>
                        <input type="number" name="nota[<?= $al['id'] ?>_<?= $av['id'] ?>]" 
                               value="<?= $valor ?>" step="0.1" min="0" max="<?= $av['nota_maxima'] ?>" required>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit" class="btn btn-success" style="margin-top:15px;">💾 Salvar Todas as Notas</button>
</form>
<?php endif; ?>
<?php rodape(); ?>