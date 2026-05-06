<?php
$page = $_GET['page'] ?? 'dashboard';

// Database Setup
$db_file = __DIR__ . '/database.sqlite';
try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create RFIs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rfis (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ref_number TEXT NOT NULL,
            subject TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'Open',
            priority TEXT NOT NULL DEFAULT 'Medium',
            due_date TEXT NOT NULL
        )
    ");
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

// Handle RFI Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_rfi') {
    $stmt = $pdo->prepare("INSERT INTO rfis (ref_number, subject, status, priority, due_date) VALUES (:ref_number, :subject, :status, :priority, :due_date)");
    $stmt->execute([
        ':ref_number' => $_POST['ref_number'] ?? '',
        ':subject' => $_POST['subject'] ?? '',
        ':status' => $_POST['status'] ?? 'Open',
        ':priority' => $_POST['priority'] ?? 'Medium',
        ':due_date' => $_POST['due_date'] ?? ''
    ]);
    header("Location: index.php?page=rfis");
    exit;
}

// Fetch RFIs
$rfi_search = $_GET['search'] ?? '';
if ($rfi_search) {
    $stmt = $pdo->prepare("SELECT * FROM rfis WHERE subject LIKE :search OR ref_number LIKE :search ORDER BY id DESC");
    $stmt->execute([':search' => "%$rfi_search%"]);
    $rfis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("SELECT * FROM rfis ORDER BY id DESC");
    $rfis = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Mock Data
$recent_activities = [
    ['id' => 1, 'event' => 'Concrete Pour - Foundation', 'date' => 'Oct 24, 2023', 'status' => 'Completed'],
    ['id' => 2, 'event' => 'Structural Steel Delivery', 'date' => 'Oct 25, 2023', 'status' => 'Pending'],
    ['id' => 3, 'event' => 'Plumbing Rough-in Inspection', 'date' => 'Oct 26, 2023', 'status' => 'Scheduled'],
    ['id' => 4, 'event' => 'RFI #42 - Change to Beam Specs', 'date' => 'Oct 24, 2023', 'status' => 'In Review'],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenBuilder - Construction Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3C50E0',
                        bodybg: '#F1F5F9',
                        success: '#219653',
                        warning: '#F2994A',
                        danger: '#D34053',
                        stroke: '#E2E8F0',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-bodybg text-slate-800 font-sans">
    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <aside class="absolute left-0 top-0 z-9999 flex h-screen w-72 flex-col overflow-y-hidden bg-slate-900 duration-300 ease-linear lg:static lg:translate-x-0 -translate-x-full">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between gap-2 px-6 py-5.5 lg:py-6.5 mt-5">
                <a href="index.php" class="text-2xl font-bold text-white flex items-center gap-2">
                    <span class="text-primary text-3xl">Open</span>Builder
                </a>
            </div>

            <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
                <nav class="mt-5 py-4 px-4 lg:mt-9 lg:px-6">
                    <div>
                        <h3 class="mb-4 ml-4 text-sm font-semibold text-slate-400">MENU</h3>
                        <ul class="mb-6 flex flex-col gap-1.5">
                            <!-- Menu Item Dashboard -->
                            <li>
                                <a class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium <?php echo $page === 'dashboard' ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white'; ?> duration-300 ease-in-out" href="?page=dashboard">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.95 9.05a.5.5 0 0 0-.25-.43l-7-4a.5.5 0 0 0-.5 0l-7 4a.5.5 0 0 0-.25.43v7.9a.5.5 0 0 0 .25.43l7 4a.5.5 0 0 0 .5 0l7-4a.5.5 0 0 0 .25-.43v-7.9zm-8-3.46l5.77 3.3-2.18 1.25-5.77-3.3 2.18-1.25zm-6.27 4.17l4.77-2.73v5.46l-4.77 2.73V9.76zm1 6.32l4.77-2.73 2.18 1.25-4.77 2.73-2.18-1.25zm6.77-1.46v-5.46l4.77-2.73v5.46l-4.77 2.73z"></path>
                                    </svg>
                                    Dashboard
                                </a>
                            </li>
                            <!-- Menu Item RFIs -->
                            <li>
                                <a class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium <?php echo in_array($page, ['rfis', 'create_rfi']) ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white'; ?> duration-300 ease-in-out" href="?page=rfis">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12zM6 10h2v2H6zm0 4h8v2H6zm10 0h2v2h-2zm-6-4h8v2h-8z"></path>
                                    </svg>
                                    RFIs
                                </a>
                            </li>
                            <!-- Menu Item Budget -->
                            <li>
                                <a class="group relative flex items-center gap-2.5 rounded-sm px-4 py-2 font-medium <?php echo $page === 'budget' ? 'text-white bg-slate-800' : 'text-slate-300 hover:bg-slate-800 hover:text-white'; ?> duration-300 ease-in-out" href="?page=budget">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"></path>
                                    </svg>
                                    Budget
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <!-- Header -->
            <header class="sticky top-0 z-999 flex w-full bg-white drop-shadow-1">
                <div class="flex flex-grow items-center justify-between px-4 py-4 shadow-2 md:px-6 2xl:px-11">
                    <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
                        <button class="z-99999 block rounded-sm border border-slate-200 bg-white p-1.5 shadow-sm">
                            <span class="relative block h-5.5 w-5.5 cursor-pointer">
                                <span class="block absolute right-0 h-full w-full">
                                    <span class="relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-[0] duration-200 ease-in-out"></span>
                                    <span class="relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-150 duration-200 ease-in-out"></span>
                                    <span class="relative left-0 top-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-200 duration-200 ease-in-out"></span>
                                </span>
                            </span>
                        </button>
                    </div>

                    <div class="hidden sm:block">
                        <form action="index.php" method="POST">
                            <div class="relative">
                                <button class="absolute left-0 top-1/2 -translate-y-1/2">
                                    <svg class="fill-slate-500 hover:fill-primary" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M9.16666 3.33332C5.945 3.33332 3.33332 5.945 3.33332 9.16666C3.33332 12.3883 5.945 15 9.16666 15C12.3883 15 15 12.3883 15 9.16666C15 5.945 12.3883 3.33332 9.16666 3.33332ZM1.66666 9.16666C1.66666 5.02452 5.02452 1.66666 9.16666 1.66666C13.3088 1.66666 16.6667 5.02452 16.6667 9.16666C16.6667 11.0028 15.9922 12.6841 14.8876 13.9875L18.0893 17.1892C18.4147 17.5147 18.4147 18.0423 18.0893 18.3677C17.7638 18.6932 17.2362 18.6932 16.9107 18.3677L13.709 15.166C12.4056 16.2706 10.7243 16.9451 8.8882 16.9451C4.74606 16.9451 1.3882 13.5872 1.3882 9.4451C1.3882 5.30296 4.74606 1.9451 8.8882 1.9451C13.0303 1.9451 16.3882 5.30296 16.3882 9.4451C16.3882 13.5872 13.0303 16.9451 8.8882 16.9451Z" fill=""></path>
                                    </svg>
                                </button>
                                <input type="text" placeholder="Type to search..." class="w-full bg-transparent pl-9 pr-4 font-medium focus:outline-none xl:w-125">
                            </div>
                        </form>
                    </div>

                    <div class="flex items-center gap-3 2x:gap-7">
                        <ul class="flex items-center gap-2 2x:gap-4">
                        </ul>

                        <!-- User Area -->
                        <div class="relative">
                            <a class="flex items-center gap-4" href="#">
                                <span class="hidden text-right lg:block">
                                    <span class="block text-sm font-medium text-black">John Doe</span>
                                    <span class="block text-xs">Project Manager</span>
                                </span>
                                <span class="h-12 w-12 rounded-full overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name=John+Doe&background=3C50E0&color=fff" alt="User">
                                </span>
                                <svg class="hidden fill-current sm:block" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.410765 0.910734C0.736202 0.585297 1.26384 0.585297 1.58928 0.910734L6.00002 5.32148L10.4108 0.910734C10.7362 0.585297 11.2638 0.585297 11.5893 0.910734C11.9147 1.23617 11.9147 1.76381 11.5893 2.08924L6.58928 7.08924C6.26384 7.41468 5.7362 7.41468 5.41076 7.08924L0.410765 2.08924C0.0853277 1.76381 0.0853277 1.23617 0.410765 0.910734Z" fill=""></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main>
                <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

                    <?php if ($page === 'dashboard'): ?>
                    <!-- Dashboard -->

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">

                        <!-- Card 1: Total Open RFIs -->
                        <div class="rounded-sm border border-slate-200 bg-white py-6 px-7.5 shadow-default">
                            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-slate-100">
                                <svg class="fill-primary" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12zM6 10h2v2H6zm0 4h8v2H6zm10 0h2v2h-2zm-6-4h8v2h-8z"></path>
                                </svg>
                            </div>
                            <div class="mt-4 flex items-end justify-between">
                                <div>
                                    <h4 class="text-title-md font-bold text-black">12</h4>
                                    <span class="text-sm font-medium">Total Open RFIs</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Budget Utilization % -->
                        <div class="rounded-sm border border-slate-200 bg-white py-6 px-7.5 shadow-default">
                            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-slate-100">
                                <svg class="fill-primary" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"></path>
                                </svg>
                            </div>
                            <div class="mt-4 flex items-end justify-between">
                                <div>
                                    <h4 class="text-title-md font-bold text-black">64%</h4>
                                    <span class="text-sm font-medium">Budget Utilization</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Pending Submittals -->
                        <div class="rounded-sm border border-slate-200 bg-white py-6 px-7.5 shadow-default">
                            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-slate-100">
                                <svg class="fill-primary" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"></path>
                                </svg>
                            </div>
                            <div class="mt-4 flex items-end justify-between">
                                <div>
                                    <h4 class="text-title-md font-bold text-black">8</h4>
                                    <span class="text-sm font-medium">Pending Submittals</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: Safety Days -->
                        <div class="rounded-sm border border-slate-200 bg-white py-6 px-7.5 shadow-default">
                            <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-slate-100">
                                <svg class="fill-primary" width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"></path>
                                </svg>
                            </div>
                            <div class="mt-4 flex items-end justify-between">
                                <div>
                                    <h4 class="text-title-md font-bold text-black">120</h4>
                                    <span class="text-sm font-medium">Safety Days</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-2 2xl:gap-7.5">

                        <!-- Project Progress (Mocked Bar Chart) -->
                        <div class="col-span-1 rounded-sm border border-slate-200 bg-white px-5 pt-7.5 pb-5 shadow-default sm:px-7.5">
                            <h3 class="text-xl font-semibold text-black mb-4">Project Progress</h3>

                            <div class="space-y-4">
                                <!-- Phase 1 -->
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-black">Foundation</span>
                                        <span class="font-medium text-primary">100%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2.5">
                                        <div class="bg-green-500 h-2.5 rounded-full" style="width: 100%"></div>
                                    </div>
                                </div>
                                <!-- Phase 2 -->
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-black">Framing</span>
                                        <span class="font-medium text-primary">75%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2.5">
                                        <div class="bg-blue-500 h-2.5 rounded-full" style="width: 75%"></div>
                                    </div>
                                </div>
                                <!-- Phase 3 -->
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-black">Electrical & Plumbing</span>
                                        <span class="font-medium text-primary">30%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2.5">
                                        <div class="bg-yellow-500 h-2.5 rounded-full" style="width: 30%"></div>
                                    </div>
                                </div>
                                <!-- Phase 4 -->
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-black">Interior Finishes</span>
                                        <span class="font-medium text-primary">0%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2.5">
                                        <div class="bg-primary h-2.5 rounded-full" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity Table -->
                        <div class="col-span-1 rounded-sm border border-slate-200 bg-white px-5 pt-6 pb-2.5 shadow-default sm:px-7.5 xl:pb-1">
                            <h4 class="mb-6 text-xl font-semibold text-black">Recent Activity</h4>

                            <div class="flex flex-col">
                                <div class="grid grid-cols-3 rounded-sm bg-slate-100 sm:grid-cols-4">
                                    <div class="p-2.5 xl:p-5">
                                        <h5 class="text-sm font-medium uppercase xsm:text-base">Event</h5>
                                    </div>
                                    <div class="p-2.5 text-center xl:p-5">
                                        <h5 class="text-sm font-medium uppercase xsm:text-base">Date</h5>
                                    </div>
                                    <div class="hidden p-2.5 text-center sm:block xl:p-5">
                                        <h5 class="text-sm font-medium uppercase xsm:text-base">Status</h5>
                                    </div>
                                </div>

                                <?php foreach($recent_activities as $activity): ?>
                                <div class="grid grid-cols-3 border-b border-slate-200 sm:grid-cols-4">
                                    <div class="flex items-center gap-3 p-2.5 xl:p-5">
                                        <p class="hidden text-black sm:block"><?php echo htmlspecialchars($activity['event']); ?></p>
                                    </div>
                                    <div class="flex items-center justify-center p-2.5 xl:p-5">
                                        <p class="text-black"><?php echo htmlspecialchars($activity['date']); ?></p>
                                    </div>
                                    <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                                        <?php
                                            $status_class = 'text-blue-500'; // Default
                                            if ($activity['status'] === 'Completed') $status_class = 'text-green-500';
                                            if ($activity['status'] === 'Pending') $status_class = 'text-yellow-500';
                                            if ($activity['status'] === 'In Review') $status_class = 'text-orange-500';
                                        ?>
                                        <p class="<?php echo $status_class; ?> font-medium"><?php echo htmlspecialchars($activity['status']); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>

                            </div>
                        </div>

                    </div>

                    <?php elseif ($page === 'rfis'): ?>
                        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="text-2xl font-bold text-black">Requests for Information (RFIs)</h2>
                            <a href="?page=create_rfi" class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90">
                                Create RFI
                            </a>
                        </div>

                        <!-- Search Form -->
                        <div class="mb-6 rounded-sm border border-stroke bg-white shadow-default">
                            <div class="p-4 md:p-6">
                                <form action="index.php" method="GET" class="flex items-center gap-3">
                                    <input type="hidden" name="page" value="rfis">
                                    <div class="relative w-full md:w-1/2">
                                        <button class="absolute left-4 top-1/2 -translate-y-1/2">
                                            <svg class="fill-body hover:fill-primary" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.16666 3.33332C5.945 3.33332 3.33332 5.945 3.33332 9.16666C3.33332 12.3883 5.945 15 9.16666 15C12.3883 15 15 12.3883 15 9.16666C15 5.945 12.3883 3.33332 9.16666 3.33332ZM1.66666 9.16666C1.66666 5.02452 5.02452 1.66666 9.16666 1.66666C13.3088 1.66666 16.6667 5.02452 16.6667 9.16666C16.6667 11.0028 15.9922 12.6841 14.8876 13.9875L18.0893 17.1892C18.4147 17.5147 18.4147 18.0423 18.0893 18.3677C17.7638 18.6932 17.2362 18.6932 16.9107 18.3677L13.709 15.166C12.4056 16.2706 10.7243 16.9451 8.8882 16.9451C4.74606 16.9451 1.3882 13.5872 1.3882 9.4451C1.3882 5.30296 4.74606 1.9451 8.8882 1.9451C13.0303 1.9451 16.3882 5.30296 16.3882 9.4451C16.3882 13.5872 13.0303 16.9451 8.8882 16.9451Z" fill=""></path>
                                            </svg>
                                        </button>
                                        <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search RFIs..." class="w-full rounded-md border border-stroke bg-transparent py-2 pl-12 pr-4 font-medium outline-none focus:border-primary focus-visible:shadow-none">
                                    </div>
                                    <button type="submit" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">Search</button>
                                </form>
                            </div>
                        </div>

                        <!-- TailAdmin Table Two -->
                        <div class="rounded-sm border border-stroke bg-white shadow-default">
                            <div class="py-6 px-4 md:px-6 xl:px-7.5">
                                <h4 class="text-xl font-bold text-black">RFI List</h4>
                            </div>

                            <div class="grid grid-cols-6 border-t border-stroke py-4.5 px-4 sm:grid-cols-8 md:px-6 2xl:px-7.5">
                                <div class="col-span-1 flex items-center">
                                    <p class="font-medium">Ref #</p>
                                </div>
                                <div class="col-span-3 flex items-center">
                                    <p class="font-medium">Subject</p>
                                </div>
                                <div class="col-span-2 hidden items-center sm:flex">
                                    <p class="font-medium">Priority</p>
                                </div>
                                <div class="col-span-1 flex items-center">
                                    <p class="font-medium">Due Date</p>
                                </div>
                                <div class="col-span-1 flex items-center">
                                    <p class="font-medium">Status</p>
                                </div>
                            </div>

                            <?php if (empty($rfis)): ?>
                            <div class="py-4.5 px-4 md:px-6 2xl:px-7.5">
                                <p class="text-slate-500 text-center py-4">No RFIs found.</p>
                            </div>
                            <?php else: ?>
                                <?php foreach ($rfis as $rfi): ?>
                                <div class="grid grid-cols-6 border-t border-stroke py-4.5 px-4 sm:grid-cols-8 md:px-6 2xl:px-7.5">
                                    <div class="col-span-1 flex items-center">
                                        <p class="text-sm text-black">#<?php echo htmlspecialchars($rfi['ref_number']); ?></p>
                                    </div>
                                    <div class="col-span-3 flex items-center">
                                        <p class="text-sm text-black"><?php echo htmlspecialchars($rfi['subject']); ?></p>
                                    </div>
                                    <div class="col-span-2 hidden items-center sm:flex">
                                        <p class="text-sm text-black"><?php echo htmlspecialchars($rfi['priority']); ?></p>
                                    </div>
                                    <div class="col-span-1 flex items-center">
                                        <p class="text-sm text-black"><?php echo htmlspecialchars($rfi['due_date']); ?></p>
                                    </div>
                                    <div class="col-span-1 flex items-center">
                                        <?php if ($rfi['status'] === 'Closed'): ?>
                                            <p class="inline-flex rounded-full bg-success bg-opacity-10 py-1 px-3 text-sm font-medium text-success">Closed</p>
                                        <?php else: ?>
                                            <p class="inline-flex rounded-full bg-warning bg-opacity-10 py-1 px-3 text-sm font-medium text-warning">Open</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                    <?php elseif ($page === 'create_rfi'): ?>
                        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="text-2xl font-bold text-black">Create New RFI</h2>
                        </div>

                        <div class="rounded-sm border border-stroke bg-white shadow-default">
                            <div class="border-b border-stroke py-4 px-6.5">
                                <h3 class="font-medium text-black">
                                    RFI Details
                                </h3>
                            </div>
                            <form action="index.php" method="POST">
                                <input type="hidden" name="action" value="create_rfi">
                                <div class="p-6.5">
                                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                                        <div class="w-full xl:w-1/2">
                                            <label class="mb-2.5 block text-black">Reference Number <span class="text-danger">*</span></label>
                                            <input type="text" name="ref_number" required placeholder="e.g., RFI-001" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter">
                                        </div>

                                        <div class="w-full xl:w-1/2">
                                            <label class="mb-2.5 block text-black">Due Date <span class="text-danger">*</span></label>
                                            <div class="relative">
                                                <input type="date" name="due_date" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4.5">
                                        <label class="mb-2.5 block text-black">Subject <span class="text-danger">*</span></label>
                                        <input type="text" name="subject" required placeholder="Brief description of the issue" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter">
                                    </div>

                                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                                        <div class="w-full xl:w-1/2">
                                            <label class="mb-2.5 block text-black">Status</label>
                                            <div class="relative z-20 bg-transparent dark:bg-form-input">
                                                <select name="status" class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-3 px-5 outline-none transition focus:border-primary active:border-primary">
                                                    <option value="Open">Open</option>
                                                    <option value="Closed">Closed</option>
                                                </select>
                                                <span class="absolute top-1/2 right-4 z-30 -translate-y-1/2">
                                                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g opacity="0.8">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z" fill=""></path>
                                                        </g>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="w-full xl:w-1/2">
                                            <label class="mb-2.5 block text-black">Priority</label>
                                            <div class="relative z-20 bg-transparent dark:bg-form-input">
                                                <select name="priority" class="relative z-20 w-full appearance-none rounded border border-stroke bg-transparent py-3 px-5 outline-none transition focus:border-primary active:border-primary">
                                                    <option value="Low">Low</option>
                                                    <option value="Medium" selected>Medium</option>
                                                    <option value="High">High</option>
                                                </select>
                                                <span class="absolute top-1/2 right-4 z-30 -translate-y-1/2">
                                                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g opacity="0.8">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.29289 8.29289C5.68342 7.90237 6.31658 7.90237 6.70711 8.29289L12 13.5858L17.2929 8.29289C17.6834 7.90237 18.3166 7.90237 18.7071 8.29289C19.0976 8.68342 19.0976 9.31658 18.7071 9.70711L12.7071 15.7071C12.3166 16.0976 11.6834 16.0976 11.2929 15.7071L5.29289 9.70711C4.90237 9.31658 4.90237 8.68342 5.29289 8.29289Z" fill=""></path>
                                                        </g>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray hover:bg-opacity-90 text-white">
                                        Submit RFI
                                    </button>
                                </div>
                            </form>
                        </div>

                    <?php elseif ($page === 'budget'): ?>
                        <h2 class="text-2xl font-bold text-black mb-4">Budget</h2>
                        <div class="rounded-sm border border-slate-200 bg-white p-6 shadow-default">
                            <p class="text-slate-600">Budget management content will be displayed here.</p>
                        </div>

                    <?php else: ?>
                        <h2 class="text-2xl font-bold text-black mb-4">Page Not Found</h2>
                        <div class="rounded-sm border border-slate-200 bg-white p-6 shadow-default">
                            <p class="text-slate-600">The requested page does not exist.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </main>
        </div>
    </div>
</body>
</html>
