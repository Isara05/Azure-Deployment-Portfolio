<?php
session_start();

// Create task storage for the current session
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Add a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {

    $taskName = trim($_POST['task_name'] ?? '');
    $priority = $_POST['priority'] ?? '';

    if ($taskName !== '' && in_array($priority, ['High', 'Medium', 'Low'])) {

        $_SESSION['tasks'][] = [
            'id' => uniqid(),
            'name' => $taskName,
            'priority' => $priority,
            'completed' => false
        ];
    }

    header("Location: index.php");
    exit;
}

// Complete a task
if (isset($_GET['complete'])) {

    foreach ($_SESSION['tasks'] as &$task) {
        if ($task['id'] === $_GET['complete']) {
            $task['completed'] = true;
            break;
        }
    }

    unset($task);

    header("Location: index.php");
    exit;
}

// Delete a task
if (isset($_GET['delete'])) {

    $_SESSION['tasks'] = array_values(
        array_filter(
            $_SESSION['tasks'],
            fn($task) => $task['id'] !== $_GET['delete']
        )
    );

    header("Location: index.php");
    exit;
}

// Read Azure environment setting
$environment = getenv('TASK_MANAGER_ENVIRONMENT');

if (!$environment) {
    $environment = 'Local PHP Development';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Isara Task Manager PHP</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fa;
            color: #212529;
        }

        .header {
            background: white;
            border-bottom: 1px solid #ddd;
            padding: 18px 40px;
            font-size: 22px;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .title {
            text-align: center;
            margin-bottom: 35px;
        }

        .title h1 {
            font-size: 42px;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #6c757d;
            font-size: 18px;
        }

        .environment {
            margin-top: 18px;
            font-weight: bold;
        }

        .environment span {
            background: #6f42c1;
            color: white;
            padding: 6px 10px;
            border-radius: 5px;
        }

        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .card h2 {
            margin-top: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr 180px;
            gap: 20px;
            align-items: end;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: #6f42c1;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 14px 10px;
            border-bottom: 1px solid #ddd;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }

        .pending {
            background: #ffc107;
            color: #212529;
        }

        .completed {
            background: #198754;
            color: white;
        }

        .priority-high {
            color: #dc3545;
            font-weight: bold;
        }

        .priority-medium {
            color: #fd7e14;
            font-weight: bold;
        }

        .priority-low {
            color: #198754;
            font-weight: bold;
        }

        .action {
            display: inline-block;
            text-decoration: none;
            color: white;
            padding: 7px 12px;
            border-radius: 4px;
            margin-right: 5px;
        }

        .complete {
            background: #198754;
        }

        .delete {
            background: #dc3545;
        }

        .empty {
            background: #cff4fc;
            padding: 18px;
            border-radius: 5px;
            color: #055160;
        }

        .footer {
            text-align: center;
            color: #6c757d;
            margin-top: 40px;
        }

        @media (max-width: 800px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="header">
    IsaraTaskManagerPHP
</div>

<div class="container">

    <div class="title">

        <h1>Isara Task Manager</h1>

        <div class="subtitle">
            PHP 8.x Cloud Edition
        </div>

        <div class="environment">
            Environment:
            <span>
                <?= htmlspecialchars($environment) ?>
            </span>
        </div>

    </div>

    <div class="card">

        <h2>Add New Task</h2>

        <form method="POST">

            <div class="form-row">

                <div>
                    <label>Task Name</label>

                    <input
                        type="text"
                        name="task_name"
                        placeholder="Enter your task"
                        required>
                </div>

                <div>
                    <label>Priority</label>

                    <select name="priority" required>
                        <option value="">Select Priority</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>

                <div>
                    <button type="submit" name="add_task">
                        Add Task
                    </button>
                </div>

            </div>

        </form>

    </div>

    <div class="card">

        <h2>My Tasks</h2>

        <?php if (empty($_SESSION['tasks'])): ?>

            <div class="empty">
                No tasks added yet. Add your first task above.
            </div>

        <?php else: ?>

            <table>

                <thead>
                <tr>
                    <th>Task</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>

                <?php foreach ($_SESSION['tasks'] as $task): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($task['name']) ?>
                        </td>

                        <td class="priority-<?= strtolower($task['priority']) ?>">
                            <?= htmlspecialchars($task['priority']) ?>
                        </td>

                        <td>

                            <?php if ($task['completed']): ?>

                                <span class="badge completed">
                                    Completed
                                </span>

                            <?php else: ?>

                                <span class="badge pending">
                                    Pending
                                </span>

                            <?php endif; ?>

                        </td>

                        <td>

                            <?php if (!$task['completed']): ?>

                                <a
                                    class="action complete"
                                    href="?complete=<?= urlencode($task['id']) ?>">
                                    Complete
                                </a>

                            <?php endif; ?>

                            <a
                                class="action delete"
                                href="?delete=<?= urlencode($task['id']) ?>">
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

    <div class="footer">
        Isara Task Manager • PHP 8.x • Azure Deployment
    </div>

</div>

</body>
</html>