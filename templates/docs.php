<!-- templates/docs.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';
require_once __DIR__ . '/../src/PermissionHelper.php';

session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../index.php?page=login');
    exit;
}
$perm = new PermissionHelper(Database::connect());
$pdo = Database::connect();
$lang = $_GET['lang'] ?? 'en';
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Fetch docs with latest version info
$docs = $pdo->query("
    SELECT d.*, dv.revision, dv.file_path, dv.file_size, u.name as uploader_name
    FROM closeout_documents d
    LEFT JOIN (
        SELECT doc_id, revision, file_path, file_size, uploaded_by,
               ROW_NUMBER() OVER (PARTITION BY doc_id ORDER BY id DESC) as rn
        FROM doc_versions
    ) dv ON d.id = dv.doc_id AND dv.rn = 1
    LEFT JOIN users u ON dv.uploaded_by = u.id
    ORDER BY d.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch RFIs for linking
$rfis = $pdo->query("SELECT id, ref_number, subject FROM rfis ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$labels = [
    'en' => [
        'title' => 'Document Control',
        'upload' => 'Upload Document',
        'title_col' => 'Title',
        'type' => 'Type',
        'revision' => 'Rev',
        'status' => 'Status',
        'checked_out' => 'Checked Out',
        'date' => 'Date',
        'actions' => 'Actions',
        'check_out' => 'Check Out',
        'check_in' => 'Check In',
        'link' => 'Link',
        'delete' => 'Delete',
        'discipline' => 'Discipline',
        'notes' => 'Notes',
        'select_file' => 'Select File',
        'submit_btn' => 'Submit',
        'no_docs' => 'No documents yet. Upload the first one.',
        'search' => 'Search documents...',
        'all_types' => 'All Types',
        'all_status' => 'All Statuses',
        'drawings' => 'Drawings',
        'specs' => 'Specifications',
        'permits' => 'Permits',
        'contracts' => 'Contracts',
        'safety' => 'Safety Documents',
        'other' => 'Other',
    ],
    'es' => [
        'title' => 'Control de Documentos',
        'upload' => 'Subir Documento',
        'title_col' => 'Titulo',
        'type' => 'Tipo',
        'revision' => 'Rev',
        'status' => 'Estado',
        'checked_out' => 'Prestado',
        'date' => 'Fecha',
        'actions' => 'Acciones',
        'check_out' => 'Retirar',
        'check_in' => 'Devolver',
        'link' => 'Vincular',
        'delete' => 'Eliminar',
        'discipline' => 'Disciplina',
        'notes' => 'Notas',
        'select_file' => 'Seleccionar Archivo',
        'submit_btn' => 'Enviar',
        'no_docs' => 'Sin documentos. Suba el primero.',
        'search' => 'Buscar documentos...',
        'all_types' => 'Todos los Tipos',
        'all_status' => 'Todos los Estados',
        'drawings' => 'Planos',
        'specs' => 'Especificaciones',
        'permits' => 'Permisos',
        'contracts' => 'Contratos',
        'safety' => 'Documentos de Seguridad',
        'other' => 'Otro',
    ]
];
$t = $labels[$lang];
?>

<!-- Toast -->
<?php if ($flash_success): ?>
<div id="toast" class="fixed top-4 right-4 z-9999 flex items-center gap-3 bg-success text-white px-6 py-4 rounded-lg shadow-xl">
    <span><?php echo htmlspecialchars($flash_success); ?></span>
    <button onclick="this.parentElement.remove()" class="hover:opacity-80">X</button>
</div>
<script>setTimeout(() => document.getElementById('toast')?.remove(), 4000);</script>
<?php endif; ?>

<div class="p-6 max-w-7xl mx-auto">
    <!-- Header (outside Alpine x-data so Playwright always finds it) -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-primary"><?php echo htmlspecialchars($t['title']); ?></h1>
        </div>
        <button @click="showUpload = true" class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg hover:bg-opacity-90 transition-all active:scale-95">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            <?php echo htmlspecialchars($t['upload']); ?>
        </button>
    </div>

    <!-- Everything interactive lives in Alpine -->
    <div x-data="docsApp()">
        <!-- Filter bar -->
        <div class="flex flex-wrap gap-3 mb-5">
            <input type="text" x-model="search" :placeholder="<?php echo htmlspecialchars($t['search']); ?>" class="border border-stroke rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary w-64" />
            <select x-model="filterType" class="border border-stroke rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value=""><?php echo htmlspecialchars($t['all_types']); ?></option>
                <option value="Drawings"><?php echo htmlspecialchars($t['drawings']); ?></option>
                <option value="Specifications"><?php echo htmlspecialchars($t['specs']); ?></option>
                <option value="Permits"><?php echo htmlspecialchars($t['permits']); ?></option>
                <option value="Contracts"><?php echo htmlspecialchars($t['contracts']); ?></option>
                <option value="Safety"><?php echo htmlspecialchars($t['safety']); ?></option>
                <option value="Other"><?php echo htmlspecialchars($t['other']); ?></option>
            </select>
            <select x-model="filterStatus" class="border border-stroke rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value=""><?php echo htmlspecialchars($t['all_status']); ?></option>
                <option value="Active">Active</option>
                <option value="Archived">Archived</option>
            </select>
        </div>

        <!-- Upload Modal -->
        <div x-show="showUpload" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display:none">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.away="showUpload = false">
                <h2 class="text-xl font-bold mb-5"><?php echo htmlspecialchars($t['upload']); ?></h2>
                <form action="?page=docs_handler" method="POST" enctype="multipart/form-data" @submit="showUpload = false">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="upload" />
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1"><?php echo htmlspecialchars($t['title_col']); ?></label>
                            <input type="text" name="title" required class="w-full border border-stroke rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1"><?php echo htmlspecialchars($t['type']); ?></label>
                            <select name="doc_type" required class="w-full border border-stroke rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="Drawings"><?php echo htmlspecialchars($t['drawings']); ?></option>
                                <option value="Specifications"><?php echo htmlspecialchars($t['specs']); ?></option>
                                <option value="Permits"><?php echo htmlspecialchars($t['permits']); ?></option>
                                <option value="Contracts"><?php echo htmlspecialchars($t['contracts']); ?></option>
                                <option value="Safety"><?php echo htmlspecialchars($t['safety']); ?></option>
                                <option value="Other"><?php echo htmlspecialchars($t['other']); ?></option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1"><?php echo htmlspecialchars($t['discipline']); ?></label>
                            <select name="discipline" class="w-full border border-stroke rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="">--</option>
                                <option value="Structural">Structural</option>
                                <option value="Architectural">Architectural</option>
                                <option value="MEP">MEP</option>
                                <option value="Civil">Civil</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1"><?php echo htmlspecialchars($t['notes']); ?></label>
                            <textarea name="notes" rows="2" class="w-full border border-stroke rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1"><?php echo htmlspecialchars($t['select_file']); ?></label>
                            <input type="file" name="file" required class="w-full border border-stroke rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showUpload = false" class="px-5 py-2.5 rounded-lg border border-stroke hover:bg-slate-50 transition-all">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white hover:bg-opacity-90 transition-all active:scale-95"><?php echo htmlspecialchars($t['submit_btn']); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Docs Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-stroke">
                        <th class="text-left px-4 py-3 font-semibold"><?php echo htmlspecialchars($t['title_col']); ?></th>
                        <th class="text-left px-4 py-3 font-semibold"><?php echo htmlspecialchars($t['type']); ?></th>
                        <th class="text-left px-4 py-3 font-semibold"><?php echo htmlspecialchars($t['revision']); ?></th>
                        <th class="text-left px-4 py-3 font-semibold"><?php echo htmlspecialchars($t['status']); ?></th>
                        <th class="text-left px-4 py-3 font-semibold"><?php echo htmlspecialchars($t['checked_out']); ?></th>
                        <th class="text-left px-4 py-3 font-semibold"><?php echo htmlspecialchars($t['date']); ?></th>
                        <th class="text-left px-4 py-3 font-semibold"><?php echo htmlspecialchars($t['actions']); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($docs)): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400 italic"><?php echo htmlspecialchars($t['no_docs']); ?></td></tr>
                    <?php else: ?>
                    <?php foreach ($docs as $d): ?>
                    <?php
                        $title_display = $lang === 'es' ? htmlspecialchars($d['title_es'] ?? $d['title_en']) : htmlspecialchars($d['title_en'] ?? '');
                    ?>
                    <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-medium"><?php echo $title_display; ?></td>
                        <td class="px-4 py-3 text-slate-500"><?php echo htmlspecialchars($d['type'] ?? ''); ?></td>
                        <td class="px-4 py-3"><span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs font-mono"><?php echo htmlspecialchars($d['revision'] ?? '-'); ?></span></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs <?php echo $d['status'] === 'Active' ? 'bg-success bg-opacity-10 text-success' : 'bg-slate-100 text-slate-500'; ?>">
                                <?php echo htmlspecialchars($d['status']); ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm <?php echo $d['checked_out_by'] ? 'text-danger font-medium' : 'text-slate-400'; ?>">
                            <?php echo $d['checked_out_by'] ? htmlspecialchars($d['checked_out_by']) : '-'; ?>
                        </td>
                        <td class="px-4 py-3 text-slate-500"><?php echo htmlspecialchars($d['created_at'] ?? ''); ?></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <?php if (!$d['checked_out_by']): ?>
                                <button @click="checkOut(<?php echo intval($d['id']); ?>)" class="text-xs text-primary hover:underline"><?php echo htmlspecialchars($t['check_out']); ?></button>
                                <?php else: ?>
                                <button @click="checkIn(<?php echo intval($d['id']); ?>)" class="text-xs text-success hover:underline"><?php echo htmlspecialchars($t['check_in']); ?></button>
                                <?php endif; ?>
                                <button @click="showRevisions(<?php echo intval($d['id']); ?>)" class="text-xs text-slate-500 hover:underline"><?php echo htmlspecialchars($t['revision']); ?></button>
                                <button @click="linkDoc(<?php echo intval($d['id']); ?>)" class="text-xs text-slate-500 hover:underline"><?php echo htmlspecialchars($t['link']); ?></button>
                                <button @click="deleteDoc(<?php echo intval($d['id']); ?>, '<?php echo htmlspecialchars($d['title_en']); ?>')" class="text-xs text-danger hover:underline"><?php echo htmlspecialchars($t['delete']); ?></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Revision History Modal -->
        <div x-show="showRevisionsPanel" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display:none">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.away="showRevisionsPanel = false">
                <h2 class="text-xl font-bold mb-5"><?php echo $lang === 'es' ? 'Historial de Revisiones' : 'Revision History'; ?></h2>
                <div id="revisions-content"></div>
                <h3 class="font-semibold mt-4 mb-2"><?php echo $lang === 'es' ? 'Subir Nueva Revision' : 'Upload New Revision'; ?></h3>
                <form action="?page=docs_handler" method="POST" enctype="multipart/form-data" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="new_revision" />
                    <input type="hidden" name="doc_id" :value="revDocId" />
                    <input type="file" name="file" required class="w-full border border-stroke rounded-lg px-4 py-2" />
                    <textarea name="notes" :placeholder="<?php echo $lang === 'es' ? 'Notas...' : 'Notes...'; ?>" rows="2" class="w-full border border-stroke rounded-lg px-4 py-2"></textarea>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-opacity-90 transition-all"><?php echo $lang === 'es' ? 'Subir' : 'Upload'; ?></button>
                </form>
                <div class="flex justify-end mt-4">
                    <button @click="showRevisionsPanel = false" class="px-4 py-2 rounded-lg border border-stroke hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cerrar' : 'Close'; ?></button>
                </div>
            </div>
        </div>

        <!-- Link Modal -->
        <div x-show="showLinkPanel" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" style="display:none">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 p-6" @click.away="showLinkPanel = false">
                <h2 class="text-xl font-bold mb-5"><?php echo $lang === 'es' ? 'Vincular Documento' : 'Link Document'; ?></h2>
                <form action="?page=docs_handler" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="link" />
                    <input type="hidden" name="doc_id" :value="linkDocId" />
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium mb-1"><?php echo $lang === 'es' ? 'Tipo' : 'Type'; ?></label>
                            <select name="link_type" x-model="linkType" class="w-full border border-stroke rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="rfi">RFI</option>
                                <option value="submittal">Submittal</option>
                                <option value="change_order">Change Order</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1"><?php echo $lang === 'es' ? 'ID' : 'ID'; ?></label>
                            <select name="link_id" class="w-full border border-stroke rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                <?php foreach($rfis as $rfi): ?>
                                <option value="<?php echo intval($rfi['id']); ?>"><?php echo htmlspecialchars($rfi['ref_number'].' - '.substr($rfi['subject'],0,50)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" @click="showLinkPanel = false" class="px-5 py-2.5 rounded-lg border border-stroke hover:bg-slate-50 transition-all"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white hover:bg-opacity-90 transition-all active:scale-95"><?php echo htmlspecialchars($t['link']); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- end x-data -->
</div><!-- end p-6 -->

<script>
function docsApp() {
    return {
        showUpload: false,
        showRevisionsPanel: false,
        showLinkPanel: false,
        revDocId: null,
        linkDocId: null,
        linkType: 'rfi',
        search: '',
        filterType: '',
        filterStatus: '',

        checkOut(id) {
            fetch('?page=docs_handler', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=check_out&doc_id=' + id + '&csrf_token=<?php echo csrf_token(); ?>'
            }).then(r => r.json()).then(d => d.success && location.reload());
        },
        checkIn(id) {
            fetch('?page=docs_handler', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=check_in&doc_id=' + id + '&csrf_token=<?php echo csrf_token(); ?>'
            }).then(r => r.json()).then(d => d.success && location.reload());
        },
        showRevisions(id) {
            this.revDocId = id;
            this.showRevisionsPanel = true;
        },
        linkDoc(id) {
            this.linkDocId = id;
            this.showLinkPanel = true;
        },
        deleteDoc(id, title) {
            Swal.fire({
                title: 'Delete Document?',
                text: '"' + title + '" will be permanently deleted.',
                icon: 'warning',
                confirmButtonText: 'Delete',
                confirmButtonColor: '#dc2626',
                showCancelButton: true,
            }).then(result => {
                if (result.isConfirmed) {
                    fetch('?page=docs_handler', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: 'action=delete&doc_id=' + id + '&csrf_token=<?php echo csrf_token(); ?>'
                    }).then(r => r.json()).then(d => d.success && location.reload());
                }
            });
        }
    }
}
</script>