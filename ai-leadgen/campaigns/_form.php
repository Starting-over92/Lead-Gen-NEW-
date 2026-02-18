<?php
/** @var array $formData */
/** @var array $errors */
/** @var string $submitLabel */
?>
<form method="post" class="card form-card" novalidate>
    <?= csrfField(); ?>
    <div class="grid-2">
        <div class="form-group">
            <label for="niche">Target Niche *</label>
            <input type="text" id="niche" name="niche" maxlength="255" value="<?= e($formData['niche'] ?? '') ?>" required>
            <?php if (!empty($errors['niche'])): ?><p class="error"><?= e($errors['niche']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="country">Country *</label>
            <input type="text" id="country" name="country" maxlength="100" value="<?= e($formData['country'] ?? '') ?>" required>
            <?php if (!empty($errors['country'])): ?><p class="error"><?= e($errors['country']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="city">City (optional)</label>
            <input type="text" id="city" name="city" maxlength="100" value="<?= e($formData['city'] ?? '') ?>">
            <?php if (!empty($errors['city'])): ?><p class="error"><?= e($errors['city']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="company_size">Company Size *</label>
            <select id="company_size" name="company_size" required>
                <option value="">Select company size</option>
                <?php foreach (['1-10', '11-50', '51-200', '201-500', '501-1000', '1000+'] as $size): ?>
                    <option value="<?= e($size) ?>" <?= ($formData['company_size'] ?? '') === $size ? 'selected' : '' ?>><?= e($size) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['company_size'])): ?><p class="error"><?= e($errors['company_size']) ?></p><?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="job-title-input">Job Titles *</label>
        <div class="tag-input-wrapper" data-tag-input>
            <div class="tags" id="tags-container"></div>
            <input type="text" id="job-title-input" placeholder="Type title and press Enter or comma">
            <input type="hidden" name="job_titles" id="job_titles" value='<?= e(json_encode($formData['job_titles'] ?? [], JSON_UNESCAPED_UNICODE)) ?>'>
        </div>
        <?php if (!empty($errors['job_titles'])): ?><p class="error"><?= e($errors['job_titles']) ?></p><?php endif; ?>
    </div>

    <div class="grid-2">
        <div class="form-group">
            <label for="outreach_tone">Outreach Tone *</label>
            <select id="outreach_tone" name="outreach_tone" required>
                <option value="">Select tone</option>
                <?php foreach (['Formal', 'Friendly', 'Aggressive'] as $tone): ?>
                    <option value="<?= e($tone) ?>" <?= ($formData['outreach_tone'] ?? '') === $tone ? 'selected' : '' ?>><?= e($tone) ?></option>
                <?php endforeach; ?>
            </select>
            <p class="help">Formal = corporate style outreach<br>Friendly = casual and warm<br>Aggressive = salesy and urgent</p>
            <?php if (!empty($errors['outreach_tone'])): ?><p class="error"><?= e($errors['outreach_tone']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label for="daily_limit">Daily Outreach Limit *</label>
            <input type="number" id="daily_limit" name="daily_limit" min="1" max="500" value="<?= e((string)($formData['daily_limit'] ?? '')) ?>" required>
            <p id="daily-limit-warning" class="warning hidden">High volume may trigger spam filters.</p>
            <?php if (!empty($errors['daily_limit'])): ?><p class="error"><?= e($errors['daily_limit']) ?></p><?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= e($submitLabel) ?></button>
        <a href="/ai-leadgen/campaigns/index.php" class="btn btn-light">Cancel</a>
    </div>
</form>
