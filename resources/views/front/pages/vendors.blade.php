@extends('front.layout.layout3')

@section('content')

{{-- Hero / Intro --}}
<section class="py-5" style="background-color:#f3f6fb;">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-7">
                <h1 class="fw-bold mb-3">{{ __('Vendor Onboarding – Start Selling Your Books on BookHub') }}</h1>
                <p class="lead mb-4">
                    {{ __('List your books on BookHub, reach students and readers across India, and let our team handle marketing and promotion for you. You focus on books, we take care of growth.') }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('vendor.register') }}" class="btn btn-primary btn-lg">
                        {{ __('Register as Vendor') }}
                    </a>
                    <a href="{{ url('/contact') }}" class="btn btn-outline-secondary btn-lg">
                        {{ __('Contact Us for Any Doubt') }}
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end text-center">
                <img src="{{ asset('front/images/vendors/vendorbook.gif') }}"
                     alt="Vendor Onboarding"
                     class="img-fluid rounded shadow-sm"
                     style="width: 80%; height: 80%; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

{{-- Key Benefits --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-semibold">{{ __('Why Sell Your Books on BookHub?') }}</h2>
            <p class="mb-0">
                {{ __('BookHub is built for publishers, authors, bookshops, and institutes who want reach without heavy marketing effort.') }}
            </p>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">{{ __('No Marketing Hassle') }}</h5>
                        <p class="card-text">
                            {{ __('You don’t have to run ads or build your own website. BookHub promotes your books to students, institutes, and readers via our channels for just a 5% promotion fee on sales.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">{{ __('Simple Book & Edition Management') }}</h5>
                        <p class="card-text">
                            {{ __('Add and manage books with multiple editions in a clean vendor dashboard. Update prices, stock, and details whenever you need.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">{{ __('Pay Only When You Sell') }}</h5>
                        <p class="card-text">
                            {{ __('Instead of big upfront marketing budgets, pay only a 5% promotion fee on successful orders. No hidden charges, no complicated contracts.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">{{ __('Reach Ready-Made Audience') }}</h5>
                        <p class="card-text">
                            {{ __('BookHub already works with students, parents, institutes, and coaching centers. Your books appear in front of a relevant audience from day one.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold">{{ __('Transparent Vendor Panel') }}</h5>
                        <p class="card-text">
                            {{ __('Track orders, earnings, and book performance in one place. Download reports and understand what is selling without technical complexity.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Simple Flow --}}
<section class="py-5" style="background-color:#f8fafc;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-semibold">{{ __('How Vendor Onboarding Works') }}</h2>
            <p class="mb-0">
                {{ __('A short, guided process to get your catalog live on BookHub.') }}
            </p>
        </div>

        <div class="row row-cols-1 row-cols-md-4 g-4 text-center">
            <div class="col">
                <div class="h-100 d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3" style="width:52px;height:52px;">
                        1
                    </div>
                    <h6 class="fw-semibold mb-2">{{ __('Register') }}</h6>
                    <p class="small mb-0">
                        {{ __('Fill basic vendor details and complete KYC so we can verify your account.') }}
                    </p>
                </div>
            </div>
            <div class="col">
                <div class="h-100 d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3" style="width:52px;height:52px;">
                        2
                    </div>
                    <h6 class="fw-semibold mb-2">{{ __('Add Books & Editions') }}</h6>
                    <p class="small mb-0">
                        {{ __('Add titles, upload covers, set prices, and configure different editions easily.') }}
                    </p>
                </div>
            </div>
            <div class="col">
                <div class="h-100 d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3" style="width:52px;height:52px;">
                        3
                    </div>
                    <h6 class="fw-semibold mb-2">{{ __('BookHub Promotes') }}</h6>
                    <p class="small mb-0">
                        {{ __('We promote your catalog to our users. No extra marketing work from your side.') }}
                    </p>
                </div>
            </div>
            <div class="col">
                <div class="h-100 d-flex flex-column align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mb-3" style="width:52px;height:52px;">
                        4
                    </div>
                    <h6 class="fw-semibold mb-2">{{ __('You Earn') }}</h6>
                    <p class="small mb-0">
                        {{ __('Orders come in, we charge 5% promotion fee, and you receive the remaining amount.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Pricing Plans --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-semibold">{{ __('Vendor Plans') }}</h2>
            <p class="mb-0">
                {{ __('Start with the Free plan and move to Pro when your catalog grows. Both plans include our promotion system.') }}
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Free Plan --}}
            <div class="col-md-5">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <h5 class="fw-semibold mb-1">{{ __('Free Plan') }}</h5>
                        <p class="mb-3">
                            {{ __('Ideal for testing BookHub or small catalogs.') }}
                        </p>

                        <h2 class="mb-3">₹0 <span class="fs-6">/ {{ __('month') }}</span></h2>

                        <ul class="list-unstyled mb-4">
                            <li class="mb-2">
                                <i class="fa fa-check text-success me-2"></i>
                                {!! __('Add up to <b>100 books per month</b>.') !!}
                            </li>
                            <li class="mb-2">
                                <i class="fa fa-check text-success me-2"></i>
                                {{ __('Add and manage multiple editions for each book.') }}
                            </li>
                            <li class="mb-2">
                                <i class="fa fa-times text-danger me-2"></i>
                                {!! __('Coupons are <b>not available</b> in Free plan.') !!}
                            </li>
                            <li class="mb-2">
                                <i class="fa fa-check text-success me-2"></i>
                                {!! __('BookHub marketing and promotion with only <b>5% promotion fee</b> on successful orders.') !!}
                            </li>
                            <li class="mb-2">
                                <i class="fa fa-check text-success me-2"></i>
                                {{ __('Vendor dashboard to track orders and earnings.') }}
                            </li>
                        </ul>

                        <div class="mt-auto">
                            <a href="{{ route('vendor.register') }}" class="btn btn-outline-primary w-100">
                                {{ __('Start with Free Plan') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pro Plan --}}
            <div class="col-md-5">
                <div class="card h-100 shadow border-primary">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-semibold mb-0">{{ __('Pro Plan') }}</h5>
                            <span class="badge bg-primary text-white">{{ __('Recommended') }}</span>
                        </div>
                        <p class="mb-3">
                            {{ __('For serious vendors and publishers with growing sales.') }}
                        </p>

                        <h2 class="mb-3 text-primary">₹499 <span class="fs-6">/ {{ __('month') }}</span></h2>

                        <ul class="list-unstyled mb-4">
                            <li class="mb-2">
                                <i class="fa fa-check text-success me-2"></i>
                                {!! __('<b>Unlimited</b> book uploads per month.') !!}
                            </li>
                            <li class="mb-2">
                                <i class="fa fa-check text-success me-2"></i>
                                {!! __('<b>Unlimited coupons</b> for discounts, campaigns, and special offers.') !!}
                            </li>
                            <li class="mb-2">
                                <i class="fa fa-check text-success me-2"></i>
                                {{ __('Full edition management and flexible catalog organization.') }}
                            </li>
                            <li class="mb-2">
                                <i class="fa fa-check text-success me-2"></i>
                                {!! __('BookHub marketing and promotion with the same <b>5% promotion fee</b> on successful orders.') !!}
                            </li>
                            <li class="mb-2">
                                <i class="fa fa-check text-success me-2"></i>
                                {{ __('Better visibility, highlighting, and priority support for vendors.') }}
                            </li>
                        </ul>

                        <div class="mt-auto">
                            <a href="{{ route('vendor.register') }}" class="btn btn-primary w-100">
                                {{ __('Upgrade to Pro – ₹499 / month') }}
                            </a>
                            <small class="d-block mt-2 ">
                                {{ __('No long-term lock-in. Upgrade or change plan anytime.') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Strong 5% Promotion Highlight --}}
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <h2 class="fw-semibold mb-3">{{ __('The Biggest Advantage: 5% Promotion Fee Only') }}</h2>
                <p class="mb-2">
                    {{ __('Instead of spending on separate ads, influencers, campaigns, or building your own e-commerce platform, you simply pay a small 5% promotion fee on each successful order.') }}
                </p>
                <p class="mb-3">
                    {{ __('This keeps your risk low and your reach high—BookHub continuously promotes your books to the right audience, while you focus on publishing and inventory.') }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end text-center">
                <a href="{{ route('vendor.register') }}" class="btn btn-light btn-lg mb-2">
                    {{ __('Register as Vendor') }}
                </a>
                <div>
                    <a href="{{ url('/contact') }}" class="btn btn-outline-light">
                        {{ __('Any Questions? Contact Us') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-semibold">{{ __('Vendor FAQs') }}</h2>
            <p class="mb-0">
                {{ __('Quick answers before you start your vendor journey.') }}
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="accordion" id="vendorFaqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                {{ __('Who can become a BookHub vendor?') }}
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#vendorFaqAccordion">
                            <div class="accordion-body">
                                {{ __('Publishers, authors, bookstores, institutes, coaching centers, or any organization that wants to sell books through BookHub can register as a vendor.') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                {{ __('What is the difference between Free and Pro plan?') }}
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#vendorFaqAccordion">
                            <div class="accordion-body">
                                {{ __('The Free plan allows you to upload up to 100 books per month and does not allow coupon creation. The Pro plan (₹499/month) gives you unlimited book uploads and unlimited coupons with no upload limits.') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                {{ __('Do I have to do my own marketing?') }}
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#vendorFaqAccordion">
                            <div class="accordion-body">
                                {{ __('No. BookHub handles marketing and promotion. You only pay a 5% promotion fee on successful orders instead of managing separate marketing campaigns.') }}
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faqFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                {{ __('Can I switch plans later?') }}
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#vendorFaqAccordion">
                            <div class="accordion-body">
                                {{ __('Yes. You can start with the Free plan and upgrade to Pro as your catalog and sales grow. Plan changes can be handled from your vendor panel.') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('vendor.register') }}" class="btn btn-primary me-2">
                        {{ __('Register as Vendor') }}
                    </a>
                    <a href="{{ url('/contact') }}" class="btn btn-outline-primary">
                        {{ __('Still Have Doubts? Contact Us') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
