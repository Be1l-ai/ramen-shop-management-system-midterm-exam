<?php 
require_once __DIR__ . '/../../src/bootstrap.php';

$user = requireAuth($pdo);

if (!isAdmin($pdo)) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$tableName = $_GET['table'] ?? null;
$recordId = isset($_GET['record_id']) ? $_GET['record_id'] : null;

$logs = getAuditLogs($pdo, $tableName, $recordId, 100);

$tables = ['ramen_shops', 'menu_items', 'users'];

$navBase = '../';
$navActive = 'audit';
$navUser = $user;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - Ramen Shop Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/nav.php'; ?>
    
    <div class="container">
        <h1>Audit Logs</h1>
        
        <form method="GET" class="filters">
            <div>
                <label>Table: 
                    <select name="table">
                        <option value="">All Tables</option>
                        <?php foreach ($tables as $t): ?>
                            <option value="<?php echo $t; ?>" <?php echo $tableName === $t ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_', ' ', $t)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div>
                <label>Record ID: 
                    <input type="number" name="record_id" value="<?php echo $recordId ?? ''; ?>" placeholder="Filter by record ID">
                </label>
            </div>
            <div>
                <input type="submit" value="Filter" class="btn">
                <a href="index.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
        
        <?php if (empty($logs)): ?>
            <p class="empty">No audit logs found.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Table</th>
                    <th>Record ID</th>
                    <th>Old Values</th>
                    <th>New Values</th>
                    <th>Timestamp</th>
                </tr>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo $log['id']; ?></td>
                    <td><?php echo $log['username']; ?></td>
                    <td class="action-<?php echo $log['action']; ?>"><?php echo ucfirst($log['action']); ?></td>
                    <td><?php echo ucfirst(str_replace('_', ' ', $log['table_name'])); ?></td>
                    <td><?php echo $log['record_id']; ?></td>
                    <td><pre class="json"><?php echo $log['old_values'] ? json_encode(json_decode($log['old_values']), JSON_PRETTY_PRINT) : '-'; ?></pre></td>
                    <td><pre class="json"><?php echo $log['new_values'] ? json_encode(json_decode($log['new_values']), JSON_PRETTY_PRINT) : '-'; ?></pre></td>
                    <td><?php echo $log['created_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>