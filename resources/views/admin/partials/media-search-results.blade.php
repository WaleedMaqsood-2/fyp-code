  @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
    @if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif

@if($mediaFiles->isEmpty())
    <p class="text-muted">No media found.</p>
@else
    <div class="card ">
        <div class="card-body">
                 @foreach($mediaFiles as $media)
                 <p>Media ID: {{ $media->id }}</p>
    @if (isset($media->file_path))
    <h5 class="card-text">File Path: {{ $media->file_path }}</h5>
    @endif
    @if (isset($media->file_type))
    <p class="card-text mb-1">File Type: {{ $media->file_type }}</p>
    @endif
    @if (isset($media->description))
    <p class="card-text mb-1">Description: {{ $media->description }}</p>
    @endif
    @if (isset($media->status))
    <p class="card-text mb-1">Status: {{ $media->status }}</p>
    @endif
    @if (isset($media->user_id))
    <p class="card-text mb-1">Uploaded By User: {{ $media->users->name }}</p>
    @endif
    @if (isset($media->uploaded_at))
    <p class="card-text mb-1">Uploaded At: {{ $media->uploaded_at }}</p>
    @endif
    @if (isset($media->created_at))
    <span class="small text-muted float-end">{{ $media->created_at }}</span>
    @endif
    <hr>
        @endforeach

    </div>
    </div>
@endif
