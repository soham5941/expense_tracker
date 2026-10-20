<?php
session_start();

include 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$income_query = "SELECT SUM(amount) AS total_income
                 FROM transactions
                 WHERE user_id='$user_id'
                 AND type='Income'";

$expense_query = "SELECT SUM(amount) AS total_expense
                  FROM transactions
                  WHERE user_id='$user_id'
                  AND type='Expense'";

$income_result = mysqli_query($conn, $income_query);
$expense_result = mysqli_query($conn, $expense_query);

$income = mysqli_fetch_assoc($income_result)['total_income'] ?? 0;
$expense = mysqli_fetch_assoc($expense_result)['total_expense'] ?? 0;

$balance = $income - $expense;

$transactions_query = "SELECT * FROM transactions
                       WHERE user_id='$user_id'
                       ORDER BY created_at DESC";

$transactions_result = mysqli_query($conn, $transactions_query);

$pie_query = "SELECT category, SUM(amount) AS total
              FROM transactions
              WHERE user_id='$user_id'
              AND type='Expense'
              GROUP BY category";

$pie_result = mysqli_query($conn, $pie_query);

$categories = [];
$totals = [];

while($data = mysqli_fetch_assoc($pie_result)) {
    $categories[] = $data['category'];
    $totals[] = $data['total'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f6f8fb;
            color: #333;
        }

        /* Navbar Custom Styles */
        .navbar-custom {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 15px 0;
        }

        /* Metric Dashboard Cards */
        .metric-card {
            border: none;
            border-radius: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }
        .icon-shape {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        /* Section Layout Cards */
        .ui-card {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        /* Table custom look */
        .table-custom th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            background-color: #f8f9fa;
            color: #6c757d;
            border-bottom: 2px solid #edf2f9;
            padding: 14px;
        }
        .table-custom td {
            padding: 14px;
            vertical-align: middle;
            border-bottom: 1px solid #edf2f9;
        }

        /* Badges for transaction types */
        .badge-income {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
            font-weight: 500;
        }
        .badge-expense {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            font-weight: 500;
        }

        /* Layout Chart Containers */
        .chart-container {
            position: relative;
            margin: auto;
            width: 100%;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <i class="bi bi-graph-up-arrow me-2"></i> Smart Tracker
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="navbar-nav align-items-center">
                    <a href="dashboard.php" class="nav-link active px-3">Dashboard</a>
                    <a href="add_transaction.php" class="btn btn-light btn-sm mx-2 px-3 fw-medium rounded-pill shadow-sm text-primary">
                        <i class="bi bi-plus-circle-fill me-1"></i> Add Transaction
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-sm px-3 rounded-pill shadow-sm">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
                <p class="text-muted mb-0">Here is a comprehensive snapshot of your financial health.</p>
            </div>
            <span class="badge bg-white text-secondary shadow-sm px-3 py-2 mt-2 mt-sm-0 rounded-pill border">
                <i class="bi bi-calendar3 me-1"></i> Live Metrics
            </span>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="card metric-card bg-success bg-gradient text-white shadow-sm p-3">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-white-50 text-uppercase small fw-bold">Total Income</span>
                            <h2 class="fw-bold mt-1 mb-0">₹<?php echo number_format($income, 2); ?></h2>
                        </div>
                        <div class="icon-shape">
                            <i class="fa-solid fa-wallet fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card metric-card bg-danger bg-gradient text-white shadow-sm p-3">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-white-50 text-uppercase small fw-bold">Total Expense</span>
                            <h2 class="fw-bold mt-1 mb-0">₹<?php echo number_format($expense, 2); ?></h2>
                        </div>
                        <div class="icon-shape">
                            <i class="fa-solid fa-money-bill-wave fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card metric-card text-white shadow-sm p-3" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-white-50 text-uppercase small fw-bold">Remaining Balance</span>
                            <h2 class="fw-bold mt-1 mb-0">₹<?php echo number_format($balance, 2); ?></h2>
                        </div>
                        <div class="icon-shape">
                            <i class="fa-solid fa-scale-balanced fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 g-4">
            <div class="col-12 col-lg-7">
                <div class="card ui-card h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-bar-chart-line-fill text-primary fs-5 me-2"></i>
                        <h5 class="fw-bold text-dark mb-0">Financial Overview</h5>
                    </div>
                    <div class="chart-container">
                        <canvas id="financeChart" style="max-height: 280px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card ui-card h-100 p-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-pie-chart-fill text-danger fs-5 me-2"></i>
                        <h5 class="fw-bold text-dark mb-0">Expense Analysis</h5>
                    </div>
                    <div class="chart-container d-flex justify-content-center">
                        <canvas id="pieChart" style="max-height: 280px; max-width: 280px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="card ui-card mt-4 p-4">
            <div class="d-flex flex-sm-row flex-column justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-list-stars text-dark fs-5 me-2"></i>
                    <h5 class="fw-bold text-dark mb-0">Recent Transactions</h5>
                </div>
                <a href="add_transaction.php" class="btn btn-primary btn-sm px-3 rounded-3 shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Add Transaction
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($transactions_result) > 0) { ?>
                            <?php while($row = mysqli_fetch_assoc($transactions_result)) { ?>
                            <tr>
                                <td>
                                    <span class="badge rounded-pill <?php echo ($row['type'] == 'Income') ? 'badge-income' : 'badge-expense'; ?> px-2.5 py-1.5 text-uppercase">
                                        <?php echo htmlspecialchars($row['type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark"><?php echo htmlspecialchars($row['category']); ?></span>
                                </td>
                                <td>
                                    <span class="fw-bold <?php echo ($row['type'] == 'Income') ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo ($row['type'] == 'Income') ? '+' : '-'; ?> ₹<?php echo number_format($row['amount'], 2); ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?php echo htmlspecialchars($row['description'] ?: 'No details provided'); ?>
                                </td>
                                <td class="text-secondary small">
                                    <?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group rounded-3 shadow-sm overflow-hidden" role="group">
                                        <a href="edit_transaction.php?id=<?php echo $row['id']; ?>" class="btn btn-light btn-sm text-warning px-2.5" title="Edit text">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="delete_transaction.php?id=<?php echo $row['id']; ?>" class="btn btn-light btn-sm text-danger px-2.5" onclick="return confirm('Are you sure you want to delete this transaction?')" title="Delete entry">
                                            <i class="bi bi-trash3-fill"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-black-50"></i>
                                    No transactions logged yet. Click "Add Transaction" to begin!
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    var ctx = document.getElementById('financeChart').getContext('2d');
    var financeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Income', 'Expense', 'Balance'],
            datasets: [{
                label: 'Financial Breakdown',
                data: [
                    <?php echo $income; ?>,
                    <?php echo $expense; ?>,
                    <?php echo $balance; ?>
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.85)',  // Rich Emerald Green
                    'rgba(220, 53, 69, 0.85)',  // Clean Crimson Red
                    'rgba(42, 82, 152, 0.85)'   // Classic Deep Indigo Blue
                ],
                borderColor: [
                    '#198754',
                    '#dc3545',
                    '#2a5298'
                ],
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 45
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#edf2f9' }
                }
            }
        }
    });
    </script>

    <script>
    var pieCtx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(pieCtx, {
        type: 'doughnut', /* Changed to doughnut layout for premium aesthetic feel */
        data: {
            labels: <?php echo json_encode($categories); ?>,
            datasets: [{
                label: 'Expenses Breakdown',
                data: <?php echo json_encode($totals); ?>,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
                    '#858796', '#5a5c69', '#a362e6', '#f27da1', '#ff9f40'
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 15, font: { family: "'Inter', sans-serif" } }
                }
            }
        }
    });
    </script>

</body>
</html>