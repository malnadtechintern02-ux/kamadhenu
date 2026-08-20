<?php
/**
 * Admin Dashboard
 * Kamadenu Goushala
 */

require_once __DIR__ . '/includes/admin-auth.php';

$pageTitle = 'Dashboard';

// Aggregate stats
$totalCows = dbCount('cows');
$totalBreeds = dbCount('breeds');
$totalDonations = dbCount('donations', "payment_status = 'Completed'");
$sumDonations = dbFetchOne("SELECT COALESCE(SUM(amount), 0) as total FROM donations WHERE payment_status = 'Completed'")['total'];
$totalAdoptions = dbCount('adoptions', "status = 'Active'");
$unreadMessages = dbCount('contact_messages', 'is_read = 0');
$totalNews = dbCount('news');
$totalEvents = dbCount('events', "status = 'Upcoming'");

// Recent transactions & messages
$recentDonations = dbFetchAll("SELECT * FROM donations ORDER BY created_at DESC LIMIT 5");
$recentAdoptions = dbFetchAll("SELECT a.*, c.name as cow_name FROM adoptions a LEFT JOIN cows c ON a.cow_id = c.id ORDER BY a.created_at DESC LIMIT 5");
$recentMessages = dbFetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");

require_once __DIR__ . '/includes/admin-header.php';
require_once __DIR__ . '/includes/admin-sidebar.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="h4 mb-0 fw-bold">Overview &amp; Analytics</h2>
        <p class="text-muted small mb-0">Welcome to the Kamadenu Goushala administration dashboard.</p>
    </div>
    <div>
        <a href="<?= SITE_URL ?>/" target="_blank" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-box-arrow-up-right me-1"></i> Live Website
        </a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-admin">
            <div>
                <div class="text-muted small fw-semibold text-uppercase">Total Cows</div>
                <div class="fs-4 fw-bold mt-1 text-dark"><?= $totalCows ?></div>
                <span class="badge bg-success-subtle text-success small mt-1"><?= $totalBreeds ?> Breeds</span>
            </div>
            <div class="stat-icon-wrapper bg-success-subtle text-success">
                <i class="bi bi-heart-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-admin">
            <div>
                <div class="text-muted small fw-semibold text-uppercase">Donations Received</div>
                <div class="fs-4 fw-bold mt-1 text-dark"><?= formatCurrency((float)$sumDonations) ?></div>
                <span class="badge bg-primary-subtle text-primary small mt-1"><?= $totalDonations ?> Transactions</span>
            </div>
            <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                <i class="bi bi-cash-coin"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-admin">
            <div>
                <div class="text-muted small fw-semibold text-uppercase">Active Adoptions</div>
                <div class="fs-4 fw-bold mt-1 text-dark"><?= $totalAdoptions ?></div>
                <span class="badge bg-warning-subtle text-warning small mt-1">Gau Guardians</span>
            </div>
            <div class="stat-icon-wrapper bg-warning-subtle text-warning">
                <i class="bi bi-house-heart"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card-admin">
            <div>
                <div class="text-muted small fw-semibold text-uppercase">Unread Inquiries</div>
                <div class="fs-4 fw-bold mt-1 text-dark"><?= $unreadMessages ?></div>
                <span class="badge bg-danger-subtle text-danger small mt-1">Pending Action</span>
            </div>
            <div class="stat-icon-wrapper bg-danger-subtle text-danger">
                <i class="bi bi-envelope-fill"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Donations -->
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cash-coin me-2 text-primary"></i>Recent Donations</h6>
                <a href="<?= ADMIN_URL ?>/donations.php" class="small text-decoration-none">View All &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Amount</th>
                            <th>Seva</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentDonations)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No donations recorded yet.</td></tr>
                        <?php else: foreach ($recentDonations as $d): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= e($d['donor_name']) ?></div>
                                <small class="text-muted"><?= formatDate($d['created_at']) ?></small>
                            </td>
                            <td class="fw-bold text-success"><?= formatCurrency((float)$d['amount']) ?></td>
                            <td><?= e($d['seva_type'] ?? 'General') ?></td>
                            <td><span class="badge <?= getStatusBadgeClass($d['payment_status']) ?>"><?= e($d['payment_status']) ?></span></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Adoptions -->
    <div class="col-lg-6">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-house-heart me-2 text-warning"></i>Recent Adoptions</h6>
                <a href="<?= ADMIN_URL ?>/adoptions.php" class="small text-decoration-none">View All &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Adopter</th>
                            <th>Cow</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentAdoptions)): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">No active adoptions recorded yet.</td></tr>
                        <?php else: foreach ($recentAdoptions as $a): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= e($a['adopter_name']) ?></div>
                                <small class="text-muted"><?= e($a['adopter_phone'] ?? '') ?></small>
                            </td>
                            <td><span class="fw-bold text-dark"><?= e($a['cow_name'] ?? 'General') ?></span></td>
                            <td><?= e($a['duration_months']) ?> Mo.</td>
                            <td><span class="badge <?= getStatusBadgeClass($a['status']) ?>"><?= e($a['status']) ?></span></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Messages -->
    <div class="col-12">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-envelope me-2 text-danger"></i>Recent Inquiries &amp; Messages</h6>
                <a href="<?= ADMIN_URL ?>/messages.php" class="small text-decoration-none">View All &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentMessages)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No inquiries received.</td></tr>
                        <?php else: foreach ($recentMessages as $m): ?>
                        <tr class="<?= $m['is_read'] ? '' : 'table-light fw-medium' ?>">
                            <td>
                                <div><?= e($m['name']) ?></div>
                                <small class="text-muted"><?= e($m['email']) ?></small>
                            </td>
                            <td><?= e(truncateText($m['subject'], 40)) ?></td>
                            <td><?= formatDate($m['created_at']) ?></td>
                            <td>
                                <?php if ($m['is_read']): ?>
                                <span class="badge bg-secondary-subtle text-secondary">Read</span>
                                <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">Unread</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= ADMIN_URL ?>/message-details.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
