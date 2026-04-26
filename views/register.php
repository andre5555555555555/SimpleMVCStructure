<?php include __DIR__ . "/header.php"; ?>

<h1 class="page-title">Create Account</h1>

<form class="auth-card" action="index.php?url=storeUser" method="POST" data-validate-form="register" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($oldInput['username'] ?? '') ?>" minlength="3" maxlength="50" data-label="Username" required>

    <label>Role</label>
    <select name="role_id" data-label="Role" required>
        <option value="">Select role</option>
        <?php foreach (($roles ?? []) as $roleId => $roleName): ?>
            <option value="<?= $roleId ?>" <?= (string) ($roleId) === (string) ($oldInput['role_id'] ?? '') ? 'selected' : '' ?>>
                <?= htmlspecialchars($roleName) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Password</label>
    <input type="password" name="password" minlength="6" maxlength="100" data-label="Password" required>

    <label>Confirm Password</label>
    <input type="password" name="confirm_password" minlength="6" maxlength="100" data-label="Confirm Password" required>

    <?php if (!empty($registerError)): ?>
        <p class="auth-message auth-message-error"><?= htmlspecialchars($registerError) ?></p>
    <?php endif; ?>

    <p class="auth-message auth-message-error validation-message" hidden></p>

    <button type="submit">Create Account</button>

    <p class="auth-links"><a href="index.php?url=login">Back to login</a></p>
</form>

<?php include __DIR__ . "/footer.php"; ?>
