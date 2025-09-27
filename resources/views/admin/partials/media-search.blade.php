@forelse($media as $file)
    @include('admin.partials.media-search-result', ['file' => $file])
@empty
    <tr>
        <td colspan="6" class="text-center text-muted">No results found</td>
    </tr>
@endforelse
