<?php
require_once 'config.php';
requireLogin();

$page = 'api_keys';

$message = '';

// Handle key revocation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['revoke_key'])) {
    $key_id = $_POST['key_id'];

    $result = apiRequest("/api/v1/admin/api-keys/$key_id", 'DELETE');

    if (isset($result['error'])) {
        $message = '<div class="alert alert-error">Error: ' . htmlspecialchars($result['error']) . '</div>';
    } else {
        $message = '<div class="alert alert-success">API key revoked successfully!</div>';
    }
}

// Fetch API keys
$search = $_GET['search'] ?? '';
$environment = $_GET['environment'] ?? '';
$is_active = $_GET['is_active'] ?? '';

$query_params = [];
if ($search) $query_params[] = 'search=' . urlencode($search);
if ($environment) $query_params[] = 'environment=' . urlencode($environment);
if ($is_active !== '') $query_params[] = 'is_active=' . ($is_active === 'true' ? 'true' : 'false');

$query_string = !empty($query_params) ? '?' . implode('&', $query_params) : '';
$api_keys = apiRequest('/api/v1/admin/api-keys' . $query_string);
?>

<?php include 'header.php'; ?>

<div class="page-header">
    <h2>API Keys</h2>
</div>

<?php echo $message; ?>

<!-- Filters -->
<div class="card">
    <form method="GET" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="margin: 0;">
            <label for="search">Search</label>
            <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Key prefix or org name...">
        </div>
        <div class="form-group" style="margin: 0;">
            <label for="environment">Environment</label>
            <select id="environment" name="environment">
                <option value="">All</option>
                <option value="live" <?php echo $environment === 'live' ? 'selected' : ''; ?>>Live</option>
                <option value="test" <?php echo $environment === 'test' ? 'selected' : ''; ?>>Test</option>
            </select>
        </div>
        <div class="form-group" style="margin: 0;">
            <label for="is_active">Status</label>
            <select id="is_active" name="is_active">
                <option value="">All</option>
                <option value="true" <?php echo $is_active === 'true' ? 'selected' : ''; ?>>Active</option>
                <option value="false" <?php echo $is_active === 'false' ? 'selected' : ''; ?>>Revoked</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="api_keys.php" class="btn" style="background: #ddd;">Clear</a>
    </form>
</div>

<!-- API Keys List -->
<div class="card">
    <h3>API Keys (<?php echo is_array($api_keys) ? count($api_keys) : 0; ?>)</h3>

    <?php if (isset($api_keys['error'])): ?>
        <div class="alert alert-error">
            Error: <?php echo htmlspecialchars($api_keys['error']); ?>
        </div>
    <?php elseif (empty($api_keys)): ?>
        <p style="color: #666; padding: 20px; text-align: center;">No API keys found</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Key Prefix</th>
                    <th>Name</th>
                    <th>Organization</th>
                    <th>Environment</th>
                    <th>Last Used</th>
                    <th>Requests (7d)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($api_keys as $key): ?>
                <tr>
                    <td>
                        <code style="background: #f5f7fa; padding: 5px; border-radius: 3px;">
                            <?php echo htmlspecialchars($key['key_prefix']); ?>...
                        </code>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($key['name'] ?? 'Unnamed'); ?>
                        <?php if ($key['description']): ?>
                            <br><small style="color: #666;"><?php echo htmlspecialchars(substr($key['description'], 0, 50)); ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($key['organization_name']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $key['environment'] === 'live' ? 'success' : 'warning'; ?>">
                            <?php echo htmlspecialchars($key['environment']); ?>
                        </span>
                    </td>
                    <td>
                        <?php echo $key['last_used_at'] ? formatDate($key['last_used_at']) : '<span style="color: #999;">Never</span>'; ?>
                    </td>
                    <td><?php echo formatNumber($key['requests_last_7d']); ?></td>
                    <td>
                        <?php if ($key['is_active']): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Revoked</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($key['is_active']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="key_id" value="<?php echo htmlspecialchars($key['id']); ?>">
                                <button type="submit" name="revoke_key" class="btn btn-danger btn-sm" onclick="return confirm('Revoke this API key? This cannot be undone.')">
                                    Revoke
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
