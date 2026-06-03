<?php
    session_start();
    // --- Configuration ---
    $tasksFile = __DIR__ . '/../data/tasks.json';

     // ۱. تعریف توابع کمکی (بالای فایل یا قبل از استفاده)
    function getStatusLabel($status) {
        return ($status === 'done') ? 'انجام شده' : 'در انتظار';
    }

    function getStatusClass($status) {
        return ($status === 'done') ? 'status-done' : 'status-pending';
    }

    $defaultTasks = [
        [
            'id' => 1,
            'title' => 'کار شماره ۱',
            'status' => 'pending',
            'created_at' => '2026-05-20 10:00:00'
        ],
        [
            'id' => 2,
            'title' => 'کار شماره ۲',
            'status' => 'done',
            'created_at' => '2026-05-20 11:00:00'
        ],
        [
            'id' => 3,
            'title' => 'کار شماره ۳',
            'status' => 'pending',
            'created_at' => '2026-05-20 12:30:00'
        ]
    ];

    // --- Load Tasks ---
    $tasks = [];
    if (file_exists($tasksFile)) {
        $jsonContent = file_get_contents($tasksFile);
        $decodedTasks = json_decode($jsonContent, true); // Use true to get associative arrays
        // Check if decoding was successful and if it's an array
        if ($decodedTasks !== null && is_array($decodedTasks)) {
            $tasks = $decodedTasks;
        } else {
            // If JSON is invalid or not an array, use default tasks
            $tasks = $defaultTasks;
        }
    } else {
        // If the file doesn't exist, use default tasks
        $tasks = $defaultTasks;
    }

    // --- پیام سشن (Flash Message) را همیشه در ابتدای اسکریپت بخوان ---
    $alertMessage = $_SESSION['message'] ?? '';
    $alertType    = $_SESSION['message_type'] ?? '';

    unset($_SESSION['message'], $_SESSION['message_type']);

    // -------------- Filter Tasks -----------------------------
    $filter = $_GET['filter'] ?? 'all';
    $filteredTasks = array_filter($tasks, function($task) use ($filter) {
        if ($filter === 'done') return $task['status'] === 'done';
        if ($filter === 'pending') return $task['status'] === 'pending';
        return true; 
    });

    // --- Handle Toggle Status ---
    if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {

        $taskId = (int) $_GET['id'];

        foreach ($tasks as &$task) {
            if ($task['id'] == $taskId) {

                // تغییر وضعیت
                $task['status'] = ($task['status'] === 'done') ? 'pending' : 'done';

                // ذخیره در فایل
                file_put_contents(
                    $tasksFile,
                    json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );

                $_SESSION['message'] = 'وضعیت کار با موفقیت تغییر کرد.';
                $_SESSION['message_type'] = 'success';

                break;
            }
        }

        header("Location: index.php?filter=" . $filter);
        exit;
    }

    // --- Handle Delete ---
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {

        $taskId = (int) $_GET['id'];

        foreach ($tasks as $index => $task) {
            if ($task['id'] == $taskId) {

                unset($tasks[$index]);

                // مرتب‌سازی مجدد اندیس‌ها
                $tasks = array_values($tasks);

                file_put_contents(
                    $tasksFile,
                    json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );

                $_SESSION['message'] = 'کار با موفقیت حذف شد.';
                $_SESSION['message_type'] = 'success';

                break;
            }
        }

        header("Location: index.php?filter=" . $filter);
        exit;
    }

    $taskTitle = '';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $taskTitle = trim($_POST['task_title'] ?? '');

        if ($taskTitle === '') {
           
            // ذخیره پیام خطا در سشن
            $_SESSION['message'] = 'لطفاً عنوان کار را وارد کنید.';
            $_SESSION['message_type'] = 'error'; // برای تعیین نوع استایل

        } else {
            // ایجاد ساختار تسک جدید
            $newTask = [
                'id' => time(), // استفاده از timestamp به عنوان id ساده و یکتا
                'title' => $taskTitle,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];

            // اضافه کردن به ابتدای لیست
            array_unshift($tasks, $newTask);

            // ذخیره در فایل
            $jsonData = json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($tasksFile, $jsonData);

            $taskTitle = ''; // پاک کردن فرم پس از موفقیت
            
            // ذخیره پیام در سشن
            $_SESSION['message'] = 'کار جدید با موفقیت اضافه شد.';
            $_SESSION['message_type'] = 'success'; // برای تعیین نوع استایل

        }

            // PRG
            header("Location: index.php?status=success");
            exit;
    }

    // --- Prepare Data for View ---
    $totalTasks = count($filteredTasks);

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیست کارهای من</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">

        <h1>لیست کارهای من</h1>

        <h2>افزودن کار جدید</h2>
        
        <?php if ($alertMessage): ?>
            <div class="alert <?php echo $alertType; ?>">
                <?php echo $alertMessage; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="todo-form">
        <input type="text" name="task_title" placeholder="عنوان کار جدید..." value="<?php echo htmlspecialchars($taskTitle); ?>">
        <button type="submit">افزودن</button>
        </form>

        <h2>کارها (<?php echo $totalTasks; ?>)</h2>

        <!-- اضافه کردن دکمه‌های فیلتر -->
        <div class="filter-buttons" style="margin-bottom: 20px;">
            <a href="index.php?filter=all">همه</a> | 
            <a href="index.php?filter=done">انجام شده</a> | 
            <a href="index.php?filter=pending">در انتظار</a>
        </div>

        <div class="task-list">
            <?php if (empty($filteredTasks)): ?>
                <p>هیچ کاری بااین وضعیت یافت نشد.</p>
            <?php else: ?>
                <?php foreach ($filteredTasks as $task): ?>
                    
                    <div class="task-card">

                        <!-- عنوان کار -->
                        <div class="task-card-title">
                            <?php echo htmlspecialchars($task['title']); ?>
                        </div>

                        <!-- گروه دکمه‌ها و وضعیت (یک کانتینر برای مدیریت بهتر) -->
                        <div class="task-meta">
                            <span class="task-status <?php echo getStatusClass($task['status']); ?>">
                                <?php echo getStatusLabel($task['status']); ?>
                            </span>
                            
                            <div class="task-actions">
                                <a class="btn-toggle" href="index.php?action=toggle&id=<?php echo $task['id']; ?>&filter=<?php echo $filter; ?>">تغییر وضعیت</a>
                                <a class="btn-delete" href="index.php?action=delete&id=<?php echo $task['id']; ?>&filter=<?php echo $filter; ?>" onclick="return confirm('آیا مطمئن هستید؟');">حذف</a>
                            </div>
                        </div>

                    </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <script>
    // انتخاب تمام باکس‌های پیام (الرت)
    const alertBox = document.querySelector('.alert');
    
    if (alertBox) {
        // بعد از 1 ثانیه (1000 میلی‌ثانیه)
        setTimeout(() => {
            // اعمال افکت محو شدن با تغییر opacity
            alertBox.style.transition = "opacity 1s ease";
            alertBox.style.opacity = "0";
            
            // حذف کامل از داکیومنت بعد از تمام شدن انیمیشن
            setTimeout(() => {
                alertBox.remove();
            }, 500);
        }, 1000);
    }
    </script>

</body>
</html>