<main id="main" class="container availability-admin">
    <div class="availability-admin-heading"><div><p class="eyebrow">TribeInn · Private admin</p><h1>Availability calendar</h1><p><?= e($unitName) ?> · Blocked nights, not bookings.</p></div>
    <?php if ($authenticated): ?><form method="post"><input type="hidden" name="csrf" value="<?= e($_SESSION['admin_csrf']) ?>"><button class="text-link" name="action" value="logout">Log out</button></form><?php endif; ?></div>
    <?php if ($error): ?><p class="availability-alert" role="alert"><?= e($error) ?></p><?php endif; ?>
    <?php if ($notice): ?><p class="availability-notice" role="status"><?= e($notice) ?></p><?php endif; ?>
    <?php if (!$authenticated): ?>
    <form method="post" class="availability-admin-form availability-login"><h2>Sign in</h2><input type="hidden" name="csrf" value="<?= e($_SESSION['admin_csrf']) ?>"><input type="hidden" name="action" value="login">
        <label>Username<input name="username" autocomplete="username" required maxlength="120"></label>
        <label>Password<input type="password" name="password" autocomplete="current-password" required maxlength="200"></label>
        <button class="button">Sign in →</button><p>Private access for managing unavailable dates.</p>
    </form>
    <?php else: ?>
    <div class="availability-admin-layout">
        <section aria-label="Calendar"><div data-calendar data-admin="true" data-today="<?= e($today) ?>" data-month="<?= e(max($_SESSION['admin_month'] ?? '', substr($today, 0, 7))) ?>" data-ranges="<?= e(json_encode($ranges)) ?>" data-start="#block-start" data-end="#block-end"></div><p class="availability-help">Select the first occupied night, then the checkout/end date. All sources appear unavailable here.</p></section>
        <form method="post" class="availability-admin-form" id="block-form">
            <h2><?= !empty($edit['id']) ? 'Edit manual block' : 'Block dates' ?></h2>
            <input type="hidden" name="csrf" value="<?= e($_SESSION['admin_csrf']) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= e((string)($edit['id'] ?? '')) ?>">
            <label>Start · first occupied night<input type="date" id="block-start" name="start" value="<?= e($edit['start_date'] ?? '') ?>" <?= $edit ? '' : 'min="' . e($today) . '"' ?> required></label>
            <label>End · checkout, not occupied<input type="date" id="block-end" name="end" value="<?= e($edit['end_date'] ?? '') ?>" min="<?= e($today) ?>" required></label>
            <label>Private reason <span>(optional)</span><textarea name="reason" maxlength="500" rows="3"><?= e($edit['reason'] ?? '') ?></textarea></label>
            <p>Reasons are visible only to admin. Overlapping blocks are allowed; removing one does not remove the others.</p>
            <button class="button" <?= $ranges === null ? 'disabled' : '' ?>><?= !empty($edit['id']) ? 'Save changes' : 'Block dates' ?> →</button>
            <?php if ($edit): ?><a class="text-link" href="<?= e(url('admin/calendar')) ?>">Cancel editing</a><?php endif; ?>
        </form>
    </div>
    <section class="availability-blocks"><h2>Upcoming manual blocks</h2>
    <?php if ($ranges !== null && !$blocks): ?><p>No upcoming manual blocks.</p><?php endif; ?>
    <?php foreach ($blocks as $block): ?><article class="availability-block"><div><h3><?= e($block['start_date']) ?> → <?= e($block['end_date']) ?></h3><p>End date is not occupied.</p><?php if ($block['reason'] !== ''): ?><p><strong>Private:</strong> <?= e($block['reason']) ?></p><?php endif; ?></div>
        <a class="text-link" href="<?= e(url('admin/calendar?edit=' . $block['id'])) ?>">Edit</a>
        <details><summary>Remove / unblock</summary><form method="post"><input type="hidden" name="csrf" value="<?= e($_SESSION['admin_csrf']) ?>"><input type="hidden" name="action" value="remove"><input type="hidden" name="id" value="<?= e((string)$block['id']) ?>"><label><input type="checkbox" name="confirm" value="yes" required> Remove this manual block?</label><button class="button">Confirm removal</button></form></details>
    </article><?php endforeach; ?></section>
    <?php endif; ?>
</main>
