<?php
require_once 'config.php';
requireLogin();

$page = 'users';

$search = $_GET['search'] ?? '';
$query_string = $search ? '?search=' . urlencode($search) : '';
$users = apiRequest('/api/v1/admin/users' . $query_string);
?>

<?php include 'header.php'; ?>

<div class="page-header">
    <h2>Users</h2>
</div>

<!-- Search -->
<div class="card">
    <form method="GET" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="margin: 0; flex: 1;">
            <label for="search">Search</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Email or name...">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="users.php" class="btn" style="background: #ddd;">Clear</a>
    </form>
</div>

<!-- Users List -->
<div class="card">
    <h3>Users (<?php echo is_array($users) ? count($users) : 0; ?>)</h3>

    <?php if (isset($users['error'])): ?>
        <div class="alert alert-error">
            Error: <?php echo htmlspecialchars($users['error']); ?>
        </div>
    <?php elseif (empty($users)): ?>
        <p style="color: #666; padding: 20px; text-align: center;">No users found</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Organization</th>
                    <th>Active</th>
                    <th>Verified</th>
                    <th>Created</th>
                    <th>Last Login</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                        <?php if ($user['full_name']): ?>
                            <br><small style="color: #666;"><?php echo htmlspecialchars($user['full_name']); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($user['organization_name']); ?></td>
                    <td>
                        <?php if ($user['is_active']): ?>
                            <span class="badge badge-success">Yes</span>
                        <?php else: ?>
                            <span class="badge badge-danger">No</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['is_verified']): ?>
                            <span class="badge badge-success">Yes</span>
                        <?php else: ?>
                            <span class="badge badge-warning">No</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo formatDate($user['created_at']); ?></td>
                    <td>
                        <?php echo $user['last_login_at'] ? formatDate($user['last_login_at']) : '<span style="color: #999;">Never</span>'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
