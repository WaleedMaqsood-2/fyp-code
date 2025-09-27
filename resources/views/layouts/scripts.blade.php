<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Bundle with Popper -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

 <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?display=swap&family=Public+Sans:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<!-- Core JS Files -->
<script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
{{-- <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

<!-- jQuery Scrollbar -->
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

<!-- Chart JS -->
<script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>


<!-- jQuery Sparkline -->
<script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Chart Circle -->
<script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- jQuery Vector Maps -->
<script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>

<!-- Sweet Alert -->
<script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Kaiadmin JS -->
<script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 {{-- <script>
      $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#177dff",
        fillColor: "rgba(23, 125, 255, 0.14)",
      });

      $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#f3545d",
        fillColor: "rgba(243, 84, 93, .14)",
      });

      $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#ffa534",
        fillColor: "rgba(255, 165, 52, .14)",
      });
    </script>


<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('main-search');
  const suggestionsBox = document.getElementById('search-suggestions');
  const searchBtn = document.getElementById('search-btn');
  const resultsBox = document.getElementById('search-results');
  const modalEl = document.getElementById('userSearchModal');
  const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

  // Agar search bar exist hi nahi karta to script exit
  if (!searchInput || !suggestionsBox) {
    console.log("Search bar not found on this page. Skipping search script...");
    return;
  }

  // Default config (server se dynamic banaya ja sakta hai)
  const searchConfig = window.searchConfig || {
    endpoint: '/admin/user-search',
    suggestionKey: 'users',
    resultKey: 'users',
  };

  let timeout = null;

  // Ensure parent is relative for dropdown positioning
  if (searchInput.parentElement) {
    searchInput.parentElement.style.position = 'relative';
  }
  suggestionsBox.style.zIndex = '1050';

  // 🔎 Input ke sath suggestions show
  searchInput.addEventListener('input', function() {
    clearTimeout(timeout);
    const query = this.value.trim();
    if (query.length < 1) {
      suggestionsBox.style.display = 'none';
      return;
    }
    timeout = setTimeout(function() {
      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
          const items = data[searchConfig.suggestionKey] || [];
          console.log("Suggestions Data:", items);

          if (items.length > 0) {
            suggestionsBox.innerHTML = items.map(item => `
              <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}">
                <div class="fw-bold">${item.name || item.title}</div>
                <small class="text-muted">${item.email || ''}</small>
              </button>
            `).join('');
            suggestionsBox.style.display = 'block';
          } else {
            suggestionsBox.innerHTML = '<span class="dropdown-item">No results found</span>';
            suggestionsBox.style.display = 'block';
          }
        })
        .catch(err => console.error("Fetch Error:", err));
    }, 300);
  });

  // Hide suggestions on outside click
  document.addEventListener('click', function(e) {
    if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
      suggestionsBox.style.display = 'none';
    }
  });

  // 🖱 Suggestion click -> open details in modal
  suggestionsBox.addEventListener('click', function(e) {
    const target = e.target.closest('.dropdown-item');
    if (!target) return;

    const itemId = target.getAttribute('data-id');
    console.log("Clicked ID:", itemId);

    fetch(`${searchConfig.endpoint}?id=${itemId}`)
      .then(res => res.text())
      .then(html => {
        if (resultsBox) {
          resultsBox.innerHTML = html;
        }
        if (modal) modal.show();
      })
      .catch(err => console.error("Fetch Error:", err));

    suggestionsBox.style.display = 'none';
  });

  // 🔍 Search button click
  if (searchBtn) {
    searchBtn.addEventListener('click', function() {
      const query = searchInput.value.trim();
      if (query.length < 1) return;

      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
        .then(res => res.text())
        .then(html => {
          if (resultsBox) resultsBox.innerHTML = html;
          if (modal) modal.show();
        });
      suggestionsBox.style.display = 'none';
    });
  }

  // ⌨ Enter key
  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const query = searchInput.value.trim();
      if (query.length < 1) return;

      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
        .then(res => res.text())
        .then(html => {
          if (resultsBox) resultsBox.innerHTML = html;
          if (modal) modal.show();
        });
      suggestionsBox.style.display = 'none';
    }
  });
});
</script> --}}



{{-- <script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('main-search');
  const suggestionsBox = document.getElementById('search-suggestions');
  const searchBtn = document.getElementById('search-btn');
  const resultsBox = document.getElementById('search-results');
  const modalEl = document.getElementById('userSearchModal');
  const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

  // Agar search bar exist hi nahi karta to script exit
  if (!searchInput || !suggestionsBox) {
    console.log("Search bar not found on this page. Skipping search script...");
    return;
  }

  // Default config (server se dynamic banaya ja sakta hai)
  const searchConfig = window.searchConfig || {
    endpoint: '/admin/user-search',
    suggestionKey: 'users',
    resultKey: 'users',
  };

  let timeout = null;

  // Ensure parent is relative for dropdown positioning
  if (searchInput.parentElement) {
    searchInput.parentElement.style.position = 'relative';
  }
  suggestionsBox.style.zIndex = '1050';



  // 🔎 Input ke sath suggestions show
  searchInput.addEventListener('input', function() {
    clearTimeout(timeout);
    const query = this.value.trim();
    if (query.length < 1) {
      suggestionsBox.style.display = 'none';
      return;
    }
    timeout = setTimeout(function() {
      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
          const items = data[searchConfig.suggestionKey] || [];
          console.log("Suggestions Data:", items);

          if (items.length > 0) {
            suggestionsBox.innerHTML = items.map(item => `
              <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}">
                <div class="fw-bold">${item.name || item.title}</div>
                <small class="text-muted">${item.email || ''}</small>
              </button>
            `).join('');
            suggestionsBox.style.display = 'block';
          } else {
            suggestionsBox.innerHTML = '<span class="dropdown-item">No results found</span>';
            suggestionsBox.style.display = 'block';
          }
        })
        .catch(err => console.error("Fetch Error:", err));
    }, 300);
  });

  // Hide suggestions on outside click
  document.addEventListener('click', function(e) {
    if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
      suggestionsBox.style.display = 'none';
    }
  });

  // 🖱 Suggestion click -> open details in modal
  suggestionsBox.addEventListener('click', function(e) {
    const target = e.target.closest('.dropdown-item');
    if (!target) return;

    const itemId = target.getAttribute('data-id');
    console.log("Clicked ID:", itemId);

    fetch(`${searchConfig.endpoint}?id=${itemId}`)
      .then(res => res.text())
      .then(html => {
        if (resultsBox) {
          resultsBox.innerHTML = html;
        }
        if (modal) modal.show();
      })
      .catch(err => console.error("Fetch Error:", err));

    suggestionsBox.style.display = 'none';
  });

  // 🔍 Search button click
  if (searchBtn) {
    searchBtn.addEventListener('click', function() {
      const query = searchInput.value.trim();
      if (query.length < 1) return;

      // 🔍 Enter/Search button
fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
  .then(res => res.text())
  .then(html => {
    if (resultsBox) resultsBox.innerHTML = html;
    if (modal) modal.show();
  });

      suggestionsBox.style.display = 'none';
    });
  }

  // ⌨ Enter key
  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const query = searchInput.value.trim();
      if (query.length < 1) return;

      // 🔍 Enter/Search button
fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
  .then(res => res.text())
  .then(html => {
    if (resultsBox) resultsBox.innerHTML = html;
    if (modal) modal.show();
  });

      suggestionsBox.style.display = 'none';
    }
  });
});
</script> --}}
{{-- <script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('main-search');
  const suggestionsBox = document.getElementById('search-suggestions');
  const searchBtn = document.getElementById('search-btn');
  const resultsBox = document.getElementById('search-results');
  const modalEl = document.getElementById('userSearchModal');
  const modal = modalEl ? new bootstrap.Modal(modalEl) : null;

  if (!searchInput || !suggestionsBox) return;

  const searchConfig = window.searchConfig || {
  endpoint: '/admin/user-search', // fallback agar config na bhejo
  suggestionKey: 'users',
  resultKey: 'users',
};


  let timeout = null;
  if (searchInput.parentElement) {
    searchInput.parentElement.style.position = 'relative';
  }
  suggestionsBox.style.zIndex = '1050';

  // 🔎 Suggestions on typing
  searchInput.addEventListener('input', function() {
    clearTimeout(timeout);
    const query = this.value.trim();
    if (query.length < 1) {
      suggestionsBox.style.display = 'none';
      return;
    }
    timeout = setTimeout(function() {
      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}&mode=suggestion`)
        .then(res => res.json())   // 👈 Suggestion = JSON
        .then(data => {
  const items = data[searchConfig.suggestionKey] || [];
  if (items.length > 0) {
    suggestionsBox.innerHTML = items.map(item => {
      switch (searchConfig.suggestionKey) {
        case "users":
          return `
            <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}">
              <div class="fw-bold">${item.name} </div>
              <small class="text-muted">${item.email || ''}</small>
            </button>
          `;
        case "media":
          return `
            <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}">
              <div class="fw-bold">${item.file_type} Status: ${item.status}</div>
              <small class="text-muted">${item.file_path || ''}</small>
            </button>
          `;
        case "feedback":
          return `
            <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}">
              <div class="fw-bold">AI-Type: ${item.ai_type}</div>
              <div class="fw-semi-bold">Rating: ${item.rating}</div>
              <small class="text-muted">${item.feedback_text || ''}</small>
            </button>
          `;
        default:
          return `
            <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}">
              <div class="fw-bold">${item.id}</div>
            </button>
          `;
      }
    }).join('');
    suggestionsBox.style.display = 'block';
  } else {
    suggestionsBox.innerHTML = '<span class="dropdown-item">No results found</span>';
    suggestionsBox.style.display = 'block';
  }
})

        .catch(err => console.error("Fetch Error:", err));
    }, 300);
  });

  // Hide suggestions on outside click
  document.addEventListener('click', function(e) {
    if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
      suggestionsBox.style.display = 'none';
    }
  });

  // 🖱 Suggestion click → open details
  suggestionsBox.addEventListener('click', function(e) {
    const target = e.target.closest('.dropdown-item');
    if (!target) return;

    const itemId = target.getAttribute('data-id');
    fetch(`${searchConfig.endpoint}?id=${itemId}`)
      .then(res => res.text())
      .then(html => {
        if (resultsBox) resultsBox.innerHTML = html;
        if (modal) modal.show();
      });
    suggestionsBox.style.display = 'none';
  });

  // 🔍 Search button
  if (searchBtn) {
    searchBtn.addEventListener('click', function() {
      const query = searchInput.value.trim();
      if (query.length < 1) return;

      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
        .then(res => res.text())   // 👈 Full search = HTML
        .then(html => {
          if (resultsBox) resultsBox.innerHTML = html;
          if (modal) modal.show();
        });
      suggestionsBox.style.display = 'none';
    });
  }

  // ⌨ Enter key
  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const query = searchInput.value.trim();
      if (query.length < 1) return;

      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
        .then(res => res.text())   // 👈 Full search = HTML
        .then(html => {
          if (resultsBox) resultsBox.innerHTML = html;
          if (modal) modal.show();
        });
      suggestionsBox.style.display = 'none';
    }
  });
});
</script> --}}


<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('main-search');
  const suggestionsBox = document.getElementById('search-suggestions');
  const searchBtn = document.getElementById('search-btn');
  const resultsBox = document.getElementById('search-results'); 
  const cardsContainer = document.getElementById('user-card');  // 👈 yahan results load honge

  if (!searchInput || !suggestionsBox) return;

  // 📌 Save original cards (jo page load par hain)
  const originalCards = cardsContainer ? cardsContainer.innerHTML : '';

  const searchConfig = window.searchConfig || {
    endpoint: '/admin/user-search', // fallback
    suggestionKey: 'users',
    resultKey: 'users',
  };

  let timeout = null;
  if (searchInput.parentElement) {
    searchInput.parentElement.style.position = 'relative';
  }
  suggestionsBox.style.zIndex = '1050';

  // 🔎 Suggestions + Cards on typing
  searchInput.addEventListener('input', function() {
    clearTimeout(timeout);
    const query = this.value.trim();

    if (query.length < 1) {
      // 👈 Reset to original cards
      suggestionsBox.style.display = 'none';
      if (cardsContainer) cardsContainer.innerHTML = originalCards;
      return;
    }

    timeout = setTimeout(function() {
      // 1) Suggestion ke liye JSON fetch
      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}&mode=suggestion`)
        .then(res => res.json())
        .then(data => {
          const items = data[searchConfig.suggestionKey] || [];
          if (items.length > 0) {
            suggestionsBox.innerHTML = items.map(item => {
              switch (searchConfig.suggestionKey) {
                case "users":
                  return `
                    <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}" data-name="${item.name}">
                      <div class="fw-bold">${item.name}</div>
                      <small class="text-muted">${item.email || ''}</small>
                    </button>
                  `;
                case "media":
                  return `
                    <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}" data-name="${item.name}">
                      <div class="fw-bold">${item.file_type} Status: ${item.status}</div>
                      <small class="text-muted">${item.file_path || ''}</small>
                    </button>
                  `;
                case "feedback":
                  return `
                    <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}">
                      <div class="fw-bold">AI-Type: ${item.ai_type}</div>
                      <div class="fw-semi-bold">Rating: ${item.rating}</div>
                      <small class="text-muted">${item.feedback_text || ''}</small>
                    </button>
                  `;
                default:
                  return `
                    <button class="dropdown-item w-100 text-start" type="button" data-id="${item.id}">
                      <div class="fw-bold">${item.id}</div>
                    </button>
                  `;
              }
            }).join('');
            suggestionsBox.style.display = 'block';
          } else {
            suggestionsBox.innerHTML = '<span class="dropdown-item">No results found</span>';
            suggestionsBox.style.display = 'block';
          }
        })
        .catch(err => console.error("Fetch Error:", err));

      // 2) Cards ke liye HTML fetch
      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}&mode=cards`)
        .then(res => res.text())
        .then(html => {
          if (cardsContainer) cardsContainer.innerHTML = html;
         
        });
    }, 300);
  });

  // Hide suggestions on outside click
  document.addEventListener('click', function(e) {
    if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
      suggestionsBox.style.display = 'none';
    }
  });

  // 🖱 Suggestion click → load user cards
suggestionsBox.addEventListener('click', function(e) {
  const target = e.target.closest('.dropdown-item');
  if (!target) return;
 const userName = target.dataset.name; // 👈 ab name defined hoga
  searchInput.value = userName;         // input me name set karo

  // Ab normal search ki tarah cards fetch karo
  fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(userName)}&mode=cards`)
    .then(res => res.text())
    .then(html => {
      if (cardsContainer) cardsContainer.innerHTML = html;
    });

  suggestionsBox.style.display = 'none';
});


  // 🔍 Search button
  if (searchBtn) {
    searchBtn.addEventListener('click', function() {
      const query = searchInput.value.trim();
      if (query.length < 1) {
        if (cardsContainer) cardsContainer.innerHTML = originalCards;
        return;
      }

      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}&mode=cards`)
        .then(res => res.text())
        .then(html => {
          if (cardsContainer) cardsContainer.innerHTML = html;
        });
      suggestionsBox.style.display = 'none';
    });
  }

  // ⌨ Enter key
  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const query = searchInput.value.trim();
      if (query.length < 1) {
        if (cardsContainer) cardsContainer.innerHTML = originalCards;
        return;
      }

      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}&mode=cards`)
        .then(res => res.text())
        .then(html => {
          if (cardsContainer) cardsContainer.innerHTML = html;
        });
      suggestionsBox.style.display = 'none';
    }
  });
});
</script>


