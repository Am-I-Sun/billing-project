<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';
require_once 'C:\xampp\htdocs\billing-project\config\functions.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing System | Small Business Billing</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark border-bottom" style="background-color: var(--bg-card); border-color: var(--border-color) !important;">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
        <i class="bi bi-lightning-charge-fill text-success fs-4"></i> 
        <span class="fw-bold text-white ms-1">Billing System</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="clients.php">Clients</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="products.php">Products & Services</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-success text-white ms-lg-3 px-3 rounded-pill fw-medium" href="create-invoice.php">
             <i class="bi bi-plus-lg"></i> New Invoice
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger ms-lg-2" href="logout.php">
             <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container-fluid mt-4">
<?php display_flash(); ?>
