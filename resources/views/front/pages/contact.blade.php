{{-- This page is rendered by contact() method inside Front/CmsController.php --}}
@extends('front.layout.layout3')

@section('content')
<!-- Page Introduction Wrapper -->
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-white px-3 py-2">
            <li class="breadcrumb-item">
                <i class="ion ion-md-home"></i>
                <a href="{{ url('/') }}" class="text-decoration-none">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
        </ol>
    </nav>
    <div class="text-center mb-5">
        <h2 class="display-4 font-weight-bold">Contact Us</h2>
    </div>
    <div class="row">
        <!-- Contact form -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="mb-4">Get In Touch With Us</h3>
                    @if (Session::has('error_message'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error:</strong> {{ Session::get('error_message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {!! implode('', $errors->all('<div>:message</div>')) !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (Session::has('success_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success:</strong> {{ Session::get('success_message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ url('contact') }}" method="post" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="contact-name" class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" id="contact-name" class="form-control" name="name" placeholder="Name" value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label for="contact-email" class="form-label">Your Email <span class="text-danger">*</span></label>
                            <input type="email" id="contact-email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}">
                        </div>
                        <div class="mb-3">
                            <label for="contact-subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" id="contact-subject" class="form-control" name="subject" placeholder="Subject" value="{{ old('subject') }}">
                        </div>
                        <div class="mb-3">
                            <label for="contact-message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea id="contact-message" class="form-control" name="message" rows="5" placeholder="Your message here...">{{ old('message') }}</textarea>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Contact info -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h3 class="mb-3">Information About Us</h3>
                    <p>BookHub is a modern digital platform built to make reading, learning, and knowledge sharing simple and accessible to everyone. Whether you’re a student, teacher, or lifelong learner, BookHub connects you to a vast collection of books and study materials through an easy-to-use online library system.

                    With BookHub, we aim to empower readers and authors by providing anengaging space to explore, publish, and collaborate. We believe inthe transformative power of books and the importance of connectingcommunities through knowledge.</p>
                    <hr>
                    <h5 class="mt-4 mb-3">Contact Details</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <span class="ms-2">
                                Plot No-325, Baramunda ISBT,
                                Above MRF Tyre Showroom
                                Bhubaneswar-751003</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope text-primary"></i>
                            <span class="ms-2">support@srdcindia.co.in</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt text-primary"></i>
                            <span class="ms-2">+91 (0) 84807 46394, +91 (0) 84807 46395</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Map section -->
    <div class="mt-5">
        <div id="map" style="width: 100%; height: 350px; border-radius:0.5rem; box-shadow:0 0 15px rgba(0,0,0,0.1);"></div>
    </div>
</div>
@endsection
