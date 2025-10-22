  <!-- Bootstrap + Icons + Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <!-- Dark Mode Toggle Script -->
  <script>
    const toggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    function setTheme(mode) {
      html.setAttribute('data-bs-theme', mode);
      localStorage.setItem('theme', mode);
      if (toggle) toggle.textContent = mode === 'dark' ? 'light_mode' : 'dark_mode';
    }

    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    if (toggle) {
      toggle.addEventListener('click', () => {
        const current = html.getAttribute('data-bs-theme');
        setTheme(current === 'dark' ? 'light' : 'dark');
      });
    }

    // Update year
    document.getElementById("year").textContent = new Date().getFullYear();
  </script>