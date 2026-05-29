<!-- templates/create_daily_log.php -->
<?php
require_once __DIR__ . '/../src/WeatherProvider.php';
require_once __DIR__ . '/../src/GPSEngine.php';
$weather_provider = new WeatherProvider();
$current_weather = $weather_provider->getWeather($lang);
?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-2xl font-bold text-black">
        <?php echo $lang === 'es' ? 'Nuevo Diario de Obra' : 'New Daily Log'; ?>
    </h2>
</div>

<div class="card">
    <form action="index.php?lang=<?php echo $lang; ?>" method="POST" x-data="gpsStamp()">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="action" value="create_daily_log">
        <input type="hidden" name="latitude" :value="lat">
        <input type="hidden" name="longitude" :value="lon">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha' : 'Date'; ?></label>
                <input type="date" name="log_date" value="<?php echo date('Y-m-d'); ?>" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
            </div>
            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Clima' : 'Weather'; ?></label>
                <input type="text" name="weather" value="<?php echo $current_weather['display']; ?>" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
            </div>
            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Personal (Núm. Personas)' : 'Manpower (Headcount)'; ?></label>
                <input type="number" name="manpower" placeholder="10" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
            </div>
            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'GPS' : 'GPS'; ?></label>
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span x-show="lat && lon" class="font-mono">
                        <span x-text="lat ? lat.toFixed(6) : '-'"></span>,
                        <span x-text="lon ? lon.toFixed(6) : '-'"></span>
                        <span x-show="locationStatus === 'found'" class="text-success">OK</span>
                        <span x-show="locationStatus === 'error'" class="text-danger">FAILED</span>
                    </span>
                    <span x-show="!lat && locationStatus === 'requesting'" class="animate-pulse text-slate-400">
                        <?php echo $lang === 'es' ? 'Obteniendo GPS...' : 'Getting GPS...'; ?>
                    </span>
                    <span x-show="!lat && !locationStatus" class="text-slate-400">
                        <?php echo $lang === 'es' ? 'Sin GPS' : 'No GPS'; ?>
                    </span>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Trabajo Realizado (Notas de Campo)' : 'Work Performed (Field Notes)'; ?></label>
                <textarea name="work_performed" rows="6" placeholder="<?php echo $lang === 'es' ? 'Escribe tus notas aquí...' : 'Enter your field notes here...'; ?>" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary"></textarea>
                <p class="mt-2 text-xs text-slate-500 italic"><?php echo $lang === 'es' ? 'OpenBuilder AI transformará estas notas en un informe profesional.' : 'OpenBuilder AI will transform these notes into a professional report.'; ?></p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="?page=daily_logs&lang=<?php echo $lang; ?>" class="rounded border border-stroke py-2 px-6 font-medium text-black hover:shadow-md transition-all"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></a>
            <button type="submit" class="rounded bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90 shadow-md transition-all"><?php echo $lang === 'es' ? 'Guardar y Generar Informe' : 'Save & Generate Report'; ?></button>
        </div>
    </form>
</div>

<script>
function gpsStamp() {
    return {
        lat: null,
        lon: null,
        locationStatus: null,
        init() {
            this.requestGPS();
        },
        requestGPS() {
            if (!navigator.geolocation) {
                this.locationStatus = 'error';
                return;
            }
            this.locationStatus = 'requesting';
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.lat = pos.coords.latitude;
                    this.lon = pos.coords.longitude;
                    this.locationStatus = 'found';
                },
                (err) => {
                    this.locationStatus = 'error';
                    console.warn('Geolocation error:', err.message);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 30000 }
            );
        }
    };
}
</script>