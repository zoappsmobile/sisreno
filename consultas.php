<?php
require_once 'config.php';
verificaAdmin();

$turmas = $supabase->select('turmas', 'id, nome', [], 'nome.asc');
$turma_sel = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;
$aluno_sel = isset($_GET['aluno_id']) ? (int)$_GET['aluno_id'] : 0;

$aluno_dados = null;
$notas_raw = [];

if ($turma_sel && $aluno_sel) {
    $aluno_dados = $supabase->fetchOne('alunos', '*, turmas(nome)', ['id' => 'eq.' . $aluno_sel]);
    $notas_raw = $supabase->select('notas', '*, avaliacoes(bimestre, nome, nota_maxima)', ['aluno_id' => 'eq.' . $aluno_sel], 'avaliacoes.bimestre.asc,avaliacoes.id.asc');
}

$alunos = [];
if ($turma_sel) {
    $alunos = $supabase->select('alunos', 'id, nome, matricula', ['turma_id' => 'eq.' . $turma_sel], 'nome.asc');
}

topo("Consultas");
?>
<a href="index.php" class="btn">← Voltar</a>

<h2 style="margin-top:20px;">🔍 Consultar Dados</h2>
<form method="GET">
    <div class="form-group">
        <label>Turma:</label>
        <select name="turma_id" onchange="this.form.submit()">
            <option value="">-- Selecione --</option>
            <?php foreach ($turmas as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $t['id']==$turma_sel?'selected':'' ?>><?= htmlspecialchars($t['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($turma_sel && !empty($alunos)): ?>
    <div class="form-group">
        <label>Aluno:</label>
        <select name="aluno_id" onchange="this.form.submit()">
            <option value="">-- Selecione --</option>
            <?php foreach ($alunos as $a): ?>
                <option value="<?= $a['id'] ?>" <?= $a['id']==$aluno_sel?'selected':'' ?>><?= htmlspecialchars($a['nome']) ?> (<?= htmlspecialchars($a['matricula']) ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
</form>

<?php if ($aluno_dados): ?>
<div class="acoes">
    <button onclick="window.print()" class="btn btn-success">📄 Gerar PDF</button>
</div>

<div id="relatorio" style="border:1px solid #e2e8f0; padding:20px; border-radius:8px; margin-top:15px;">
    <h2 style="text-align:center; color:#4f46e5;">Boletim Escolar</h2>
    <p><b>Nome:</b> <?= htmlspecialchars($aluno_dados['nome']) ?></p>
    <p><b>Matrícula:</b> <?= htmlspecialchars($aluno_dados['matricula']) ?></p>
    <p><b>Turma:</b> <?= htmlspecialchars($aluno_dados['turmas']['nome']) ?></p>
    <p><b>Data de emissão:</b> <?= date('d/m/Y H:i') ?></p>
    
    <table>
        <thead>
            <tr><th>Bimestre</th><th>Avaliação</th><th>Nota Máxima</th><th>Nota Obtida</th></tr>
        </thead>
        <tbody>
        <?php foreach ($notas_raw as $n): 
            $av = $n['avaliacoes'];
        ?>
            <tr>
                <td><?= $av['bimestre'] ?>º</td>
                <td><?= htmlspecialchars($av['nome']) ?></td>
                <td><?= number_format($av['nota_maxima'],1,',','.') ?></td>
                <td><b><?= number_format($n['nota'],1,',','.') ?></b></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php elseif ($turma_sel): ?>
    <p style="margin-top:20px; color:#64748b;">Selecione um aluno para visualizar os dados.</p>
<?php endif; ?>
<?php rodape(); ?>