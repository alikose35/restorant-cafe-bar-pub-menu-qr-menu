(() => {
    const alertBox = document.getElementById('ajax-alert');
    if (!alertBox) return;

    const showAlert = (message, isError = false) => {
        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        alertBox.classList.add(isError ? 'alert-danger' : 'alert-success');
        alertBox.textContent = message;
    };

    const applyFragments = (fragments) => {
        if (!fragments) return;

        const categoryRows = document.querySelector('.js-category-rows');
        const productRows = document.querySelector('.js-product-rows');
        const categorySelect = document.querySelector('.js-category-select');
        const discountRows = document.querySelector('.js-discount-rows');
        const discountTarget = document.querySelector('.js-discount-target');
        const sortableBlocks = document.querySelector('.js-sortable-blocks');

        if (categoryRows && fragments.categoryRows) {
            categoryRows.innerHTML = fragments.categoryRows;
        }
        if (productRows && fragments.productRows) {
            productRows.innerHTML = fragments.productRows;
        }
        if (categorySelect && fragments.categoryOptions) {
            categorySelect.innerHTML = fragments.categoryOptions;
        }
        if (discountRows && fragments.discountRows) {
            discountRows.innerHTML = fragments.discountRows;
        }
        if (discountTarget && fragments.discountTargetOptions) {
            discountTarget.innerHTML = fragments.discountTargetOptions;
        }
        if (sortableBlocks && fragments.sortableBlocks) {
            sortableBlocks.innerHTML = fragments.sortableBlocks;
            initSortables();
        }
        bindDiscountForm();
    };

    const submitAjax = async (form) => {
        const confirmText = form.getAttribute('data-confirm');
        if (confirmText && !window.confirm(confirmText)) {
            return;
        }

        const submitButton = form.querySelector('button[type="submit"], button:not([type])');
        if (submitButton) {
            submitButton.disabled = true;
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const payload = await response.json();
            if (!response.ok || !payload.ok) {
                showAlert(payload.message || 'Islem basarisiz.', true);
                return;
            }

            showAlert(payload.message || 'Islem basarili.');
            applyFragments(payload.fragments);

            if (!form.action.includes('/settings/save')) {
                form.reset();
                bindDiscountForm();
            }
        } catch (_error) {
            showAlert('Sunucuya baglanirken hata olustu.', true);
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    };

    document.addEventListener('submit', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLFormElement)) return;
        if (!target.matches('form[data-ajax-form="1"]')) return;
        event.preventDefault();
        void submitAjax(target);
    });

    let dragged = null;

    function bindDiscountForm() {
        const form = document.getElementById('discount-form');
        if (!(form instanceof HTMLFormElement)) return;

        const targetSelect = form.querySelector('.js-discount-target');
        const targetTypeInput = form.querySelector('input[name="target_type"]');
        const targetIdInput = form.querySelector('input[name="target_id"]');
        const ruleType = form.querySelector('.js-rule-type');
        const dateRange = form.querySelector('.js-rule-date-range');
        const weekly = form.querySelector('.js-rule-weekly');
        if (!(targetSelect instanceof HTMLSelectElement) || !(targetTypeInput instanceof HTMLInputElement) || !(targetIdInput instanceof HTMLInputElement) || !(ruleType instanceof HTMLSelectElement)) {
            return;
        }

        const syncTarget = () => {
            const raw = targetSelect.value || '';
            const parts = raw.split(':');
            targetTypeInput.value = parts[0] || '';
            targetIdInput.value = parts[1] || '';
        };

        const syncRuleView = () => {
            const rule = ruleType.value;
            if (dateRange) {
                dateRange.classList.toggle('d-none', rule !== 'date_range');
            }
            if (weekly) {
                weekly.classList.toggle('d-none', rule !== 'weekly_time');
            }
        };

        syncTarget();
        syncRuleView();
        targetSelect.onchange = syncTarget;
        ruleType.onchange = syncRuleView;
    }

    const getDragAfterElement = (container, y) => {
        const elements = [...container.querySelectorAll('.sortable-item:not(.dragging)')];
        let closest = { offset: Number.NEGATIVE_INFINITY, element: null };

        elements.forEach((child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                closest = { offset, element: child };
            }
        });

        return closest.element;
    };

    async function saveOrder(list) {
        const ids = [...list.querySelectorAll('.sortable-item[data-id]')].map((el) => el.dataset.id);
        if (ids.length === 0) return;

        const formData = new FormData();
        formData.append('_token', list.dataset.token || '');
        ids.forEach((id) => formData.append('ids[]', id));
        if (list.dataset.categoryId) {
            formData.append('category_id', list.dataset.categoryId);
        }

        try {
            const response = await fetch(list.dataset.url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const payload = await response.json();
            if (!response.ok || !payload.ok) {
                showAlert(payload.message || 'Siralama kaydedilemedi.', true);
                return;
            }
            showAlert(payload.message || 'Siralama kaydedildi.');
            applyFragments(payload.fragments);
        } catch (_error) {
            showAlert('Siralama kaydedilirken baglanti hatasi olustu.', true);
        }
    }

    function initSortables() {
        const lists = document.querySelectorAll('.js-sortable-list[data-url]');
        lists.forEach((list) => {
            list.addEventListener('dragstart', (event) => {
                const item = event.target.closest('.sortable-item');
                if (!item) return;
                dragged = item;
                item.classList.add('dragging');
            });

            list.addEventListener('dragend', (event) => {
                const item = event.target.closest('.sortable-item');
                if (!item) return;
                item.classList.remove('dragging');
            });

            list.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (!dragged) return;
                const afterElement = getDragAfterElement(list, event.clientY);
                if (!afterElement) {
                    list.appendChild(dragged);
                } else {
                    list.insertBefore(dragged, afterElement);
                }
            });

            list.addEventListener('drop', (event) => {
                event.preventDefault();
                dragged = null;
                void saveOrder(list);
            });
        });
    }

    bindDiscountForm();
    initSortables();
})();
