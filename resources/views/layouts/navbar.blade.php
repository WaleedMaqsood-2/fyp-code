<div class="main-panel">
  <div class="main-header">
    <div class="main-header-logo">
      <!-- Logo Header -->
      <div class="logo-header" data-background-color="dark">
        <a href="index.html" class="logo">
          <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
        </a>
        <div class="nav-toggle">
          <button class="btn btn-toggle toggle-sidebar">
            <i class="gg-menu-right"></i>
          </button>
          <button class="btn btn-toggle sidenav-toggler">
            <i class="gg-menu-left"></i>
          </button>
        </div>
        <button class="topbar-toggler more">
          <i class="gg-more-vertical-alt"></i>
        </button>
      </div>
      <!-- End Logo Header -->
    </div>
    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
      <div class="container-fluid">
        <!-- Admin User Search Bar -->
        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
          <div class="input-group ms-4">
            <input type="text" id="main-search" placeholder="@yield('search_placeholder', 'Search...')" class="form-control" autocomplete="off" />
            <div class="input-group-prepend">
              <button type="button" class="btn btn-search pe-1" id="search-btn">
                <i class="fa fa-search search-icon"></i>
              </button>
            </div>
            <div id="search-suggestions" class="mt-5 dropdown-menu" style="display:none; position:absolute; z-index:1000; width:100%"></div>
          </div>
        </nav>
      

        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">

        
          <li class="nav-item topbar-icon dropdown hidden-caret">
            <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-envelope"></i>
            </a>
            <ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
              <li>
                <div class="dropdown-title d-flex justify-content-between align-items-center">
                  Messages
                  <a href="#" class="small">Mark all as read</a>
                </div>
              </li>
              <li>
                <div class="message-notif-scroll scrollbar-outer">
                  <div class="notif-center">
                    <a href="#">
                      <div class="notif-img">
                        <img src="assets/img/jm_denis.jpg" alt="Img Profile" />
                      </div>
                      <div class="notif-content">
                        <span class="subject">Jimmy Denis</span>
                        <span class="block"> How are you ? </span>
                        <span class="time">5 minutes ago</span>
                      </div>
                    </a>
                    <a href="#">
                      <div class="notif-img">
                        <img src="assets/img/chadengle.jpg" alt="Img Profile" />
                      </div>
                      <div class="notif-content">
                        <span class="subject">Chad</span>
                        <span class="block"> Ok, Thanks ! </span>
                        <span class="time">12 minutes ago</span>
                      </div>
                    </a>
                    <a href="#">
                      <div class="notif-img">
                        <img src="assets/img/mlane.jpg" alt="Img Profile" />
                      </div>
                      <div class="notif-content">
                        <span class="subject">Jhon Doe</span>
                        <span class="block">
                          Ready for the meeting today...
                        </span>
                        <span class="time">12 minutes ago</span>
                      </div>
                    </a>
                    <a href="#">
                      <div class="notif-img">
                        <img src="assets/img/talha.jpg" alt="Img Profile" />
                      </div>
                      <div class="notif-content">
                        <span class="subject">Talha</span>
                        <span class="block"> Hi, Apa Kabar ? </span>
                        <span class="time">17 minutes ago</span>
                      </div>
                    </a>
                  </div>
                </div>
              </li>
              <li>
                <a class="see-all" href="javascript:void(0);">See all messages<i class="fa fa-angle-right"></i>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item topbar-icon dropdown hidden-caret">
            <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-bell"></i>
              <span class="notification">4</span>
            </a>
            <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
              <li>
                <div class="dropdown-title">
                  You have 4 new notification
                </div>
              </li>
              <li>
                <div class="notif-scroll scrollbar-outer">
                  <div class="notif-center">
                    <a href="#">
                      <div class="notif-icon notif-primary">
                        <i class="fa fa-user-plus"></i>
                      </div>
                      <div class="notif-content">
                        <span class="block"> New user registered </span>
                        <span class="time">5 minutes ago</span>
                      </div>
                    </a>
                    <a href="#">
                      <div class="notif-icon notif-success">
                        <i class="fa fa-comment"></i>
                      </div>
                      <div class="notif-content">
                        <span class="block">
                          Rahmad commented on Admin
                        </span>
                        <span class="time">12 minutes ago</span>
                      </div>
                    </a>
                    <a href="#">
                      <div class="notif-img">
                        <img src="assets/img/profile2.jpg" alt="Img Profile" />
                      </div>
                      <div class="notif-content">
                        <span class="block">
                          Reza send messages to you
                        </span>
                        <span class="time">12 minutes ago</span>
                      </div>
                    </a>
                    <a href="#">
                      <div class="notif-icon notif-danger">
                        <i class="fa fa-heart"></i>
                      </div>
                      <div class="notif-content">
                        <span class="block"> Farrah liked Admin </span>
                        <span class="time">17 minutes ago</span>
                      </div>
                    </a>
                  </div>
                </div>
              </li>
              <li>
                <a class="see-all" href="javascript:void(0);">See all notifications<i class="fa fa-angle-right"></i>
                </a>
              </li>
            </ul>
          </li>
      

          @php
            $user =Auth::user();
          @endphp
          <li class="nav-item topbar-user dropdown hidden-caret">
            @if($user)
              <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                <div class="avatar-sm">
                  <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/profile.jpg') }}" alt="..." class="avatar-img rounded-circle" />
                </div>
                <span class="profile-username">
                  <span class="op-7">{{ $user->name }}</span>
                </span>
              </a>
              <ul class="dropdown-menu dropdown-user animated fadeIn">
                <div class="dropdown-user-scroll scrollbar-outer">
                  <li>
                    <div class="user-box">
                      <div class="avatar-lg">
                        <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/img/profile.jpg') }}" alt="image profile" class="avatar-img rounded" />
                      </div>
                      <div class="u-text">
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        <!-- Profile Edit Button -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                          <i class="fas fa-user-edit"></i> Edit Profile
                        </button>
                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">My Profile</a>
                    <a class="dropdown-item" href="#">Account Setting</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                  </li>
                </div>
              </ul>
            @else
              <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
              <a class="btn btn-secondary ms-2" href="{{ route('register') }}">Register</a>
            @endif
          </li>
        </ul>
      </div>
    </nav>
  </div>
  <!-- End Navbar -->

  <!-- Profile Edit Modal -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label>Name</label>
              <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password (leave blank if not changing)</label>
              <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
              <label>Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div class="mb-3">
              <label>CNIC</label>
              <input type="text" name="cnic" value="{{ $user->cnic }}" class="form-control">
            </div>
            <div class="mb-3">
              <label>Contact Number</label>
              <input type="text" name="contact_number" value="{{ $user->contact_number }}" class="form-control">
            </div>
            <div class="mb-3">
              <label>Profile Image</label>
              <input type="file" name="profile_image" class="form-control">
              @if($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" class="img-thumbnail mt-2" width="100">
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Update Profile</button>
          </div>
        </form>
      </div>
    </div>
  </div>


    <!-- User Search Modal -->
        <div class="modal fade" id="userSearchModal" tabindex="-1" aria-labelledby="userSearchModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">@yield('search_modal_title', 'Search Results')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="search-results">
                <!-- Results will be injected here -->
              </div>
            </div>
          </div>
        </div>

        <script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('main-search');
  const suggestionsBox = document.getElementById('search-suggestions');
  const searchBtn = document.getElementById('search-btn');
  const modal = new bootstrap.Modal(document.getElementById('userSearchModal'));
  const resultsBox = document.getElementById('search-results');
  const searchConfig = window.searchConfig || {
    endpoint: '/admin/user-search',
    suggestionKey: 'users',
    resultKey: 'users',
  };
  let timeout = null;

  // Ensure parent is relative for dropdown positioning
  searchInput.parentElement.style.position = 'relative';
  suggestionsBox.style.zIndex = '1050';

  searchInput.addEventListener('input', function() {
    clearTimeout(timeout);
    const query = this.value.trim();
    if (query.length < 1  ) {
      suggestionsBox.style.display = 'none';
      return;
    }
    timeout = setTimeout(function() {
      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
   const items = data.users || data;  
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
        });
    }, 300);
  });

  // Hide suggestions on blur or click outside
  document.addEventListener('click', function(e) {
    if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
      suggestionsBox.style.display = 'none';
    }
  });

 suggestionsBox.addEventListener('click', function(e) {
  const target = e.target.closest('.dropdown-item'); // parent pakdo
  if (!target) return;
  
  const itemId = target.getAttribute('data-id');
  console.log("Clicked ID:", itemId); // debug
  
  fetch(`${searchConfig.endpoint}?id=${itemId}`)
    .then(res => res.text())
    .then(html => {
      resultsBox.innerHTML = html;
      modal.show();
    })
    .catch(err => console.error("Fetch Error:", err));
  
  suggestionsBox.classList.remove("show");
});


  searchBtn.addEventListener('click', function() {
    const query = searchInput.value.trim();
    if (query.length < 1) return;
    fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
      .then(res => res.text())
      .then(html => {
        resultsBox.innerHTML = html;
        modal.show();
      });
    suggestionsBox.style.display = 'none';
  });

  // Trigger search on Enter key
  searchInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      const query = searchInput.value.trim();
      if (query.length < 2) return;
      fetch(`${searchConfig.endpoint}?q=${encodeURIComponent(query)}`)
        .then(res => res.text())
        .then(html => {
          resultsBox.innerHTML = html;
          modal.show();
        });
      suggestionsBox.style.display = 'none';
    }
  });
});
</script>