<style>
  .copy-toast {
    position: fixed;
    right: 1.5rem;
    bottom: 1.5rem;
    z-index: 1080;
    min-width: 220px;
    opacity: 0;
    transform: translateY(12px);
    pointer-events: none;
    transition: opacity .2s ease, transform .2s ease;
  }

  .copy-toast.show {
    opacity: 1;
    transform: translateY(0);
  }
</style>

<div id="copy-toast" class="alert alert-success shadow copy-toast" role="status" aria-live="polite"></div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.__copyFeedbackInitialized) {
      return;
    }

    window.__copyFeedbackInitialized = true;

    const toast = document.getElementById('copy-toast');

    function showToast(message, type) {
      if (!toast) {
        return;
      }

      toast.textContent = message;
      toast.classList.remove('alert-success', 'alert-danger', 'show');
      toast.classList.add(type === 'error' ? 'alert-danger' : 'alert-success');
      toast.classList.add('show');

      window.clearTimeout(showToast.timeoutId);
      showToast.timeoutId = window.setTimeout(function () {
        toast.classList.remove('show');
      }, 1800);
    }

    async function copyText(value) {
      if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(value);

        return;
      }

      const textArea = document.createElement('textarea');
      textArea.value = value;
      textArea.setAttribute('readonly', 'readonly');
      textArea.style.position = 'fixed';
      textArea.style.top = '-9999px';
      textArea.style.left = '-9999px';

      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();
      textArea.setSelectionRange(0, textArea.value.length);

      const copied = document.execCommand('copy');

      document.body.removeChild(textArea);

      if (!copied) {
        throw new Error('Clipboard API is unavailable.');
      }
    }

    document.querySelectorAll('[data-copy-text]').forEach(function (button) {
      if (button.dataset.copyBound === '1') {
        return;
      }

      button.dataset.copyBound = '1';
      button.addEventListener('click', async function () {
        const originalText = button.textContent;
        const label = button.dataset.copyLabel || 'teks';
        const outlineVariant = button.dataset.copyVariant === 'light' ? 'btn-outline-light' : 'btn-outline-secondary';

        try {
          await copyText(button.dataset.copyText || '');
          button.textContent = 'Tersalin';
          button.classList.remove('btn-outline-light', 'btn-outline-secondary');
          button.classList.add('btn-success');
          showToast(label + ' berhasil disalin.', 'success');
        } catch (error) {
          button.textContent = 'Gagal';
          button.classList.remove('btn-outline-light', 'btn-outline-secondary');
          button.classList.add('btn-danger');
          showToast(label + ' gagal disalin.', 'error');
        }

        window.setTimeout(function () {
          button.textContent = originalText;
          button.classList.remove('btn-success', 'btn-danger');
          button.classList.add(outlineVariant);
        }, 1500);
      });
    });

    document.querySelectorAll('[data-download-text]').forEach(function (button) {
      if (button.dataset.downloadBound === '1') {
        return;
      }

      button.dataset.downloadBound = '1';
      button.addEventListener('click', function () {
        const originalText = button.textContent;
        const label = button.dataset.downloadLabel || 'teks';
        const fileName = button.dataset.downloadFilename || 'token-api.txt';

        try {
          const blob = new Blob([button.dataset.downloadText || ''], { type: 'text/plain;charset=utf-8' });
          const url = window.URL.createObjectURL(blob);
          const link = document.createElement('a');

          link.href = url;
          link.download = fileName;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          window.URL.revokeObjectURL(url);

          button.textContent = 'Terunduh';
          button.classList.remove('btn-outline-primary');
          button.classList.add('btn-success');
          showToast(label + ' berhasil diunduh.', 'success');
        } catch (error) {
          button.textContent = 'Gagal';
          button.classList.remove('btn-outline-primary');
          button.classList.add('btn-danger');
          showToast(label + ' gagal diunduh.', 'error');
        }

        window.setTimeout(function () {
          button.textContent = originalText;
          button.classList.remove('btn-success', 'btn-danger');
          button.classList.add('btn-outline-primary');
        }, 1500);
      });
    });
  });
</script>