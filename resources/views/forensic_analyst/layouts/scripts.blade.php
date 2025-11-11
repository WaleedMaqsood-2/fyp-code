
   <!-- jQuery & Bootstrap Bundle -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 


     <!-- jQuery (for simple interactions/AJAX) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Bootstrap bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


 
<!-- Bootstrap Bundle with Popper -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

 <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?display=swap&family=Public+Sans:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">


  
  
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
<script>
    (function(){
      const html = document.documentElement;
      const themeToggle = document.getElementById('themeToggle');
      const themeIcon = document.getElementById('themeIcon');

      function setTheme(mode){
        html.setAttribute('data-bs-theme', mode);
        localStorage.setItem('theme', mode);
        if(mode === 'dark'){
          themeIcon.className = 'bi bi-sun-fill';
        } else {
          themeIcon.className = 'bi bi-moon';
        }
      }

      // initialize theme
      const saved = localStorage.getItem('theme') || 'light';
      setTheme(saved);

      themeToggle.addEventListener('click', ()=>{
        const cur = html.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
        setTheme(cur === 'dark' ? 'light' : 'dark');
      });

      // sidebar toggle for mobile
      $('#sidebarToggle').on('click', function(){
        $('.sidebar').toggleClass('show');
        // simple backdrop (optional)
        if($('.sidebar').hasClass('show')){
          if(!$('.sidebar-backdrop').length){
            $('<div class="sidebar-backdrop"></div>').appendTo('body').css({
              position:'fixed', inset:0, zIndex:1035, background:'rgba(0,0,0,0.3)'
            }).on('click', function(){
              $('.sidebar').removeClass('show'); $(this).remove();
            });
          }
        }else{
          $('.sidebar-backdrop').remove();
        }
      });

      // case selection
      $('.case-list').on('click', '.case-card', function(){
        $('.case-card').removeClass('selected');
        $(this).addClass('selected');
        const caseId = $(this).data('case');
        // update detail panel header & content (simple)
        $('.detail-panel h5 span.text-primary').text(caseId);
        // optionally load details via AJAX:
        // $.get('/api/cases/'+caseId, function(data){ ... });
      });

      // play video (demo)
      $('#playVideo').on('click', function(){
        alert('Play video (demo) — replace with modal or player.');
      });

      // filter & sort demo (replace with actual behavior)
      $('#filterBtn').on('click', function(){
        // Example AJAX placeholder:
        // $.get('/api/cases?filter=...', function(html){ $('.case-list').html(html); });
        alert('Filter clicked — implement filter modal / AJAX as needed');
      });
      $('#sortBtn').on('click', function(){
        alert('Sort clicked — implement sorting logic here');
      });

      // search demo (simple highlight by case id)
      $('#searchInput').on('input', function(){
        const q = $(this).val().trim().toLowerCase();
        if(!q) { $('.case-card').show(); return; }
        $('.case-card').each(function(){
          const text = $(this).text().toLowerCase();
          $(this).toggle(text.indexOf(q) !== -1);
        });
      });

      // footer year
      document.getElementById('year').textContent = new Date().getFullYear();

      // ensure theme of footer links (no separate listener needed since CSS uses variables)
      window.addEventListener('storage', ()=> setTheme(localStorage.getItem('theme') || 'light'));
    })();
  </script>