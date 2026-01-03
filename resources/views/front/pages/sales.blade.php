@extends('front.layout.layout3')

@section('content')
<!-- Banner -->
<div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/sales-banner.jpg);">
    <div class="container">
        <div class="dz-bnr-inr-entry text-center">
            <h1>Become a BookHub Sales Executive</h1>
            <p class="lead">Earn Commissions | Flexible Hours | Unlimited Growth</p>
            <a href="{{ route('sales.register') }}" class="btn btn-primary btn-lg mt-3">Start Earning - Join Now</a>
        </div>
    </div>
</div>

<!-- About Sales Executive Opportunity -->
<section class="content-inner py-5 bg-light">
    <div class="container">
        <div class="row align-items-center gx-5">
            <div class="col-md-6 mb-4 mb-md-0">
                <img src="{{ asset('front/images/sales/sales.png') }}" alt="Sales Executive Opportunity" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-6">
                <div class="section-head style-1">
                    <h2>Why Join as a BookHub Sales Executive?</h2>
                    <p>
                        BookHub is opening doors for enthusiastic individuals to earn rewarding incomes by joining our dynamic Sales Executive community. Whether you're a student, homemaker, professional, or just looking for an extra income, <b>anyone</b> can join us and start making money instantly!
                    </p>
                    <ul class="list-check primary mt-3">
                        <li>Work part-time or full-time—<b>your schedule, your choice</b></li>
                        <li>Simple and impactful role: enroll students from various institutes and communities</li>
                        <li><b>Commission-based earnings:</b> Earn for every successful enrollment</li>
                        <li><b>No prior sales experience required</b>—training & support included</li>
                        <li>Incentives for top performers and growth opportunities within BookHub</li>
                    </ul>
                </div>
                <a href="{{ url('/contact') }}" class="btn btn-outline-primary mt-4">Connect for More Info</a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="content-inner-1 py-5">
    <div class="container">
        <div class="section-head text-center mb-5">
            <h2>How Does It Work?</h2>
            <p>It's simple! Just follow the blocks below and start earning:</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="icon-bx-wraper style-3 text-center p-4 h-100 shadow">
                    <i class="flaticon-employee icon-lg mb-3"></i>
                    <h5>Step 1: Register</h5>
                    <p>Sign up online—quick approval and easy onboarding for everyone.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="icon-bx-wraper style-3 text-center p-4 h-100 shadow">
                    <i class="flaticon-presentation icon-lg mb-3"></i>
                    <h5>Step 2: Reach Out</h5>
                    <p>Contact students and institutes in your network or area using our tools and training.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="icon-bx-wraper style-3 text-center p-4 h-100 shadow">
                    <i class="flaticon-checklist icon-lg mb-3"></i>
                    <h5>Step 3: Enroll Students</h5>
                    <p>Help students register for BookHub, guiding them through the simple process.</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="icon-bx-wraper style-3 text-center p-4 h-100 shadow">
                    <i class="flaticon-dollar icon-lg mb-3"></i>
                    <h5>Step 4: Earn Commission</h5>
                    <p>Receive payments directly for every successful enrollment—no upper earning limits!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="content-inner py-5 bg-primary text-white text-center">
    <div class="container">
        <h2>Start Your Earning Journey with BookHub Today</h2>
        <p class="lead">Flexible. Rewarding. Impactful. Empower yourself while empowering education in your community.</p>
        <a href="{{ url('/register-sales') }}" class="btn btn-light btn-lg mt-3">Register as Sales Executive</a>
    </div>
</section>

<!-- FAQ & Benefits -->
<section class="content-inner py-5">
    <div class="container">
        <div class="section-head text-center mb-5">
            <h2>Frequently Asked Questions</h2>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h5 class="accordion-header" id="faq1"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">Who can become a Sales Executive?</button></h5>
                        <div id="collapse1" class="accordion-collapse collapse show">
                            <div class="accordion-body">Anyone above 18 years can join, including students, homemakers, professionals, or part-timers.</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h5 class="accordion-header" id="faq2"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">How much can I earn?</button></h5>
                        <div id="collapse2" class="accordion-collapse collapse">
                            <div class="accordion-body">Earnings are commission-based. The more students you enroll, the higher your income—no capping!</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h5 class="accordion-header" id="faq3"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">Is there any fee to join?</button></h5>
                        <div id="collapse3" class="accordion-collapse collapse">
                            <div class="accordion-body">Zero joining fees. Start for free!</div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h5 class="accordion-header" id="faq4"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">Do I need sales experience?</button></h5>
                        <div id="collapse4" class="accordion-collapse collapse">
                            <div class="accordion-body">No sales experience required—BookHub provides full training and marketing support.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
