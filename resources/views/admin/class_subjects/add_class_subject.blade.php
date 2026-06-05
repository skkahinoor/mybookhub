@extends('admin.layout.layout')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-12 grid-margin">
                    <div class="row">
                        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                            <h3 class="font-weight-bold">Assign Subjects to Class</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Select Class and Subjects</h4>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form class="forms-sample" action="{{ route('admin.class_subjects.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="section_id">Select Education Level</label>
                                    <select name="section_id" id="section_id" class="form-control" required>
                                        <option value="">Select Education Level</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="appendCategoriesLevel">
                                    {{-- Categories will be loaded here via AJAX --}}
                                </div>
                                <div id="appendSubcategoriesLevel">
                                    {{-- Subcategories will be loaded here via AJAX --}}
                                </div>
                                <div class="form-group" id="subjects-container">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="mb-0">Select Subjects</label>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-xs btn-outline-primary" id="selectAllFiltered" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Select All Visible</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary" id="deselectAllFiltered" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; margin-left: 5px;">Deselect All Visible</button>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <input type="text" id="search-subjects" class="form-control" placeholder="Search subjects here...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Clear search"><i class="mdi mdi-close"></i></button>
                                            <button class="btn btn-success" type="button" id="open-quick-subject-btn" title="Create a new subject"><i class="mdi mdi-plus"></i> Add Subject</button>
                                        </div>
                                    </div>
                                    <!-- Quick Subject Creation Panel -->
                                    <div id="quick-subject-panel" class="card mb-3 border-success" style="display: none; background-color: #f8fff9; transition: all 0.3s ease;">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="card-title text-success mb-0" style="font-weight: 600;"><i class="mdi mdi-plus-circle"></i> Create New Subject</h6>
                                                <button type="button" class="close" id="close-quick-subject-panel" aria-label="Close" style="background: none; border: none; font-size: 1.2rem; line-height: 1;">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control form-control-sm" id="new-subject-name" placeholder="Enter subject name..." autocomplete="off">
                                                <div class="input-group-append">
                                                    <button class="btn btn-sm btn-success" type="button" id="save-new-subject-btn">Save</button>
                                                </div>
                                            </div>
                                            <div class="text-danger small font-weight-bold" id="quick-subject-feedback" style="display: none; margin-top: 5px;"></div>
                                            <div class="text-warning small font-weight-bold" id="quick-subject-warning" style="display: none; margin-top: 5px;"></div>
                                        </div>
                                    </div>
                                    <div class="row" id="subjects-list" style="max-height: 250px; overflow-y: auto; border: 1px solid #e3e3e3; padding: 15px; border-radius: 4px; margin: 0 1px 15px 1px; background-color: #fcfcfc;">
                                        @foreach ($subjects as $subject)
                                            <div class="col-md-6 subject-item">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="subject_ids[]"
                                                            value="{{ $subject->id }}" class="form-check-input">
                                                        {{ $subject->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Assign Subjects</button>
                                <a href="{{ route('admin.class_subjects.index') }}" class="btn btn-light">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes highlightPulse {
            0% { background-color: #ffeeba; transform: scale(1.02); }
            50% { background-color: #ffeeba; transform: scale(1.02); }
            100% { background-color: transparent; transform: scale(1); }
        }
        .subject-highlight {
            animation: highlightPulse 2.5s ease-in-out;
            border-radius: 4px;
            padding: 2px 5px;
            display: inline-block;
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle Category change to load Classes (Subcategories)
            // We use delegated event because #category_id is loaded via AJAX
            $(document).on('change', '#category_id', function(e) {
                // If this page is Class-Subject Assignment, we load subcategories
                // instead of filters (which custom.js might try to do correctly if we check for target)
                var category_id = $(this).val();
                if ($("#appendSubcategoriesLevel").length > 0 && category_id != "") {
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        type: "get",
                        url: "{{ url('admin/append-subcategories-level') }}",
                        data: {
                            category_id: category_id
                        },
                        success: function(resp) {
                            $("#appendSubcategoriesLevel").html(resp);
                        },
                        error: function() {
                            alert("Error loading classes");
                        }
                    });
                }
            });
            $(document).on('change', '#section_id', function() {
                var sectionName = $(this).find("option:selected").text().trim().toLowerCase();
                var noSubjectsSections = ['religious book', 'religious', 'religious books'];
                
                if (noSubjectsSections.includes(sectionName)) {
                    $("#appendSubcategoriesLevel").hide();
                    $("#subjects-container").hide();
                } else {
                    $("#appendSubcategoriesLevel").show();
                    $("#subjects-container").show();
                }
            });

            // Live search for subjects
            $('#search-subjects').on('keyup input', function() {
                var value = $(this).val().toLowerCase();
                var visibleCount = 0;
                
                $('#subjects-list .subject-item').each(function() {
                    var text = $(this).text().toLowerCase();
                    if (text.indexOf(value) > -1) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                if (visibleCount === 0) {
                    if ($('#no-subjects-found').length === 0) {
                        $('#subjects-list').append('<div id="no-subjects-found" class="col-12 text-muted text-center py-3">No matching subjects found</div>');
                    }
                } else {
                    $('#no-subjects-found').remove();
                }
            });

            // Clear search field
            $('#clear-search').on('click', function() {
                $('#search-subjects').val('').trigger('input');
            });

            // Select all visible subjects
            $('#selectAllFiltered').on('click', function() {
                $('#subjects-list .subject-item:visible').find('input[type="checkbox"]').prop('checked', true);
            });

            // Deselect all visible subjects
            $('#deselectAllFiltered').on('click', function() {
                $('#subjects-list .subject-item:visible').find('input[type="checkbox"]').prop('checked', false);
            });

            // Toggle quick subject panel
            $('#open-quick-subject-btn').on('click', function() {
                $('#quick-subject-panel').slideToggle(200, function() {
                    if ($(this).is(':visible')) {
                        $('#new-subject-name').focus();
                        // Pre-fill with search term if it has a value
                        var searchVal = $('#search-subjects').val().trim();
                        if (searchVal !== '') {
                            $('#new-subject-name').val(searchVal).trigger('input');
                        }
                    }
                });
            });

            $('#close-quick-subject-panel').on('click', function() {
                $('#quick-subject-panel').slideUp(200);
            });

            // Client-side real-time duplicate check
            $('#new-subject-name').on('keyup input', function() {
                var newName = $(this).val().trim().toLowerCase();
                var exists = false;
                var exactName = '';

                if (newName !== '') {
                    $('#subjects-list .subject-item').each(function() {
                        var existingName = $(this).find('.form-check-label').text().trim();
                        if (existingName.toLowerCase() === newName) {
                            exists = true;
                            exactName = existingName;
                            return false; // break loop
                        }
                    });
                }

                if (exists) {
                    $('#save-new-subject-btn').prop('disabled', true);
                    $('#quick-subject-warning').html(
                        'Subject "' + exactName + '" already exists in the list. ' +
                        '<a href="javascript:void(0)" class="highlight-existing-subject text-primary font-weight-bold" data-name="' + exactName + '">Highlight & Select it</a>'
                    ).show();
                    $('#quick-subject-feedback').hide();
                } else {
                    $('#save-new-subject-btn').prop('disabled', false);
                    $('#quick-subject-warning').hide();
                }
            });

            // Highlight & select existing subject
            $(document).on('click', '.highlight-existing-subject', function() {
                var targetName = $(this).data('name').toLowerCase();
                
                // 1. Clear search field so all subjects are visible
                $('#clear-search').trigger('click');
                
                // 2. Find the subject item
                $('#subjects-list .subject-item').each(function() {
                    var label = $(this).find('.form-check-label');
                    if (label.text().trim().toLowerCase() === targetName) {
                        var checkbox = $(this).find('input[type="checkbox"]');
                        checkbox.prop('checked', true).trigger('change');
                        
                        // Scroll container to this item
                        var container = $('#subjects-list');
                        var scrollTo = $(this);
                        
                        container.animate({
                            scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop() - 10
                        }, 500);
                        
                        // Add highlight animation class
                        label.addClass('subject-highlight');
                        setTimeout(function() {
                            label.removeClass('subject-highlight');
                        }, 2600);
                        
                        // Close quick panel
                        $('#quick-subject-panel').slideUp(200);
                        return false;
                    }
                });
            });

            // Save new subject via AJAX
            $('#save-new-subject-btn').on('click', function() {
                saveNewSubject();
            });

            $('#new-subject-name').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    saveNewSubject();
                }
            });

            function saveNewSubject() {
                var subjectName = $('#new-subject-name').val().trim();
                if (subjectName === '') {
                    $('#quick-subject-feedback').text('Subject name cannot be empty.').show();
                    return;
                }

                $('#save-new-subject-btn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    type: "POST",
                    url: "{{ route('admin.quick_store_subject') }}",
                    data: {
                        name: subjectName
                    },
                    success: function(resp) {
                        $('#save-new-subject-btn').prop('disabled', false).text('Save');
                        if (resp.status) {
                            // Clear inputs & feedback
                            $('#new-subject-name').val('');
                            $('#quick-subject-feedback').hide();
                            $('#quick-subject-warning').hide();
                            $('#quick-subject-panel').slideUp(200);
                            
                            // Check if the subject is already in the list (e.g. it was inactive and now activated)
                            var alreadyInList = false;
                            var existingCheckbox = null;
                            
                            $('#subjects-list .subject-item').each(function() {
                                var checkbox = $(this).find('input[type="checkbox"]');
                                if (checkbox.val() == resp.subject.id) {
                                    alreadyInList = true;
                                    existingCheckbox = checkbox;
                                    return false;
                                }
                            });
                            
                            if (alreadyInList) {
                                // If it's already there, just check it, scroll to it and highlight it
                                existingCheckbox.prop('checked', true).trigger('change');
                                var label = existingCheckbox.closest('.form-check-label');
                                var container = $('#subjects-list');
                                var item = existingCheckbox.closest('.subject-item');
                                
                                container.animate({
                                    scrollTop: item.offset().top - container.offset().top + container.scrollTop() - 10
                                }, 500);
                                
                                label.addClass('subject-highlight');
                                setTimeout(function() {
                                    label.removeClass('subject-highlight');
                                }, 2600);
                            } else {
                                // Create new checkbox HTML
                                var newSubjectHtml = 
                                    '<div class="col-md-6 subject-item">' +
                                        '<div class="form-check">' +
                                            '<label class="form-check-label">' +
                                                '<input type="checkbox" name="subject_ids[]" value="' + resp.subject.id + '" class="form-check-input" checked>' +
                                                resp.subject.name +
                                                '<i class="input-helper"></i>' +
                                            '</label>' +
                                        '</div>' +
                                    '</div>';
                                
                                // Remove 'no subjects found' if visible
                                $('#no-subjects-found').remove();
                                
                                // Append and highlight new subject
                                var $newEl = $(newSubjectHtml).appendTo('#subjects-list');
                                var $label = $newEl.find('.form-check-label');
                                
                                // Trigger change event to ensure any custom template handlers run
                                $newEl.find('input[type="checkbox"]').trigger('change');
                                
                                // Scroll to bottom of container
                                var container = $('#subjects-list');
                                container.animate({
                                    scrollTop: container.prop("scrollHeight")
                                }, 500);
                                
                                $label.addClass('subject-highlight');
                                setTimeout(function() {
                                    $label.removeClass('subject-highlight');
                                }, 2600);
                            }
                        } else {
                            $('#quick-subject-feedback').text(resp.message).show();
                        }
                    },
                    error: function(xhr) {
                        $('#save-new-subject-btn').prop('disabled', false).text('Save');
                        var errorMsg = 'Error saving subject.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        $('#quick-subject-feedback').text(errorMsg).show();
                    }
                });
            }
        });
    </script>
@endpush
