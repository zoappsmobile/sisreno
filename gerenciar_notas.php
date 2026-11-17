<?php
require_once 'config.php';
verificaAdmin();

$turmas = $conn->query("SELECT * FROM turmas ORDER BY nome");
$turma_sel = isset($_GET['turma_id']) ? (int)$_GET['turma_id'] : 0;

topo("Gerenciar Notas");
?>

<a href="index.php" class="btn">← Voltar</a>

<h2 style="margin-top:20px;">📝 Gerenciar Notas</h2>
<form method="GET">
    <div class="form-group">
        <label>Escolha a Turma:</label>
        <select name="turma_id" required>
            <option value="">-- Selecione --</option>
            <?php while ($t = $turmas->fetch_assoc()): ?>
                <option value="<?= $t['id'] ?>" <?= $t['id']==$turma_sel?'selected':'' ?>><?= htmlspecialchars($t['nome']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <button type="submit" class="btn">Selecionar</button>
</form>

<?php if ($turma_sel): ?>
<div class="acoes" style="margin-top:30px;">
    <a href="lancar_notas.php?turma_id=<?= $turma_sel ?>" class="btn">✏️ Lançar Notas</a>
    <a href="adicionar_avaliacao.php?turma_id=<?= $turma_sel ?>" class="btn btn-warning">➕ Adicionar Avaliação</a>
</div>
<?php endif; ?>

<?php rodape(); ?>