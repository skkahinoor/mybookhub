@include('user.layout.header')

<style>
    .book-query-page .card {
        border: 1px solid #e9edf3;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(26, 43, 72, 0.06);
    }
    .book-query-page .page-hero {
        border-radius: 12px;
        padding: 18px 20px;
        background: linear-gradient(135deg, #fff8ef 0%, #f6f9ff 100%);
        border: 1px solid #f0e5d6;
    }
    .book-query-page .page-hero h1 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 6px;
        color: #1d2a3b;
    }
    .book-query-page .page-hero p {
        margin: 0;
        color: #5e6b7a;
    }
    .book-query-page .accent-btn {
        background: #cf8938 !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
    }
    .book-query-page .accordion-item {
        border: 1px solid #e8edf5 !important;
        border-radius: 12px !important;
        overflow: hidden;
    }
    .book-query-page .accordion-button {
        background: #f8fbff !important;
    }
    .book-query-page .accordion-button:not(.collapsed) {
        background: #eef5ff !important;
        box-shadow: none;
    }
    .book-query-page .query-head-content {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .book-query-page .query-title {
        color: #1f2937;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }
    .book-query-page .query-meta {
        color: #64748b;
        font-size: 12px;
    }
    .book-query-page .status-chip {
        display: inline-block;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }
    .book-query-page .status-chip.pending {
        background: #fff3cd;
        color: #8a6d1f;
    }
    .book-query-page .status-chip.progress {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .book-query-page .status-chip.resolved {
        background: #dcfce7;
        color: #166534;
    }
    .book-query-page .message-admin-btn {
        background-color: #2563eb;
        color: #fff;
        border: none;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }
    .book-query-page .query-info-box {
        background: #f0f7ff;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #0ea5e9;
    }
    .book-query-page .reply-box {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        margin-top: 15px;
    }
</style>

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel book-query-page">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="woocommerce-account-header page-hero"
                                style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                                <div>
                                    <h1>My Book Request
                                        Queries</h1>
                                    <p>View the status and replies for all your book
                                        request
                                        queries.</p>
                                </div>
                                <a href="{{ route('student.query.raise') }}" class="woocommerce-Button accent-btn"
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
                            @php
                                $queriesToShow = isset($selectedQuery) && $selectedQuery ? collect([$selectedQuery]) : collect();
                            @endphp

                            {{-- Empty state when there are no queries yet --}}
                            @if ($queries->isEmpty())
                                <div class="empty-state" style="text-align: center; padding: 60px 20px;">
                                    <div class="empty-state-icon" style="font-size: 64px; margin-bottom: 20px;">💬</div>
                                    <h3 style="color: #333; margin-bottom: 10px;">No queries yet</h3>
                                    <p style="color: #666; margin-bottom: 10px;">You haven't submitted any book request
                                        queries yet.</p>
                                    <p style="color: #666; margin-bottom: 30px;">Use the form below to raise your first
                                        book query.</p>
                                </div>
                            @elseif (!$selectedQuery)
                                <div class="empty-state" style="text-align: center; padding: 40px 20px;">
                                    <h3 style="color: #333; margin-bottom: 10px;">No query selected</h3>
                                    <p style="color: #666; margin-bottom: 20px;">Open a query from My Book Requests to view details here.</p>
                                </div>
                            @endif


                            {{-- Existing queries accordion list (only when there are queries) --}}
                            @if ($queriesToShow->isNotEmpty())
                                <div class="accordion" id="queriesAccordion" style="margin-top: 20px;">
                                    @foreach ($queriesToShow as $key => $query)
                                        @php
                                            $collapseId = 'collapse' . $query->id;
                                            $headingId = 'heading' . $query->id;
                                            // Handle both numeric and string status values
                                            $statusValue = is_numeric($query->status) ? $query->status : $query->status;
                                            $isResolved = $statusValue === 'resolved' || $statusValue === 'Resolved';
                                            // Expand first query or if status is not resolved, collapse resolved queries by default
                                            $isExpanded = isset($selectedQueryId) && $selectedQueryId > 0
                                                ? ((int) $selectedQueryId === (int) $query->id)
                                                : (($key == 0 && !$isResolved) ||
                                                    ($key == 0 && $isResolved && $queries->count() == 1));
                                        @endphp

                                        <div class="accordion-item" id="query-{{ $query->id }}"
                                            style="border: 1px solid #e5e5e5; border-radius: 8px; margin-bottom: 15px; overflow: hidden;">
                                            <h2 class="accordion-header" id="{{ $headingId }}">
                                                <button class="accordion-button {{ $isExpanded ? '' : 'collapsed' }}"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#{{ $collapseId }}"
                                                    aria-expanded="{{ $isExpanded ? 'true' : 'false' }}"
                                                    aria-controls="{{ $collapseId }}"
                                                    style="background: #f8f9fa; padding: 15px 20px;">
                                                    <div class="query-head-content">
                                                        <div style="flex: 1; min-width: 0;">
                                                            <strong class="query-title">{{ $query->book_title }}</strong>
                                                            <small class="query-meta">Requested on {{ $query->created_at->format('M d, Y h:i A') }}</small>
                                                        </div>
                                                        <div style="margin-left: 15px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end;">
                                                            @php
                                                                // Handle both numeric and string status
                                                                $status = is_numeric($query->status)
                                                                    ? (int) $query->status
                                                                    : $query->status;
                                                                $isResolved =
                                                                    $status === 'resolved' || $status === 'Resolved';
                                                            @endphp
                                                            @if ($status === 0 || $status === 'pending' || $status === 'Pending')
                                                                <span class="status-chip pending">Pending</span>
                                                            @elseif ($status === 1 || $status === 'in_progress' || $status === 'In Progress')
                                                                <span class="status-chip progress">In Progress</span>
                                                            @elseif ($status === 'resolved' || $status === 'Resolved')
                                                                <span class="status-chip resolved">Resolved</span>
                                                            @else
                                                                <span class="status-badge"
                                                                    style="background: #6c757d; color: #fff; padding: 5px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">Book
                                                                    Available</span>
                                                            @endif

                                                            {{-- @if (!$isResolved)
                                                                <button type="button" class="message-admin-btn"
                                                                    onclick="event.stopPropagation(); expandAndFocusReply({{ $query->id }});"
                                                                    title="Message Admin about this query">
                                                                    <span>💬</span>
                                                                    <span>Message Admin</span>
                                                                </button>
                                                            @endif --}}
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
                                                    <div class="query-info-box">
                                                        <strong
                                                            style="color: #0073aa; display: block; margin-bottom: 8px;">Your
                                                            Original Message:</strong>
                                                        <p style="margin: 0; color: #333; line-height: 1.6;">
                                                            <strong>Book Title:</strong> {{ $query->book_title }}<br>
                                                            @if ($query->author_name)
                                                                <strong>Author:</strong> {{ $query->author_name }}<br>
                                                            @endif
                                                            @if ($query->publisher_name)
                                                                <strong>Publisher:</strong> {{ $query->publisher_name }}<br>
                                                            @endif
                                                            @if ($query->vendor)
                                                                <strong>Requested Vendor:</strong>
                                                                {{ $query->vendor->vendorbusinessdetails->shop_name ?? $query->vendor->user->name ?? ('Vendor #' . $query->vendor->id) }}<br>
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
                                                        <div class="reply-box">
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
                                                            <form action="{{ route('student.book.reply', $query->id) }}"
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
    document.addEventListener('DOMContentLoaded', function() {
        const selectedQueryId = @json($selectedQueryId ?? 0);
        if (selectedQueryId) {
            const target = document.getElementById('query-' + selectedQueryId);
            if (target) {
                setTimeout(function() {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 200);
            }
        }
    });

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
