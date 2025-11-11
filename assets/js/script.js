/* NexGizmo JS (Apple Style + Dark Mode Toggle + Cart) */

// Add to Cart Logic
async function addToCart(id, qty = 1) {
  try {
    const res = await fetch('/NexGizmo/add_to_cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${encodeURIComponent(id)}&qty=${encodeURIComponent(qty)}`
    });
    const j = await res.json();
    if (j.ok) {
      alert('✅ Added to cart!');
      location.reload();
    } else {
      alert(j.msg || 'Failed to add to cart.');
    }
  } catch (err) {
    alert('Network Error');
  }
}

// Theme toggle (light/dark)
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('themeToggle');
  const root = document.documentElement;

  // Load saved theme
  const saved = localStorage.getItem('theme');
  if (saved) root.setAttribute('data-theme', saved);

  updateButton();

  btn.addEventListener('click', () => {
    const current = root.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateButton();
  });

  function updateButton() {
    const mode = root.getAttribute('data-theme');
    btn.textContent = mode === 'dark' ? '☀️ Light Mode' : '🌙 Dark Mode';
  }
});
