@include('user.layout.header')

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="woocommerce-account-header"
                                style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                                <div>
                                    <h1 style="font-size: 24px; font-weight: 600; margin-bottom: 10px;">My Book Request
                                        Queries</h1>
                                    <p style="color: #666; margin: 0;">View the status and replies for all your book
                                        request
                                        queries.</p>
                                </div>
                                <a href="{{ route('user.query.index') }}" class="woocommerce-Button"
                                    style="background-color: #cf8938; color: #fff; padding: 12px 30px; border-radius: 4px; text-decoration: none; display: inline-block; font-weight: 600; transition: background-color 0.3s; white-space: nowrap;"
                                    onmouseover="this.style.backgroundColor='#b8752f'"
                                    onmouseout="this.style.backgroundColor='#cf8938'">
                                    <span style="margin-right: 8px;">➕</span>
                                    New Book Query
                                </a>
                            </div>

                            @if (session('success'))
                                <div class="woocommerce-message"
                                    style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="woocommerce-error"
                                    style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if ($queries->isEmpty())
                                <div class="empty-state" style="text-align: center; padding: 60px 20px;">
                                    <div class="empty-state-icon" style="font-size: 64px; margin-bottom: 20px;">💬</div>
                                    <h3 style="color: #333; margin-bottom: 10px;">No queries yet</h3>
                                    <p style="color: #666; margin-bottom: 30px;">You haven't submitted any book request
                                        queries yet.</p>
                                    <a href="{{ route('user.book.request') }}" class="woocommerce-Button"
                                        style="background-color: #cf8938; color: #fff; padding: 12px 30px; border-radius: 4px; text-decoration: none; display: inline-block;">
                                        Search & Request Books
                                    </a>
                                </div>
                            @else
                                <!-- Quick New Query Form -->
                                <style>
                                    .query-form-hidden {
                                        display: none;
                                    }
                                </style>
                                <div class="card"
                                    style="margin-bottom: 20px; border: 1px solid #e5e5e5; border-radius: 8px; overflow: hidden;">
                                    <div class="card-header"
                                        style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #e5e5e5; cursor: pointer;"
                                        onclick="var form = document.getElementById('newQueryForm'); var icon = this.querySelector('.toggle-icon'); if(form.style.display === 'none') { form.style.display = 'block'; icon.textContent = '➖'; } else { form.style.display = 'none'; icon.textContent = '➕'; }">
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center;">
                                            <h5
                                                style="margin: 0; color: #333; font-weight: 600; display: flex; align-items: center;">
                                                <span style="margin-right: 10px; font-size: 20px;">💬</span>
                                                Raise New Book Query
                                            </h5>
                                            <span class="toggle-icon" style="font-size: 18px; color: #666;">➕</span>
                                        </div>
                                    </div>
                                    <div id="newQueryForm" style="display: none; padding: 20px; background: #fff;">
                                        <form action="{{ route('user.book.request.store') }}" method="POST">
                                            @csrf
                                            <div class="mb-3">
                                                <label
                                                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                                    Book Title <span class="required" style="color: #e74c3c;">*</span>
                                                </label>
                                                <input type="text" name="book_title" class="form-control"
                                                    style="width: 100%; padding: 12px; border: 2px solid #e5e5e5; border-radius: 4px; font-size: 16px;"
                                                    placeholder="Enter book title" value="{{ old('book_title') }}"
                                                    required>
                                                @error('book_title')
                                                    <small
                                                        style="color: #e74c3c; display: block; margin-top: 5px;">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label
                                                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                                    Author Name
                                                </label>
                                                <input type="text" name="author_name" class="form-control"
                                                    style="width: 100%; padding: 12px; border: 2px solid #e5e5e5; border-radius: 4px; font-size: 16px;"
                                                    placeholder="Enter author name (optional)"
                                                    value="{{ old('author_name') }}">
                                                @error('author_name')
                                                    <small
                                                        style="color: #e74c3c; display: block; margin-top: 5px;">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label
                                                    style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                                    Your Message <span class="required" style="color: #e74c3c;">*</span>
                                                </label>
                                                <textarea name="message" rows="5" class="form-control"
                                                    style="width: 100%; padding: 12px; border: 2px solid #e5e5e5; border-radius: 4px; font-size: 16px; resize: vertical;"
                                                    required minlength="10" placeholder="Type your message here... Provide details about the book you're looking for.">{{ old('message') }}</textarea>
                                                @error('message')
                                                    <small
                                                        style="color: #e74c3c; display: block; margin-top: 5px;">{{ $message }}</small>
                                                @enderror
                                                <small style="color: #666; display: block; margin-top: 5px;">
                                                    Minimum 10 characters required
                                                </small>
                                            </div>
                                            <div style="margin-top: 15px;">
                                                <button type="submit" class="woocommerce-Button"
                                                    style="background-color: #cf8938; color: #fff; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: background-color 0.3s;">
                                                    <span style="margin-right: 5px;">📤</span>
                                                    Submit Query
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Accordion Style Query Display -->
                                <div class="accordion" id="queriesAccordion" style="margin-top: 20px;">
                                    @foreach ($queries as $key => $query)
                                        @php
                                            $collapseId = 'collapse' . $query->id;
                                            $headingId = 'heading' . $query->id;
                                            // Handle both numeric and string status values
                                            $statusValue = is_numeric($query->status) ? $query->status : $query->status;
                                            $isResolved = $statusValue === 'resolved' || $statusValue === 'Resolved';
                                            // Expand first query or if status is not resolved, collapse resolved queries by default
                                            $isExpanded =
                                                ($key == 0 && !$isResolved) ||
                                                ($key == 0 && $isResolved && $queries->count() == 1);
                                        @endphp

                                        <div class="accordion-item"
                                            style="border: 1px solid #e5e5e5; border-radius: 8px; margin-bottom: 15px; overflow: hidden;">
                                            <h2 class="accordion-header" id="{{ $headingId }}">
                                                <button class="accordion-button {{ $isExpanded ? '' : 'collapsed' }}"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#{{ $collapseId }}"
                                                    aria-expanded="{{ $isExpanded ? 'true' : 'false' }}"
                                                    aria-controls="{{ $collapseId }}"
                                                    style="background: #f8f9fa; padding: 15px 20px;">
                                                    <div
                                                        style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
                                                        <div style="flex: 1;">
                                                            <strong
                                                                style="color: #333; font-size: 16px; display: block; margin-bottom: 5px;">{{ $query->book_title }}</strong>
                                                            <small style="color: #666; font-size: 13px;">Requested
                                                                on
                                                                {{ $query->created_at->format('M d, Y h:i A') }}</small>
                                                        </div>
                                                        <div
                                                            style="margin-left: 15px; display: flex; align-items: center; gap: 10px;">
                                                            @php
                                                                // Handle both numeric and string status
                                                                $status = is_numeric($query->status)
                                                                    ? (int) $query->status
                                                                    : $query->status;
                                                                $isResolved =
                                                                    $status === 'resolved' || $status === 'Resolved';
                                                            @endphp
                                                            @if ($status === 0 || $status === 'pending' || $status === 'Pending')
                                                                <span class="status-badge status-pending"
                                                                    style="background: #ffc107; color: #000; padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">Pending</span>
                                                            @elseif ($status === 1 || $status === 'in_progress' || $status === 'In Progress')
                                                                <span class="status-badge"
                                                                    style="background: #cce5ff; color: #004085; border: 1px solid #b3d7ff; padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">In
                                                                    Progress</span>
                                                            @elseif ($status === 'resolved' || $status === 'Resolved')
                                                                <span class="status-badge status-available"
                                                                    style="background: #28a745; color: #fff; padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">Resolved</span>
                                                            @else
                                                                <span class="status-badge"
                                                                    style="background: #6c757d; color: #fff; padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">Book
                                                                    Available</span>
                                                            @endif

                                                            @if (!$isResolved)
                                                                <button type="button" class="btn-message-admin"
                                                                    onclick="event.stopPropagation(); expandAndFocusReply({{ $query->id }});"
                                                                    style="background-color: #007bff; color: #fff; border: none; padding: 6px 14px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: background-color 0.3s; white-space: nowrap;"
                                                                    onmouseover="this.style.backgroundColor='#0056b3'"
                                                                    onmouseout="this.style.backgroundColor='#007bff'"
                                                                    title="Message Admin about this query">
                                                                    <span>💬</span>
                                                                    <span>Message Admin</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>

                                            <div id="{{ $collapseId }}"
                                                class="accordion-collapse collapse {{ $isExpanded ? 'show' : '' }}"
                                                aria-labelledby="{{ $headingId }}"
                                                data-bs-parent="#queriesAccordion">
                                                <div class="accordion-body" style="padding: 20px; background: #fff;">
                                                    <!-- Original Query -->
                                                    <div
                                                        style="background: #f0f7ff; padding: 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #0073aa;">
                                                        <strong
                                                            style="color: #0073aa; display: block; margin-bottom: 8px;">Your
                                                            Original Message:</strong>
                                                        <p style="margin: 0; color: #333; line-height: 1.6;">
                                                            <strong>Book Title:</strong> {{ $query->book_title }}<br>
                                                            @if ($query->author_name)
                                                                <strong>Author:</strong> {{ $query->author_name }}<br>
                                                            @endif
                                                            @if ($query->message)
                                                                <strong>Message:</strong> {{ $query->message }}
                                                            @endif
                                                        </p>
                                                        <small
                                                            style="color: #999; display: block; margin-top: 8px;">{{ $query->created_at->format('F d, Y h:i A') }}</small>
                                                    </div>

                                                    <!-- Admin Reply (if exists in admin_reply field) -->
                                                    @if ($query->admin_reply)
                                                        <div
                                                            style="background: #e8f5e9; padding: 15px; border-radius: 6px; margin-bottom: 15px; border-left: 4px solid #28a745;">
                                                            <strong
                                                                style="color: #28a745; display: block; margin-bottom: 8px;">👨‍💼
                                                                Admin Reply:</strong>
                                                            <p style="margin: 0; color: #333; line-height: 1.6;">
                                                                {{ $query->admin_reply }}</p>
                                                            <small
                                                                style="color: #999; display: block; margin-top: 8px;">{{ $query->updated_at->format('F d, Y h:i A') }}</small>
                                                        </div>
                                                    @endif

                                                    <!-- Conversation Thread (Replies) -->
                                                    @if (isset($query->replies) && $query->replies && $query->replies->count() > 0)
                                                        <div style="margin-bottom: 15px;">
                                                            <strong
                                                                style="color: #333; display: block; margin-bottom: 10px; font-size: 14px;">Conversation
                                                                Thread:</strong>
                                                            <div
                                                                style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                                                                @foreach ($query->replies as $reply)
                                                                    @if ($reply->reply_by == 'admin')
                                                                        <div
                                                                            style="background: #e8f5e9; padding: 12px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #28a745;">
                                                                            <strong
                                                                                style="color: #28a745; display: block; margin-bottom: 5px; font-size: 13px;">👨‍💼
                                                                                Admin:</strong>
                                                                            <p
                                                                                style="margin: 0; color: #333; line-height: 1.6; font-size: 14px;">
                                                                                {{ $reply->message }}</p>
                                                                            <small
                                                                                style="color: #999; display: block; margin-top: 5px; font-size: 12px;">{{ $reply->created_at->format('F d, Y h:i A') }}</small>
                                                                        </div>
                                                                    @else
                                                                        <div
                                                                            style="background: #e3f2fd; padding: 12px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #2196f3;">
                                                                            <strong
                                                                                style="color: #2196f3; display: block; margin-bottom: 5px; font-size: 13px;">👤
                                                                                You:</strong>
                                                                            <p
                                                                                style="margin: 0; color: #333; line-height: 1.6; font-size: 14px;">
                                                                                {{ $reply->message }}</p>
                                                                            <small
                                                                                style="color: #999; display: block; margin-top: 5px; font-size: 12px;">{{ $reply->created_at->format('F d, Y h:i A') }}</small>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Reply Form or Resolved Message -->
                                                    @php
                                                        $status = is_numeric($query->status)
                                                            ? (int) $query->status
                                                            : $query->status;
                                                        $isResolved = $status === 'resolved' || $status === 'Resolved';
                                                    @endphp
                                                    @if ($isResolved)
                                                        <div
                                                            style="background: #d4edda; padding: 20px; border-radius: 6px; border-left: 4px solid #28a745; text-align: center;">
                                                            <div style="font-size: 48px; margin-bottom: 15px;">✅
                                                            </div>
                                                            <h5
                                                                style="color: #155724; margin-bottom: 10px; font-weight: 600;">
                                                                Query Resolved Successfully!</h5>
                                                            <p style="margin: 0; color: #155724; font-size: 16px;">
                                                                Admin has successfully resolved your query. If you
                                                                have any further questions, please feel free to
                                                                submit a new query.</p>
                                                        </div>
                                                    @else
                                                        <!-- Reply Form - Always visible unless resolved -->
                                                        <div
                                                            style="background: white; padding: 20px; border-radius: 6px; border: 2px solid #e5e5e5; margin-top: 15px;">
                                                            <h5
                                                                style="margin-bottom: 15px; color: #333; display: flex; align-items: center;">
                                                                <span style="margin-right: 10px;">💬</span>
                                                                @if (
                                                                    $query->admin_reply ||
                                                                        (isset($query->replies) && $query->replies && $query->replies->where('reply_by', 'admin')->count() > 0))
                                                                    Reply to Admin
                                                                @else
                                                                    Add a Message or Follow-up
                                                                @endif
                                                            </h5>
                                                            <form action="{{ route('user.book.reply', $query->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div
                                                                    class="woocommerce-form-row woocommerce-form-row--wide">
                                                                    <label
                                                                        style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">
                                                                        Your Message <span class="required"
                                                                            style="color: #e74c3c;">*</span>
                                                                    </label>
                                                                    <textarea name="message" id="replyTextarea{{ $query->id }}" rows="5" class="woocommerce-form"
                                                                        style="width: 100%; padding: 12px; border: 2px solid #e5e5e5; border-radius: 4px; font-size: 16px; resize: vertical;"
                                                                        required minlength="10"
                                                                        placeholder="Type your message or reply here... You can add additional information, ask questions, or provide more details about your book request."></textarea>
                                                                    @error('message')
                                                                        <small
                                                                            style="color: #e74c3c; display: block; margin-top: 5px;">{{ $message }}</small>
                                                                    @enderror
                                                                    <small
                                                                        style="color: #666; display: block; margin-top: 5px;">
                                                                        Minimum 10 characters required
                                                                    </small>
                                                                </div>
                                                                <div class="woocommerce-form-row"
                                                                    style="margin-top: 15px; display: flex; gap: 10px;">
                                                                    <button type="submit" class="woocommerce-Button"
                                                                        style="background-color: #cf8938; color: #fff; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; transition: background-color 0.3s;">
                                                                        <span style="margin-right: 5px;">📤</span>
                                                                        Send Message
                                                                    </button>
                                                                    @if (
                                                                        !$query->admin_reply &&
                                                                            (!isset($query->replies) || !$query->replies || $query->replies->where('reply_by', 'admin')->count() == 0))
                                                                        <div
                                                                            style="flex: 1; padding: 12px; background: #fff3cd; border-radius: 4px; border-left: 4px solid #ffc107;">
                                                                            <small
                                                                                style="color: #856404; display: block;">
                                                                                <strong>ℹ️ Note:</strong> Your query is
                                                                                being reviewed. You can still add
                                                                                additional messages or provide more
                                                                                details.
                                                                            </small>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function expandAndFocusReply(queryId) {
        // Find the accordion button and collapse element
        const headingId = 'heading' + queryId;
        const collapseId = 'collapse' + queryId;
        const accordionButton = document.getElementById(headingId)?.querySelector('.accordion-button');
        const collapseElement = document.getElementById(collapseId);
        const textareaId = 'replyTextarea' + queryId;

        if (!accordionButton || !collapseElement) {
            return;
        }

        // Check if the accordion is collapsed
        const isCollapsed = accordionButton.classList.contains('collapsed');

        if (isCollapsed) {
            // Expand the accordion
            accordionButton.classList.remove('collapsed');
            accordionButton.setAttribute('aria-expanded', 'true');
            collapseElement.classList.add('show');

            // Wait for the collapse animation to complete, then focus
            setTimeout(function() {
                scrollToReplyForm(textareaId);
            }, 350); // Bootstrap collapse animation duration
        } else {
            // Already expanded, just scroll and focus
            scrollToReplyForm(textareaId);
        }
    }

    function scrollToReplyForm(textareaId) {
        const textarea = document.getElementById(textareaId);
        if (textarea) {
            // Scroll to the textarea with some offset
            textarea.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Focus on the textarea after a short delay
            setTimeout(function() {
                textarea.focus();
            }, 100);
        }
    }
</script>
