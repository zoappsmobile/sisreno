<?php
require_once 'config.php';
verificaAdmin();
topo("Painel Administrativo");
?>
<h2>Bem-vindo ao ReNo!</h2>
<p>Selecione uma opção abaixo:</p>

<div class="menu-admin">
    <a href="cadastro_turmas.php" class="menu-card">
        <span class="icon">🏫</span>
        <h3>Cadastro de Turmas</h3>
    </a>
    <a href="cadastro_usuarios.php" class="menu-card">
        <span class="icon">👥</span>
        <h3>Cadastro de Usuários</h3>
    </a>
    <a href="consultas.php" class="menu-card">
        <span class="icon">🔍</span>
        <h3>Consultas</h3>
    </a>
    <a href="gerenciar_notas.php" class="menu-card">
        <span class="icon">📝</span>
        <h3>Gerenciar Notas</h3>
    </a>
</div>
<?php rodape(); ?>