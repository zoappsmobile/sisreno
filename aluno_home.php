<?php
require_once 'config.php';
verificaLogin();

if ($_SESSION['perfil'] === 'admin') {
    header("Location: index.php");
    exit;
}

$aluno_id = $_SESSION['aluno_id'];
$aluno = $supabase->fetchOne('alunos', '*, turmas(nome)', ['id' => 'eq.' . $aluno_id]);

// Busca notas com dados da avaliação aninhados
$notas_raw = $supabase->select('notas', '*, avaliacoes(bimestre, nome, nota_maxima)', ['aluno_id' => 'eq.' . $aluno_id], 'avaliacoes.bimestre.asc,avaliacoes.id.asc');

topo("Minhas Notas");
?>
<h2>Olá, <?= htmlspecialchars($aluno['nome']) ?>!</h2>

<div style="background:#f1f5f9; padding:20px; border-radius:8px; margin:20px 0;">
    <p><b>Matrícula:</b> <?= htmlspecialchars($aluno['matricula']) ?></p>
    <p><b>Turma:</b> <?= htmlspecialchars($aluno['turmas']['nome']) ?></p>
</div>

<h3>📊 Minhas Notas</h3>
<?php if (!empty($notas_raw)): ?>
<table>
    <thead>
        <tr>
            <th>Bimestre</th>
            <th>Avaliação</th>
            <th>Nota Máxima</th>
            <th>Nota Obtida</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($notas_raw as $n): 
        $av = $n['avaliacoes'];
    ?>
        <tr>
            <td><?= $av['bimestre'] ?>º Bimestre</td>
            <td><?= htmlspecialchars($av['nome']) ?></td>
            <td><?= number_format($av['nota_maxima'], 1, ',', '.') ?></td>
            <td><b><?= number_format($n['nota'], 1, ',', '.') ?></b></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <p style="margin-top:15px; color:#64748b;">Nenhuma avaliação cadastrada para sua turma ainda.</p>
<?php endif; ?>

<?php rodape(); ?>