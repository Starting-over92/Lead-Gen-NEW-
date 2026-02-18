(function () {
    const hiddenInput = document.getElementById('job_titles');
    const textInput = document.getElementById('job-title-input');
    const tagsContainer = document.getElementById('tags-container');
    const dailyInput = document.getElementById('daily_limit');
    const warningEl = document.getElementById('daily-limit-warning');

    if (!hiddenInput || !textInput || !tagsContainer) {
        return;
    }

    let tags = [];

    try {
        const initial = JSON.parse(hiddenInput.value || '[]');
        if (Array.isArray(initial)) {
            tags = initial.map((tag) => String(tag).trim()).filter(Boolean);
        }
    } catch (error) {
        tags = [];
    }

    function syncTags() {
        tags = [...new Set(tags.map((tag) => tag.trim()).filter(Boolean))];
        hiddenInput.value = JSON.stringify(tags);
        tagsContainer.innerHTML = '';

        tags.forEach((tag, index) => {
            const badge = document.createElement('span');
            badge.className = 'tag';
            badge.textContent = tag;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'tag-remove';
            removeButton.setAttribute('aria-label', `Remove ${tag}`);
            removeButton.textContent = '×';
            removeButton.addEventListener('click', () => {
                tags.splice(index, 1);
                syncTags();
            });

            badge.appendChild(removeButton);
            tagsContainer.appendChild(badge);
        });
    }

    function addTag(value) {
        const clean = value.trim().replace(/,$/, '');
        if (!clean) return;
        tags.push(clean);
        syncTags();
    }

    textInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ',') {
            event.preventDefault();
            addTag(textInput.value);
            textInput.value = '';
        }
    });

    textInput.addEventListener('blur', () => {
        if (textInput.value.trim()) {
            addTag(textInput.value);
            textInput.value = '';
        }
    });

    function validateDailyLimit() {
        if (!dailyInput || !warningEl) return;

        const value = Number(dailyInput.value || 0);
        if (value > 200) {
            warningEl.classList.remove('hidden');
        } else {
            warningEl.classList.add('hidden');
        }
    }

    if (dailyInput) {
        dailyInput.addEventListener('input', validateDailyLimit);
        validateDailyLimit();
    }

    syncTags();
})();
