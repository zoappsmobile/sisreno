<?php
session_start();
require_once 'supabase.php';

define('SUPABASE_URL', 'https://nlgsdjtodcnrmglxsbsv.supabase.co');
define('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im5sZ3NkanRvZGNucm1nbHhzYnN2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3OTM4MzMzNzcsImV4cCI6MjEwOTQwOTM3N30.yUGoz81VYFM9lY_uU9ld9cIu1E_qMcWGA8ihV2f2CYk');

$supabase = new SupabaseClient(SUPABASE_URL, SUPABASE_KEY);

function verificaLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
}

function verificaAdmin() {
    verificaLogin();
    if ($_SESSION['perfil'] !== 'admin') {
        header("Location: aluno_home.php");
        exit;
    }
}

function topo($titulo) {
    $nome = $_SESSION['usuario_nome'] ?? '';
    $perfil = $_SESSION['perfil'] ?? '';
    echo "<!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>$titulo - ReNo</title>
        <link rel='stylesheet' href='style.css'>
    </head>
    <body>
    <header class='topo'>
        <div class='logo-area'>
            <a href='" . ($perfil=='admin' ? 'index.php' : 'aluno_home.php') . "'>
                <img src='logo.php' alt='ReNo' class='logo-img'>
            </a>
            <h1>$titulo</h1>
        </div>
        <div class='user-info'>
            <span>👤 $nome ($perfil)</span>
            <a href='" . ($perfil=='admin' ? 'index.php' : 'aluno_home.php') . "' class='btn btn-sm'>🏠 Início</a>
            <a href='logout.php' class='btn btn-sm btn-sair'>🚪 Sair</a>
        </div>
    </header>
    <main class='container'>";
}

function rodape() {
    echo "</main>
    <footer class='rodape'>
        <p>ReNo - Registro de Notas © 2026 (Supabase)</p>
    </footer>
    </body></html>";
}
?>