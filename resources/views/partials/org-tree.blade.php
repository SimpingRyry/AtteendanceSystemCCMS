<li>
    {{ $org->org_name }}
    @if ($org->children->count())
        <ul class="ms-4 mt-1">
            @foreach ($org->children as $child)
                @include('partials.org-tree', ['org' => $child])
            @endforeach
        </ul>
    @endif
</li>
