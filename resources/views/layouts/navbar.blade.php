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
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom me-2">
      <div class="container-fluid">

<nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex ">
          <div class="input-group ms-4">
       
  <input type="text" id="main-search" 
         placeholder="{{ $searchPlaceholder ?? 'Search...' }}" 
         class="form-control" autocomplete="off" />
 <div class="input-group-prepend">
              <button type="button" class="btn btn-search pe-1" id="search-btn">
                <i class="fa fa-search search-icon"></i>
              </button>
            </div>

  <div id="search-suggestions" 
     class="dropdown-menu" 
     style="display:none; position:absolute; top:100%; left:0; width:100%;">
</div>

</nav>

        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center me-2">

        
          <li class="nav-item topbar-icon dropdown hidden-caret">
          @include('partials.notification-dropdown')

          </li>
      

          @php
            $user =Auth::user();
          @endphp
<li class="nav-item topbar-user dropdown hidden-caret">
  @include('partials.profile-dropdown')
    </li>



      
        </ul>
        
      </div>
    </nav>
    
  </div>
  <!-- End Navbar -->


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

       
        @include('partials.edit-profile-modal')
        @include('partials.view-profile-model')
     
