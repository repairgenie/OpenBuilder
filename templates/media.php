<!-- templates/media.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/PermissionHelper.php';
require_once __DIR__ . '/../src/Storage.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();
$user = PermissionHelper::getCurrentUser();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$projects = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$cost_codes = $pdo->query("SELECT id, code, description FROM cost_codes ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);
$rfis = $pdo->query("SELECT id, subject FROM rfis ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$punch_items = $pdo->query("SELECT id, description FROM punch_list_items ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$media = $pdo->query("
    SELECT m.*, u.name as uploaded_by_name, p.name as project_name, cc.code as cost_code
    FROM media m
    LEFT JOIN users u ON m.uploaded_by = u.id
    LEFT JOIN projects p ON m.project_id = p.id
    LEFT JOIN cost_codes cc ON m.cost_code_id = cc.id
    ORDER BY m.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Flash Messages -->
<?php if ($flash_success): ?>
<div id="toast-success" class="fixed top-4 right-4 z-9999 flex items-center gap-3 bg-success text-white px-6 py-4 rounded-lg shadow-xl">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
    <span class="font-medium"><?php echo htmlspecialchars($flash_success); ?></span>
    <button onclick="document.getElementById('toast-success').remove()" class="ml-2 hover:opacity-80">✕</button>
</div>
<script>setTimeout(() => document.getElementById('toast-success')?.remove(), 4000);</script>
<?php endif; ?>

<?php if ($flash_error): ?>
<div id="toast-error" class="fixed top-4 right-4 z-9999 flex items-center gap-3 bg-danger text-white px-6 py-4 rounded-lg shadow-xl">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
    <span class="font-medium"><?php echo htmlspecialchars($flash_error); ?></span>
    <button onclick="document.getElementById('toast-error').remove()" class="ml-2 hover:opacity-80">✕</button>
</div>
<script>setTimeout(() => document.getElementById('toast-error')?.remove(), 4000);</script>
<?php endif; ?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Biblioteca de Medios' : 'Media Library'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Subir, organizar y anotar fotos del proyecto.' : 'Upload, organize, and annotate project photos.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('upload-media-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Subir Foto' : 'Upload Photo'; ?>
        </button>
    </div>
</div>

<!-- Gallery Grid -->
<div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-4">
    <?php foreach ($media as $m): ?>
    <div class="card overflow-hidden hover:shadow-md transition-shadow cursor-pointer" onclick='viewMedia(<?php echo json_encode($m); ?>)'>
        <?php
        $file_path = $m['file_path'];
        $is_image = in_array($m['mime_type'], ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
        ?>
        <?php if ($is_image && file_exists($file_path)): ?>
        <div class="h-32 bg-slate-100 overflow-hidden">
            <img src="<?php echo htmlspecialchars($file_path); ?>" alt="<?php echo htmlspecialchars($m['title'] ?? ''); ?>" class="w-full h-full object-cover">
        </div>
        <?php else: ?>
        <div class="h-32 bg-slate-100 flex items-center justify-center">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-400"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"></path></svg>
        </div>
        <?php endif; ?>
        <div class="p-2">
            <p class="text-xs font-bold text-black truncate"><?php echo htmlspecialchars($m['title'] ?? basename($m['filename'])); ?></p>
            <p class="text-[10px] text-slate-400"><?php echo htmlspecialchars($m['project_name'] ?? ''); ?></p>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($media)): ?>
    <div class="col-span-6 py-12 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'No hay medios cargados.' : 'No media uploaded.'; ?></div>
    <?php endif; ?>
</div>

<!-- Upload Modal -->
<div id="upload-media-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg"><?php echo $lang === 'es' ? 'Subir Foto' : 'Upload Photo'; ?></h3>
            <button onclick="closeModal('upload-media-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/media_handler.php" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="action" value="upload_media">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="uploaded_by" value="<?php echo $user['id']; ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Título' : 'Title'; ?></label>
                <input type="text" name="title" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Proyecto' : 'Project'; ?></label>
                    <select name="project_id" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguno' : 'None'; ?> --</option>
                        <?php foreach ($projects as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Código de Costo' : 'Cost Code'; ?></label>
                    <select name="cost_code_id" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary searchable-select">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguno' : 'None'; ?> --</option>
                        <?php foreach ($cost_codes as $cc): ?>
                        <option value="<?php echo $cc['id']; ?>"><?php echo htmlspecialchars($cc['code'] . ' - ' . $cc['description']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha de Toma' : 'Date Taken'; ?></label>
                    <input type="date" name="date_taken" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Etiquetas' : 'Tags'; ?></label>
                    <input type="text" name="tags" placeholder="safety, progress" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Archivo' : 'File'; ?> *</label>
                <input type="file" name="media_file" accept="image/*" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:bg-slate-100 file:text-slate-600">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('upload-media-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Subir' : 'Upload'; ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Media View / Annotation Modal -->
<div id="view-media-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-90 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-4xl mx-4 overflow-hidden flex flex-col" style="max-height:90vh;">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="media-title-display"><?php echo $lang === 'es' ? 'Medio' : 'Media'; ?></h3>
            <button onclick="closeModal('view-media-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="flex flex-1 overflow-hidden">
            <div class="flex-1 bg-slate-900 flex items-center justify-center relative" id="annotation-area">
                <img id="media-image-display" src="" alt="" class="max-w-full max-h-[60vh] object-contain">
                <canvas id="annotation-canvas" class="absolute top-0 left-0 w-full h-full pointer-events-none"></canvas>
            </div>
            <div class="w-64 border-l border-stroke p-4 overflow-y-auto">
                <h4 class="font-bold text-black mb-3"><?php echo $lang === 'es' ? 'Información' : 'Information'; ?></h4>
                <dl class="text-sm space-y-2">
                    <dt class="text-slate-500 font-medium"><?php echo $lang === 'es' ? 'Proyecto' : 'Project'; ?></dt>
                    <dd class="text-black" id="media-project-display">-</dd>
                    <dt class="text-slate-500 font-medium"><?php echo $lang === 'es' ? 'Código de Costo' : 'Cost Code'; ?></dt>
                    <dd class="text-black font-mono text-xs" id="media-cost-display">-</dd>
                    <dt class="text-slate-500 font-medium"><?php echo $lang === 'es' ? 'Fecha' : 'Date'; ?></dt>
                    <dd class="text-black" id="media-date-display">-</dd>
                </dl>
                <hr class="my-4 border-stroke">
                <h4 class="font-bold text-black mb-3"><?php echo $lang === 'es' ? 'Vincular a' : 'Link To'; ?></h4>
                <div class="space-y-2">
                    <select id="link-type-select" class="w-full rounded border border-stroke py-2 px-2 text-sm">
                        <option value="rfi">RFI</option>
                        <option value="punch_item"><?php echo $lang === 'es' ? 'Punch List' : 'Punch List'; ?></option>
                    </select>
                    <select id="link-id-select" class="w-full rounded border border-stroke py-2 px-2 text-sm">
                        <?php foreach ($rfis as $r): ?>
                        <option value="rfi:<?php echo $r['id']; ?>">RFI-<?php echo $r['id']; ?>: <?php echo htmlspecialchars(substr($r['subject'], 0, 30)); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button onclick="linkMedia()" class="w-full rounded bg-primary py-2 text-sm font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Vincular' : 'Link'; ?></button>
                </div>
                <hr class="my-4 border-stroke">
                <h4 class="font-bold text-black mb-3"><?php echo $lang === 'es' ? 'Anotación' : 'Annotation'; ?></h4>
                <p class="text-xs text-slate-500 mb-2"><?php echo $lang === 'es' ? 'Dibuja un rectángulo para anotar' : 'Draw a rectangle to annotate'; ?></p>
                <button onclick="startAnnotation()" class="w-full rounded border border-stroke py-2 text-sm font-medium text-black hover:bg-slate-50"><?php echo $lang === 'es' ? 'Iniciar Anotación' : 'Start Annotation'; ?></button>
                <button onclick="confirmDelete(document.getElementById('current-media-id').value)" class="w-full rounded border border-stroke py-2 text-sm font-medium text-danger mt-2 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?></button>
            </div>
        </div>
        <input type="hidden" id="current-media-id">
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function viewMedia(m) {
    document.getElementById('current-media-id').value = m.id;
    document.getElementById('media-title-display').textContent = m.title || m.filename;
    document.getElementById('media-image-display').src = m.file_path;
    document.getElementById('media-project-display').textContent = m.project_name || '-';
    document.getElementById('media-cost-display').textContent = m.cost_code || '-';
    document.getElementById('media-date-display').textContent = m.date_taken || '-';
    openModal('view-media-modal');
}

function linkMedia() {
    const media_id = document.getElementById('current-media-id').value;
    const link_type = document.getElementById('link-type-select').value;
    const link_id = document.getElementById('link-id-select').value;
    const [type, id] = link_id.split(':');

    fetch('templates/media_handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'link_media',
            media_id: media_id,
            linked_type: type,
            linked_id_raw: link_id,
            lang: '<?php echo $lang; ?>',
            csrf_token: '<?php echo csrf_token(); ?>'
        })
    }).then(() => {
        Swal.fire('<?php echo $lang === 'es' ? 'Vinculado exitosamente' : 'Linked successfully'; ?>', '', 'success');
    });
}

let annotating = false;
let annotatingEl = null;
let startX, startY;

function startAnnotation() {
    annotating = true;
    const canvas = document.getElementById('annotation-canvas');
    canvas.style.pointerEvents = 'auto';
    annotatingDiv = document.getElementById('annotation-area');
    annotatingDiv.addEventListener('mousedown', (e) => {
        startX = e.offsetX;
        startY = e.offsetY;
    });
    annotatingDiv.addEventListener('mouseup', (e) => {
        if (!annotating) return;
        const endX = e.offsetX, endY = e.offsetY;
        const w = Math.abs(endX - startX), h = Math.abs(endY - startY);
        const ctx = canvas.getContext('2d');
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
        ctx.strokeStyle = 'red';
        ctx.lineWidth = 2;
        ctx.strokeRect(Math.min(startX, endX), Math.min(startY, endY), w, h);
        annotating = false;
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar este medio?' : 'Delete this media?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, eliminar' : 'Yes, delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/media_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_media">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>