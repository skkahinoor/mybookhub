@include('user.layout.header')

<style>
    .raise-query-page .card {
        border: 1px solid #e8edf5;
        border-radius: 14px;
        box-shadow: 0 6px 20px rgba(21, 38, 67, 0.06);
    }
    .raise-query-page .hero {
        border-radius: 12px;
        padding: 18px 20px;
        background: linear-gradient(135deg, #fff8ef 0%, #f6f9ff 100%);
        border: 1px solid #f0e5d6;
        margin-bottom: 18px;
    }
    .raise-query-page .hero h4 {
        font-size: 22px;
        font-weight: 700;
        color: #1f2d3d;
        margin-bottom: 5px;
    }
    .raise-query-page .hero p {
        margin: 0;
        color: #64748b;
    }
    .raise-query-page .submit-btn {
        background: #cf8938;
        border: none;
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        padding: 10px 20px;
    }
</style>

<div class="container-fluid page-body-wrapper">
    @include('user.layout.sidebar')

    <div class="main-panel raise-query-page">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <div class="hero d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                                <div>
                                    <h4>Raise Your Query</h4>
                                    <p>Fill details and select a vendor by your pincode.</p>
                                </div>
                                <a href="{{ route('student.query.index') }}" class="btn btn-outline-primary" style="border-radius:8px;">
                                    View My Queries
                                </a>
                            </div>

                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form action="{{ route('student.book.request.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Book Title <span class="text-danger">*</span></label>
                                            <input type="text" name="book_title" class="form-control" value="{{ old('book_title') }}" required>
                                            @error('book_title')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Author Name</label>
                                            <input type="text" name="author_name" class="form-control" value="{{ old('author_name') }}">
                                            @error('author_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Publisher Name</label>
                                            <input type="text" name="publisher_name" class="form-control" value="{{ old('publisher_name') }}">
                                            @error('publisher_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select Vendor <span class="text-danger">*</span></label>
                                            @if (empty(Auth::user()->pincode))
                                                <div class="alert alert-warning mb-0">Please update your profile pincode first to see available vendors.</div>
                                            @elseif (isset($matchingVendors) && $matchingVendors->count() > 0)
                                                <select name="vendor_id" class="form-control" required>
                                                    <option value="">Select vendor in your pincode</option>
                                                    @foreach ($matchingVendors as $vendor)
                                                        <option value="{{ $vendor->id }}" {{ (string) old('vendor_id') === (string) $vendor->id ? 'selected' : '' }}>
                                                            {{ $vendor->vendorbusinessdetails->shop_name ?? $vendor->user->name ?? ('Vendor #' . $vendor->id) }}
                                                            ({{ $vendor->vendorbusinessdetails->shop_pincode ?? (Auth::user()->pincode ?? 'N/A') }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <div class="alert alert-danger mb-0">No vendors found for your pincode ({{ Auth::user()->pincode }}).</div>
                                            @endif
                                            @error('vendor_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Your Message <span class="text-danger">*</span></label>
                                    <textarea name="message" rows="5" class="form-control" required minlength="10"
                                        placeholder="Type your message here... Provide details about the book you're looking for.">{{ old('message') }}</textarea>
                                    <small class="text-muted">Minimum 10 characters required</small>
                                    @error('message')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                @php
                                    $canSubmitQuery = !empty(Auth::user()->pincode) && isset($matchingVendors) && $matchingVendors->count() > 0;
                                @endphp
                                <button type="submit" class="btn submit-btn" {{ $canSubmitQuery ? '' : 'disabled' }} style="{{ $canSubmitQuery ? '' : 'opacity:0.65;cursor:not-allowed;' }}">
                                    📤 Submit Query
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.layout.footer')
