<?php
require_once 'config.php';
requireLogin();

$page = 'dashboard';

// Fetch dashboard stats
$stats = apiRequest('/api/v1/admin/dashboard');
?>

<?php include 'header.php'; ?>

<div class="page-header">
    <h2>Dashboard</h2>
</div>

<?php if (isset($stats['error'])): ?>
    <div class="alert alert-error">
        <strong>Error:</strong> <?php echo htmlspecialchars($stats['error']); ?>
        <br><small>HTTP Code: <?php echo $stats['http_code']; ?></small>
        <br><small>Make sure API_BASE_URL and ADMIN_API_KEY are configured correctly in config.php</small>
    </div>
<?php else: ?>
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Organizations</h3>
            <div class="value"><?php echo formatNumber($stats['total_organizations'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="value"><?php echo formatNumber($stats['total_users'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <h3>Active API Keys</h3>
            <div class="value"><?php echo formatNumber($stats['active_api_keys'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <h3>Requests (24h)</h3>
            <div class="value"><?php echo formatNumber($stats['requests_24h'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <h3>Requests (7d)</h3>
            <div class="value"><?php echo formatNumber($stats['requests_7d'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <h3>Requests (30d)</h3>
            <div class="value"><?php echo formatNumber($stats['requests_30d'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <h3>Active Trials</h3>
            <div class="value"><?php echo formatNumber($stats['active_trials'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <h3>Expiring Soon</h3>
            <div class="value"><?php echo formatNumber($stats['expiring_trials_7d'] ?? 0); ?></div>
        </div>
    </div>

    <div class="card">
        <h3>Quick Actions</h3>
        <p style="margin-bottom: 15px;">Common administrative tasks:</p>
        <a href="trial_keys.php?action=create" class="btn btn-primary">🎟️ Create Trial Keys</a>
        <a href="organizations.php" class="btn btn-primary">🏢 View Organizations</a>
        <a href="api_keys.php" class="btn btn-primary">🔑 Manage API Keys</a>
        <a href="analytics.php" class="btn btn-primary">📈 View Analytics</a>
    </div>

    <div class="card">
        <h3>System Status</h3>
        <table>
            <tr>
                <td><strong>API Endpoint:</strong></td>
                <td><?php echo API_BASE_URL; ?></td>
            </tr>
            <tr>
                <td><strong>Admin Key Configured:</strong></td>
                <td><?php echo ADMIN_API_KEY !== 'your-admin-api-key-here' ? '✅ Yes' : '❌ No (Update config.php)'; ?></td>
            </tr>
            <tr>
                <td><strong>Session Started:</strong></td>
                <td><?php echo date('Y-m-d H:i:s', $_SESSION['login_time']); ?></td>
            </tr>
        </table>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
