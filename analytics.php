<?php
require_once 'config.php';
requireLogin();

$page = 'analytics';

$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$query_params = [
    'start_date=' . urlencode($start_date . 'T00:00:00'),
    'end_date=' . urlencode($end_date . 'T23:59:59')
];

$analytics = apiRequest('/api/v1/admin/analytics/usage?' . implode('&', $query_params));
?>

<?php include 'header.php'; ?>

<div class="page-header">
    <h2>Usage Analytics</h2>
</div>

<!-- Date Range Selector -->
<div class="card">
    <form method="GET" style="display: flex; gap: 15px; align-items: end;">
        <div class="form-group" style="margin: 0;">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
        </div>
        <div class="form-group" style="margin: 0;">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<?php if (isset($analytics['error'])): ?>
    <div class="alert alert-error">
        Error: <?php echo htmlspecialchars($analytics['error']); ?>
    </div>
<?php else: ?>
    <!-- Summary Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Requests</h3>
            <div class="value"><?php echo formatNumber($analytics['total_requests'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Tokens</h3>
            <div class="value"><?php echo formatNumber($analytics['total_tokens'] ?? 0); ?></div>
        </div>
        <div class="stat-card">
            <h3>Avg Response Time</h3>
            <div class="value"><?php echo round($analytics['avg_response_time_ms'] ?? 0); ?>ms</div>
        </div>
        <div class="stat-card">
            <h3>Error Rate</h3>
            <div class="value"><?php echo round(($analytics['error_rate'] ?? 0) * 100, 2); ?>%</div>
        </div>
    </div>

    <!-- Top Endpoints -->
    <div class="card">
        <h3>Top Endpoints</h3>
        <?php if (!empty($analytics['top_endpoints'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Endpoint</th>
                        <th>Requests</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = $analytics['total_requests'];
                    foreach ($analytics['top_endpoints'] as $endpoint):
                        $percentage = $total > 0 ? ($endpoint['count'] / $total) * 100 : 0;
                    ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($endpoint['endpoint']); ?></code></td>
                        <td><?php echo formatNumber($endpoint['count']); ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="flex: 1; background: #e1e4e8; height: 20px; border-radius: 10px; overflow: hidden;">
                                    <div style="background: #667eea; height: 100%; width: <?php echo min(100, $percentage); ?>%;"></div>
                                </div>
                                <span><?php echo round($percentage, 1); ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #666; padding: 20px; text-align: center;">No data available</p>
        <?php endif; ?>
    </div>

    <!-- Top Organizations -->
    <div class="card">
        <h3>Top Organizations by Usage</h3>
        <?php if (!empty($analytics['top_organizations'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Organization</th>
                        <th>Requests</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = $analytics['total_requests'];
                    foreach ($analytics['top_organizations'] as $org):
                        $percentage = $total > 0 ? ($org['count'] / $total) * 100 : 0;
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($org['organization']); ?></strong></td>
                        <td><?php echo formatNumber($org['count']); ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="flex: 1; background: #e1e4e8; height: 20px; border-radius: 10px; overflow: hidden;">
                                    <div style="background: #28a745; height: 100%; width: <?php echo min(100, $percentage); ?>%;"></div>
                                </div>
                                <span><?php echo round($percentage, 1); ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #666; padding: 20px; text-align: center;">No data available</p>
        <?php endif; ?>
    </div>

    <!-- Requests Timeline -->
    <div class="card">
        <h3>Requests Timeline</h3>
        <?php if (!empty($analytics['requests_by_day'])): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Requests</th>
                        <th>Chart</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $max_count = max(array_column($analytics['requests_by_day'], 'count'));
                    foreach ($analytics['requests_by_day'] as $day):
                        $percentage = $max_count > 0 ? ($day['count'] / $max_count) * 100 : 0;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($day['date']); ?></td>
                        <td><?php echo formatNumber($day['count']); ?></td>
                        <td>
                            <div style="background: #e1e4e8; height: 20px; border-radius: 10px; overflow: hidden;">
                                <div style="background: #667eea; height: 100%; width: <?php echo $percentage; ?>%;"></div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #666; padding: 20px; text-align: center;">No data available</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>
