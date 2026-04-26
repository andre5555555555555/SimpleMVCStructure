<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse budget gaming chairs with product photos, pricing, and seller listings.">
    <meta name="theme-color" content="#111111">
    <title>Gaming Chair</title>
    <link rel="stylesheet" href="<?= rtrim($this->url, '/') ?>/styles/style.css">
</head>
<body>
    <?php $currentUser = $_SESSION['user'] ?? null; ?>
    <div class="Header">
        <a href="index.php"><h1>GAMING CHAIRS</h1></a>
        <ul class="links">
            <li>
                <form action="index.php" method="get">
                    <input type="hidden" name="url" value="search">
                    <input type="text" name="query" placeholder="Search">
                    <input type="submit" style="display:none">
                </form>
            </li>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php?url=shop">Shop</a></li>
            <?php if (!empty($currentUser) && (int) $currentUser['role_id'] === 1): ?>
                <li><a href="index.php?url=add">Upload</a></li>
            <?php elseif (!empty($currentUser) && (int) $currentUser['role_id'] === 2): ?>
                <li><a href="index.php?url=my-list">My List</a></li>
            <?php endif; ?>
            <li><a href="#Footer">About</a></li>
            <?php if (!empty($currentUser)): ?>
                <li><a href="index.php?url=logout">Logout</a></li>
            <?php else: ?>
                <li><a href="index.php?url=login">Login</a></li>
       
            <?php endif; ?>
        </ul>
    </div>
    <main id="main-content">
