<html>
<head>
    <title>Aplikasi Antrian Bank Mini</title>
    <link rel="shortcut icon" href="assets/img/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        .login-container {
            text-align: center;
        }
        .login-container h2 {
            font-size: 24px;
            color: #07aca0;
            margin-bottom: 20px;
        }
        .login-box {
            background: #fff;
            padding: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .login-box .input-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #dcdcdc;
        }
        .login-box .input-group i {
            color: #07aca0;
            margin-right: 10px;
        }
        .login-box .input-group input {
            border: none;
            outline: none;
            padding: 10px;
            flex: 1;
            font-size: 16px;
            color: #8c8c8c;
        }
        .login-box .input-group input::placeholder {
            color: #8c8c8c;
        }
        .login-box .login-btn {
            background: #07aca0;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 10px; /* Added margin-bottom for spacing */
        }
        .error-message {
            background-color: #ffebee;
            border-left: 5px solid #f44336;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            color: #b71c1c;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        .error-message:before {
            content: '\f071';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 10px;
            font-size: 18px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .error-message {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<?php

session_start();

if($_SESSION['userId']) {
    header("Location: index.php");
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<body>
    <div class="login-container">
        <h2>LOGIN</h2>
        <div class="login-box">
            <form action="login.php" method="post">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="username" id="username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="password" id="password" required>
                </div>
                <button type="submit" class="login-btn">LOGIN</button>
                
                <!-- Tampilkan pesan error di bawah tombol login -->
                <?php if (isset($error)) : ?>
                    <p class="error-message"><?php echo $error; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>


<?php
require 'config/database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Cek koneksi database
    if ($mysqli->connect_error) {
        die("Koneksi database gagal: " . $mysqli->connect_error);
    }

    $username = $mysqli->real_escape_string($_POST['username']); // Tambahkan escape string
    $password = $_POST['password'];

    try {
        // Eksekusi query dengan pengecekan
        $query = "SELECT * FROM tbl_admin WHERE nama_admin = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result && $result->num_rows === 0) {
            $_SESSION['error'] = "Username tidak ditemukan";
            header("Location: login.php");
            exit;
        }

        $user_data = $result->fetch_assoc();
        $hashed_password = $user_data['password'];

        if(!password_verify($password, $hashed_password)) {
            $_SESSION['error'] = "Username atau password salah";
            header("Location: login.php");
            exit;
        }

        $_SESSION['userId'] = $user_data['id_admin'];
        header("Location: index.php");
        exit;

        $stmt->close();
    } catch (Exception $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }

    $mysqli->close();
}
?>