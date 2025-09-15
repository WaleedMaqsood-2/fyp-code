@if($feedbacks->isEmpty())
    <p class="text-muted">No AI feedback found.</p>
@else
    <div class="card list-group">
        <div class="card-body">
                 @foreach($feedbacks as $item)
                    <p class="mb-0">Feedback ID: {{ $item->id }}</p>
    @if (isset($item->ai_type))
    <h5 class="card-title mb-0">Ai Type: {{ $item->ai_type }}</h5>
    @endif
    @if (isset($item->feedback_text))
    <p class="card-text mb-1">Feedback Text: {{ $item->feedback_text }}</p>
    @endif
    @if (isset($item->user_id))
    <p class="card-text mb-1">Feedback By User: {{ $item->users->name }}</p>
    @endif
    @if (isset($item->rating))
    <p class="card-text mb-1">Rating: {{ $item->rating }}/5</p>
    @endif
    @if (isset($item->created_at))
    <span class="small text-muted float-end">{{ $item->created_at }}</span>
    @endif
    
    <hr>
        @endforeach
    </div>
@endif
