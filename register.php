<?php

include 'includes/db.php';

$message = "";
$message_type = "info"; // Added to dynamically change alert styles

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password)
            VALUES ('$name', '$email', '$password')";

    try {

        mysqli_query($conn, $sql);

        header("Location: register.php?success=1");
        exit();

    } catch(mysqli_sql_exception $e) {

        $message_type = "danger"; // Error styling
        if ($e->getCode() == 1062) {
            $message = "Email already exists!";
        } else {
            $message = "Something went wrong! Please try again.";
        }

    }
}

if(isset($_GET['success'])) {
    $message = "Registration Successful!";
    $message_type = "success"; // Success styling
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Account</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* Modern vibrant gradient background */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .register-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: transform 0.2s ease;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 16px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #764ba2;
            box-shadow: 0 0 0 0.25rem rgba(118, 75, 162, 0.25);
        }

        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(118, 75, 162, 0.3);
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center py-5">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                
                <div class="card register-card p-4 p-sm-5">
                    
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-gradient text-white rounded-circle mb-3 shadow-sm" style="width: 60px; height: 60px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">Create Account</h2>
                        <p class="text-muted small">Join us today! It only takes a minute.</p>
                    </div>

                    <?php if ($message != "") { ?>
                        <div class="alert alert-<?php echo $message_type; ?> d-flex align-items-center border-0 rounded-3 mb-4" role="alert">
                            <i class="bi <?php echo $message_type == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2"></i>
                            <div>
                                <?php echo $message; ?>
                            </div>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label fw-medium text-secondary small">Full Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   placeholder="John Doe"
                                   autocomplete="off"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium text-secondary small">Email Address</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   placeholder="name@example.com"
                                   autocomplete="off"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium text-secondary small">Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   placeholder="••••••••"
                                   autocomplete="new-password"
                                   required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-custom w-100 text-white mb-3">
                            Register
                        </button>
                        
                        <div class="text-center">
                            <p class="small text-muted mb-0">Already have an account? <a href="login.php" class="text-decoration-none fw-medium" style="color: #764ba2;">Sign In</a></p>
                        </div>

                    </form>

                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>