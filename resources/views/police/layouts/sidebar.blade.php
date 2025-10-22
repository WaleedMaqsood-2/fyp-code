<aside class="sidebar p-3 d-flex flex-column justify-content-between">
  <nav class="nav flex-column gap-2">

    <a href="{{ route('police.dashboard') }}"
       class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('police.dashboard') ? 'active text-primary fw-bold' : 'text-body' }}">
      <span class="material-symbols-outlined">dashboard</span> Dashboard
    </a>

      <a href="{{ route('police.add-fir') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('police.add-fir') ? 'active text-primary fw-bold' : 'text-body' }}">
            <span class="material-symbols-outlined">feed</span> File FIR
          </a>
          <a href="{{ route('police.complaint-management') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('police.complaint-management') ? 'active text-primary fw-bold' : 'text-body' }}">
            <span class="material-symbols-outlined">folder</span> Cases
          </a>
          <a href="{{ route('police.ai-tools') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('police.ai-tools') ? 'active text-primary fw-bold' : 'text-body' }}">
            <span class="material-symbols-outlined">face</span> AI Tools
          </a>
          <a href="{{ route('police.manage-evidence') }}" class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('police.manage-evidence') ? 'active text-primary fw-bold' : 'text-body' }}">
            <span class="material-symbols-outlined">photo_library</span> Manage Evidence
          </a>
         
          

  </nav>

  <div class="d-flex flex-column gap-2">
    <button class="btn btn-primary w-100 fw-bold">File New FIR</button>
    <button class="btn btn-secondary w-100 fw-bold">Upload Evidence</button>
  </div>
</aside>
