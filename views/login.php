<?php include __DIR__ . "/header.php"; ?>

<h1 class="page-title">Login</h1>

<form class="auth-card" action="index.php?url=authenticate" method="POST" data-validate-form="login" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($oldInput['username'] ?? '') ?>" minlength="3" maxlength="50" data-label="Username" required>

    <label>Password</label>
    <input type="password" name="password" minlength="6" maxlength="100" data-label="Password" required>

    <?php if (!empty($authError)): ?>
        <p class="auth-message auth-message-error"><?= htmlspecialchars($authError) ?></p>
    <?php endif; ?>

    <?php if (!empty($authSuccess)): ?>
        <p class="auth-message auth-message-success"><?= htmlspecialchars($authSuccess) ?></p>
    <?php endif; ?>

    <p class="auth-message auth-message-error validation-message" hidden></p>

    <button type="submit">Login</button>

    <p class="auth-links"><a href="index.php?url=register">Create an account</a></p>
</form>

<?php include __DIR__ . "/footer.php"; ?>
