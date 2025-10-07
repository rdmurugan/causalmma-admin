<?php
require_once 'config.php';
requireLogin();

$page = 'organizations';

$message = '';

// Handle organization updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_org'])) {
    $org_id = $_POST['org_id'];
    $action = $_POST['action'];

    $update_data = [];
    if ($action === 'extend_trial') {
        $update_data['extend_trial_days'] = 30;
    } elseif ($action === 'suspend') {
        $update_data['status'] = 'suspended';
    } elseif ($action === 'activate') {
        $update_data['status'] = 'active';
    }

    $result = apiRequest("/api/v1/admin/organizations/$org_id", 'PATCH', $update_data);

    if (isset($result['error'])) {
        $message = '<div class="alert alert-error">Error: ' . htmlspecialchars($result['error']) . '</div>';
    } else {
        $message = '<div class="alert alert-success">Organization updated successfully!</div>';
    }
}

// Fetch organizations
$tier_filter = $_GET['tier'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$query_params = [];
if ($tier_filter) $query_params[] = 'tier=' . urlencode($tier_filter);
if ($status_filter) $query_params[] = 'status=' . urlencode($status_filter);
if ($search) $query_params[] = 'search=' . urlencode($search);

$query_string = !empty($query_params) ? '?' . implode('&', $query_params) : '';
$organizations = apiRequest('/api/v1/admin/organizations' . $query_string);
?>

<?php include 'header.php'; ?>

<div class="page-header">
    <h2>Organizations</h2>
</div>

<?php echo $message; ?>

<!-- Filters -->
<div class="card">
    <form method="GET" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="margin: 0;">
            <label for="search">Search</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Organization name...">
        </div>
        <div class="form-group" style="margin: 0;">
            <label for="tier">Tier</label>
            <select id="tier" name="tier">
                <option value="">All</option>
                <option value="free" <?php echo $tier_filter === 'free' ? 'selected' : ''; ?>>Free</option>
                <option value="starter" <?php echo $tier_filter === 'starter' ? 'selected' : ''; ?>>Starter</option>
                <option value="pro" <?php echo $tier_filter === 'pro' ? 'selected' : ''; ?>>Pro</option>
                <option value="enterprise" <?php echo $tier_filter === 'enterprise' ? 'selected' : ''; ?>>Enterprise</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="">All</option>
                <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="suspended" <?php echo $status_filter === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="organizations.php" class="btn" style="background: #ddd;">Clear</a>
    </form>
</div>

<!-- Organizations List -->
<div class="card">
    <h3>Organizations (<?php echo is_array($organizations) ? count($organizations) : 0; ?>)</h3>

    <?php if (isset($organizations['error'])): ?>
        <div class="alert alert-error">
            Error: <?php echo htmlspecialchars($organizations['error']); ?>
        </div>
    <?php elseif (empty($organizations)): ?>
        <p style="color: #666; padding: 20px; text-align: center;">No organizations found</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Organization</th>
                    <th>Tier</th>
                    <th>Status</th>
                    <th>Users</th>
                    <th>API Keys</th>
                    <th>Requests (30d)</th>
                    <th>Trial</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($organizations as $org): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($org['name']); ?></strong>
                        <br>
                        <small style="color: #666;">
                            Created: <?php echo formatDate($org['created_at']); ?>
                        </small>
                    </td>
                    <td>
                        <span class="badge badge-info">
                            <?php echo htmlspecialchars($org['tier']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?php echo $org['status'] === 'active' ? 'success' : 'danger'; ?>">
                            <?php echo htmlspecialchars($org['status']); ?>
                        </span>
                    </td>
                    <td><?php echo formatNumber($org['total_users']); ?></td>
                    <td><?php echo formatNumber($org['total_api_keys']); ?></td>
                    <td><?php echo formatNumber($org['requests_last_30d']); ?></td>
                    <td>
                        <?php if ($org['trial_ends_at']): ?>
                            <?php
                            $trial_end = new DateTime($org['trial_ends_at']);
                            $now = new DateTime();
                            $days = $now->diff($trial_end)->days;
                            $expired = $trial_end < $now;
                            ?>
                            <?php if ($expired): ?>
                                <span class="badge badge-danger">Expired</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><?php echo $days; ?> days</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: #666;">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="org_id" value="<?php echo htmlspecialchars($org['id']); ?>">
                            <?php if ($org['status'] === 'active'): ?>
                                <button type="submit" name="update_org" value="suspend" class="btn btn-danger btn-sm" onclick="return confirm('Suspend this organization?')">
                                    Suspend
                                </button>
                            <?php else: ?>
                                <button type="submit" name="update_org" value="activate" class="btn btn-primary btn-sm">
                                    Activate
                                </button>
                            <?php endif; ?>
                            <input type="hidden" name="action" value="<?php echo $org['status'] === 'active' ? 'suspend' : 'activate'; ?>">
                        </form>
                        <?php if ($org['trial_ends_at']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="org_id" value="<?php echo htmlspecialchars($org['id']); ?>">
                                <input type="hidden" name="action" value="extend_trial">
                                <button type="submit" name="update_org" class="btn btn-sm" style="background: #28a745; color: white;">
                                    +30d
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
