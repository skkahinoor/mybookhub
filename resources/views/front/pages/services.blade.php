@extends('front.layout.layout3')
@section('content')
<div class="page-content">
    <!-- inner page banner -->
    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h1>Services</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-row">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}"> Home</a></li>
                        <li class="breadcrumb-item">Services</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- inner page banner End-->
    <!-- Services -->
    <section class="content-inner bg-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="content-box style-1 m-b30">
                        <div class="dz-info">
                            <h4 class="title">24*7 Support</h4>
                            <p>Our 24/7 support team is always here to help you with any questions or issues you may have. We are available to assist you with your account, billing, or any other questions you may have.</p>
                        </div>
                        <div class="dz-banner-media1.jpg m-b30">
                            <img src="images/services/service2.jpg" alt="">
                        </div>
                        <div class="dz-bottom">
                            <a href="{{ url('/services') }}" class="btn-link btnhover3">READ MORE<i class="fas fa-arrow-right m-l10"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="content-box style-1 m-b30">
                        <div class="dz-info">
                            <h4 class="title">Sitting Arrangement</h4>
                            <p>We offer a wide range of sitting arrangements to choose from, including traditional seating, group seating, and private seating. We also offer a variety of seating options to choose from, including traditional seating, group seating, and private seating.</p>
                        </div>
                        <div class="dz-banner-media1.jpg m-b30">
                            <img src="images/services/service1.jpg" alt="">
                        </div>
                        <div class="dz-bottom">
                            <a href="{{ url('/services') }}" class="btn-link btnhover3">READ MORE<i class="fas fa-arrow-right m-l10"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="content-box style-1 m-b30">
                        <div class="dz-info">
                            <h4 class="title">Proper Management</h4>
                            <p>We have a team of experienced professionals who are dedicated to providing you with the best possible service. We are available to assist you with your account, billing, or any other questions you may have.</p>
                        </div>
                        <div class="dz-banner-media1.jpg m-b30">
                            <img src="images/services/service3.jpg" alt="">
                        </div>
                        <div class="dz-bottom">
                            <a href="{{ url('/services') }}" class="btn-link btnhover3">READ MORE<i class="fas fa-arrow-right m-l10"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="content-box style-1 m-b30">
                        <div class="dz-info">
                            <h4 class="title">Online Registration</h4>
                            <p>We offer online registration for our services. You can register for our services online by filling out the form provided on our website.</p>
                        </div>
                        <div class="dz-banner-media1.jpg m-b30">
                            <img src="images/services/service4.jpg" alt="">
                        </div>
                        <div class="dz-bottom">
                            <a href="{{ url('/services') }}" class="btn-link btnhover3">READ MORE<i class="fas fa-arrow-right m-l10"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="content-box style-1 m-b30">
                        <div class="dz-info">
                            <h4 class="title">Download PDF</h4>
                            <p>We offer a variety of PDF downloads for our services. You can download the PDF for our services by clicking the link provided on our website.</p>
                        </div>
                        <div class="dz-banner-media1.jpg m-b30">
                            <img src="images/services/service5.jpg" alt="">
                        </div>
                        <div class="dz-bottom">
                            <a href="{{ url('/services') }}" class="btn-link btnhover3">READ MORE<i class="fas fa-arrow-right m-l10"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="content-box style-1 m-b30">
                        <div class="dz-info">
                            <h4 class="title">Flexible Timing</h4>
                            <p>We offer a variety of flexible timing for our services. You can choose the timing that best suits your needs.</p>
                        </div>
                        <div class="dz-banner-media1.jpg m-b30">
                            <img src="images/services/service6.jpg" alt="">
                        </div>
                        <div class="dz-bottom">
                            <a href="{{ url('/services') }}" class="btn-link btnhover3">READ MORE<i class="fas fa-arrow-right m-l10"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
