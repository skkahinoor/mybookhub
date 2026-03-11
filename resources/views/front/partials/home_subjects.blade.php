@if (isset($homeSubjects) && $homeSubjects->count() > 0)
    @foreach ($homeSubjects as $sub)
        <a href="javascript:void(0)" class="subject-tablet subject-filter-btn" data-subject-id="{{ $sub->id }}"
            onclick="filterBySubject({{ $sub->id }})">
            {{ $sub->name }}
        </a>
    @endforeach
@else
    <div class="w-100 text-center py-3">
        <p class="text-muted mb-0" style="font-size: 13px;">
            <i class="fas fa-info-circle me-1"></i> No subjects available.
        </p>
    </div>
@endif
