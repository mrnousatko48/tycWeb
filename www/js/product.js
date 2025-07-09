document.addEventListener('DOMContentLoaded', () => {
    // Select2 init for model form
    const colorSelect = document.querySelector('#frm-modelForm-color_ids');
    const featureSelect = document.querySelector('#frm-modelForm-feature_ids');

    if (colorSelect) {
        $(colorSelect).select2({
            placeholder: 'Select colors',
            allowClear: true,
            width: '100%'
        });
    }

    if (featureSelect) {
        $(featureSelect).select2({
            placeholder: 'Select features',
            allowClear: true,
            width: '100%'
        });
    }

    // AJAX delete + form submission
    document.querySelectorAll('form, a.ajax').forEach(el => {
        if (el.tagName === 'A') {
            el.addEventListener('click', async e => {
                e.preventDefault();
                const res = await fetch(el.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.snippets) {
                    Object.keys(data.snippets).forEach(id => {
                        document.getElementById(id).innerHTML = data.snippets[id];
                    });
                }
            });
        }

        if (el.tagName === 'FORM') {
            el.addEventListener('submit', async e => {
                e.preventDefault();
                const formData = new FormData(el);
                const res = await fetch(el.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.snippets) {
                    Object.keys(data.snippets).forEach(id => {
                        document.getElementById(id).innerHTML = data.snippets[id];
                    });
                }

                // re-init select2 after AJAX redraw
                $('#frm-modelForm-color_ids').select2({ placeholder: 'Select colors', width: '100%' });
                $('#frm-modelForm-feature_ids').select2({ placeholder: 'Select features', width: '100%' });
            });
        }
    });
});

function filterModels(manufacturerId) {
    window.location.href = `/admin/product/models?manufacturerId=${manufacturerId}`;
}
