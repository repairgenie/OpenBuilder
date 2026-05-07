<!-- templates/docs.php -->
<?php
require_once __DIR__ . '/../src/Parsedown.php';

$doc_name = $_GET['doc'] ?? 'index';
$doc_path = __DIR__ . "/../docs/{$lang}/{$doc_name}.md";

if (!file_exists($doc_path)) {
    echo "<div class='card'>Document not found / Documento no encontrado</div>";
} else {
    $markdown = file_get_contents($doc_path);
    $Parsedown = new Parsedown();
    echo "<div class='card prose max-w-none'>";
    echo $Parsedown->text($markdown);
    echo "</div>";
}
?>
