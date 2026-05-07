@auth
@if(auth()->user()->hasRole('guest'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Tambah Data / create buttons
    document.querySelectorAll('a[href]').forEach(function (el) {
        if (/\/create(\?[^]*)?$/.test(el.getAttribute('href') || '')) {
            el.remove();
        }
    });

    // 2. Edit links in table rows
    document.querySelectorAll('a[href]').forEach(function (el) {
        if (/\/[^/]+\/edit(\?[^]*)?$/.test(el.getAttribute('href') || '')) {
            el.remove();
        }
    });

    // 3. Delete forms (forms with _method=DELETE)
    document.querySelectorAll('form').forEach(function (form) {
        if (form.querySelector('input[name="_method"][value="DELETE"]')) {
            form.remove();
        }
    });

    // 4. Import modal triggers
    document.querySelectorAll('[data-bs-target]').forEach(function (el) {
        if ((el.getAttribute('data-bs-target') || '').toLowerCase().includes('import')) {
            el.remove();
        }
    });

    // 5. Import links
    document.querySelectorAll('a[href]').forEach(function (el) {
        if ((el.getAttribute('href') || '').toLowerCase().includes('/import')) {
            el.remove();
        }
    });

    // 6. Singkron / Sinkron buttons
    document.querySelectorAll('button, a.btn').forEach(function (el) {
        const text = el.textContent.trim().toLowerCase();
        if (text.includes('singkron') || text.includes('sinkron')) {
            var form = el.closest('form');
            (form || el).remove();
        }
    });
});
</script>
@endif
@endauth
