<?php
require_once 'config.php';
requireLogin();

$page = 'trial_keys';

$message = '';
$created_keys = null;

// Handle trial key creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_trials'])) {
    $count = (int)($_POST['count'] ?? 10);
    $trial_days = (int)($_POST['trial_days'] ?? 14);
    $org_prefix = $_POST['org_prefix'] ?? 'Trial';
    $monthly_limit = (int)($_POST['monthly_limit'] ?? 3000);
    $rate_limit = (int)($_POST['rate_limit'] ?? 5);

    $result = apiRequest('/api/v1/admin/trial-keys', 'POST', [
        'count' => $count,
        'trial_duration_days' => $trial_days,
        'organization_prefix' => $org_prefix,
        'tier' => 'free',
        'monthly_request_limit' => $monthly_limit,
        'rate_limit_per_minute' => $rate_limit
    ]);

    if (isset($result['error'])) {
        $message = '<div class="alert alert-error">Error: ' . htmlspecialchars($result['error']) . '</div>';
    } else {
        $created_keys = $result['keys'];
        $message = '<div class="alert alert-success">Successfully created ' . $result['count'] . ' trial keys!</div>';
    }
}

// Fetch active trials
$active_trials = apiRequest('/api/v1/admin/trial-keys/active');
?>

<?php include 'header.php'; ?>

<div class="page-header">
    <h2>Trial Key Management</h2>
</div>

<?php echo $message; ?>

<!-- Create Trial Keys Form -->
<div class="card">
    <h3>Create Bulk Trial Keys</h3>
    <form method="POST">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label for="count">Number of Keys</label>
                <input type="number" id="count" name="count" value="10" min="1" max="100" required>
            </div>
            <div class="form-group">
                <label for="trial_days">Trial Duration (days)</label>
                <input type="number" id="trial_days" name="trial_days" value="14" min="1" max="90" required>
            </div>
            <div class="form-group">
                <label for="org_prefix">Organization Prefix</label>
                <input type="text" id="org_prefix" name="org_prefix" value="Trial" required>
            </div>
            <div class="form-group">
                <label for="monthly_limit">Monthly Request Limit</label>
                <input type="number" id="monthly_limit" name="monthly_limit" value="3000" required>
            </div>
            <div class="form-group">
                <label for="rate_limit">Rate Limit (per minute)</label>
                <input type="number" id="rate_limit" name="rate_limit" value="5" required>
            </div>
        </div>
        <button type="submit" name="create_trials" class="btn btn-primary">
            🎟️ Generate Trial Keys
        </button>
    </form>
</div>

<!-- Display created keys -->
<?php if ($created_keys): ?>
<div class="card">
    <h3>Newly Created Trial Keys</h3>
    <p style="margin-bottom: 15px; color: #c33; font-weight: bold;">
        ⚠️ Save these API keys now! They won't be shown again.
    </p>
    <table>
        <thead>
            <tr>
                <th>Organization</th>
                <th>API Key</th>
                <th>Key Prefix</th>
                <th>Expires</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($created_keys as $key): ?>
            <tr>
                <td><?php echo htmlspecialchars($key['organization_name']); ?></td>
                <td>
                    <code style="background: #f5f7fa; padding: 5px; border-radius: 3px; font-size: 12px;">
                        <?php echo htmlspecialchars($key['api_key']); ?>
                    </code>
                </td>
                <td><?php echo htmlspecialchars($key['key_prefix']); ?></td>
                <td><?php echo formatDate($key['expires_at']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button onclick="downloadCSV()" class="btn btn-primary" style="margin-top: 15px;">
        💾 Download as CSV
    </button>

    <script>
    function downloadCSV() {
        const keys = <?php echo json_encode($created_keys); ?>;
        let csv = 'Organization,API Key,Key Prefix,Expires\n';
        keys.forEach(key => {
            csv += `"${key.organization_name}","${key.api_key}","${key.key_prefix}","${key.expires_at}"\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'trial_keys_' + Date.now() + '.csv';
        a.click();
    }
    </script>
</div>
<?php endif; ?>

<!-- Active Trials -->
<div class="card">
    <h3>Active Trial Organizations</h3>
    <?php if (isset($active_trials['error'])): ?>
        <div class="alert alert-error">
            Error loading active trials: <?php echo htmlspecialchars($active_trials['error']); ?>
        </div>
    <?php elseif (empty($active_trials)): ?>
        <p style="color: #666; padding: 20px; text-align: center;">No active trials found</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Organization</th>
                    <th>Days Remaining</th>
                    <th>Trial Ends</th>
                    <th>API Keys</th>
                    <th>Requests</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($active_trials as $trial): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($trial['organization_name']); ?></strong>
                        <br>
                        <small style="color: #666;">ID: <?php echo htmlspecialchars(substr($trial['organization_id'], 0, 8)); ?>...</small>
                    </td>
                    <td>
                        <?php
                        $days = $trial['days_remaining'];
                        $color = $days <= 3 ? '#c33' : ($days <= 7 ? '#856404' : '#155724');
                        echo "<strong style='color: $color;'>$days days</strong>";
                        ?>
                    </td>
                    <td><?php echo formatDate($trial['trial_ends_at']); ?></td>
                    <td><?php echo formatNumber($trial['total_api_keys']); ?></td>
                    <td><?php echo formatNumber($trial['total_requests']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $trial['status'] === 'active' ? 'success' : 'warning'; ?>">
                            <?php echo htmlspecialchars($trial['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
