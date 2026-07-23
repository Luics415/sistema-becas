<?php if (!empty($flash)): ?>
<div class="alert alert-<?= e($flash['type'] ?? 'success') ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?= ($flash['type'] ?? 'success') === 'danger' ? 'exclamation-circle' : 'check-circle' ?>-fill me-2"></i>
    <?= $flash['message'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-<?= e($error['type'] ?? 'danger') ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle-fill me-2"></i>
    <?= $error['message'] ?? $error ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
