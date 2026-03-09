@if (isset($homeSubjects) && $homeSubjects->count() > 0)
    @foreach ($homeSubjects as $sub)
        <a href="javascript:void(0)" class="subject-item-premium subject-filter-btn" data-subject-id="{{ $sub->id }}"
            onclick="filterBySubject({{ $sub->id }})">
            <div class="subject-circle-premium">
                @if ($sub->subject_icon)
                    <img src="{{ asset('admin/images/subject_icons/' . $sub->subject_icon) }}"
                        style="width: 40px; height: 40px; object-fit: contain;"
                        onerror="this.parentElement.innerText='📚'" alt="{{ $sub->name }}">
                @else
                    📚
                @endif
            </div>
            <span class="category-label">{{ $sub->name }}</span>
        </a>
    @endforeach
@else
    <div class="w-100 text-center py-3">
        <p class="text-muted mb-0" style="font-size: 13px;">
            <i class="fas fa-info-circle me-1"></i> No subjects available.
        </p>
    </div>
@endif
