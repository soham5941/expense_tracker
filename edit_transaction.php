<?php

session_start();

include 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT * FROM transactions WHERE id='$id'";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {

    $type = $_POST['type'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $update_sql = "UPDATE transactions
                   SET
                   type='$type',
                   category='$category',
                   amount='$amount',
                   description='$description'
                   WHERE id='$id'";

    if(mysqli_query($conn, $update_sql)) {

        header("Location: dashboard.php");
        exit();

    } else {

        $message = "Update Failed!";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaction - Smart Tracker</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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

        /* Edit Form Card UI */
        .ui-card {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 16px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            background-color: #fff;
            border-color: #2a5298;
            box-shadow: 0 0 0 0.25rem rgba(42, 82, 152, 0.15);
        }

        /* Warning/Update Style Button to stand out from regular add buttons */
        .btn-custom {
            background: linear-gradient(135deg, #f5af19 0%, #f12711 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(241, 39, 17, 0.25);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
                <i class="bi bi-graph-up-arrow me-2"></i> Smart Tracker
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <div class="navbar-nav align-items-center">
                    <a href="dashboard.php" class="nav-link px-3">Dashboard</a>
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

    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7">

                <div class="card ui-card p-4 p-sm-5">

                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center text-white rounded-circle mb-3 shadow-sm" style="width: 60px; height: 60px; background: linear-gradient(135deg, #f5af19 0%, #f12711 100%);">
                            <i class="bi bi-pencil-square fs-3"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">Modify Entry</h2>
                        <p class="text-muted small">Update and correct your existing ledger record details below.</p>
                    </div>

                    <?php if($message != "") { ?>
                        <div class="alert alert-danger d-flex align-items-center border-0 rounded-3 mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                <?php echo $message; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="row">
                            <div class="col-12 col-sm-6 mb-3">
                                <label class="form-label fw-medium text-secondary small">Transaction Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="Income" <?php if($row['type']=="Income") echo "selected"; ?>>
                                        Income
                                    </option>
                                    <option value="Expense" <?php if($row['type']=="Expense") echo "selected"; ?>>
                                        Expense
                                    </option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 mb-3">
                                <label class="form-label fw-medium text-secondary small">Category Name</label>
                                <input type="text"
                                       name="category"
                                       class="form-control"
                                       value="<?php echo htmlspecialchars($row['category']); ?>"
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium text-secondary small">Amount (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text border-end-0 bg-light text-secondary" style="border-radius: 8px 0 0 8px;">₹</span>
                                <input type="number"
                                       name="amount"
                                       class="form-control border-start-0"
                                       value="<?php echo htmlspecialchars($row['amount']); ?>"
                                       step="any"
                                       required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium text-secondary small">Description Notes</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="3"><?php echo htmlspecialchars($row['description']); ?></textarea>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2 mt-2">
                            <a href="dashboard.php" class="btn btn-light border py-2.5 px-4 rounded-3 order-2 order-sm-1 fw-medium text-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-custom flex-grow-1 text-white order-1 order-sm-2">
                                <i class="bi bi-file-earmark-check me-1"></i> Save Changes
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>