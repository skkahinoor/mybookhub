<div class="category-scroll">
    @if (isset($homeSubjects) && $homeSubjects->count() > 0)
        @foreach ($homeSubjects as $sub)
            <a href="javascript:void(0)" class="category-item subject-filter-btn" data-subject-id="{{ $sub->id }}"
                onclick="filterBySubject({{ $sub->id }})">
                <div class="category-icon">
                    @if ($sub->subject_icon)
                        <img src="{{ asset('front/images/subject_icons/' . $sub->subject_icon) }}"
                            onerror="this.src='https://img.icons8.com/color/96/book.png'" alt="{{ $sub->name }}">
                    @else
                        <img src="https://img.icons8.com/color/96/book.png" alt="{{ $sub->name }}">
                    @endif
                </div>
                <span class="category-label">{{ $sub->name }}</span>
            </a>
        @endforeach
    @else
        <div class="w-100 text-center py-3">
            <p class="text-muted mb-0" style="font-size: 13px;">
                <i class="fas fa-info-circle me-1"></i> No subjects available for this selection.
            </p>
        </div>
    @endif
</div>
