document.addEventListener('click', async (e) => {
  const btn = e.target.closest('[data-copy]');
  if (!btn) return;

  const text = btn.getAttribute('data-copy');
  try {
    await navigator.clipboard.writeText(text);
    const old = btn.textContent;
    btn.textContent = 'Copied!';
    setTimeout(() => (btn.textContent = old), 900);
  } catch (err) {
    // Fallback: select the input next to it
    const card = btn.closest('.wf-card');
    const input = card ? card.querySelector('.linkinput') : null;
    if (input) {
      input.focus();
      input.select();
      document.execCommand('copy');
      const old = btn.textContent;
      btn.textContent = 'Copied!';
      setTimeout(() => (btn.textContent = old), 900);
    } else {
      alert('Could not copy. Please copy manually.');
    }
  }
});
