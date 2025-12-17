@extends('front.layout.layout3')
@section('content')
<div class="page-content">
    <!-- inner page banner -->
    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h1>Privacy Policy</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-row">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}"> Home</a></li>
                        <li class="breadcrumb-item">Privacy Policy</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <!-- inner page banner End-->
    
    <!-- contact area -->
    <section class="content-inner-1 shop-account">
        <div class="container">
            <div class="row">
                <!-- Left part start -->
                <div class="col-lg-8 col-md-7 col-sm-12 inner-text">
                    <h4 class="title">The BookHub Privacy Policy was updated on 10 November 2025.</h4>
                    <p class="m-b30">
                        Welcome to <strong>BookHub</strong>. We respect your privacy and are committed to protecting your personal information.
                        This Privacy Policy explains how we collect, use, and safeguard your information when you visit or interact with
                        our platform, whether through our website or mobile interface.  
                        If you have any questions or concerns about this policy, please
                        <a href="{{ url('/contact') }}">contact us</a>.
                    </p>
                
                    <div class="dlab-divider bg-gray-dark"></div>
                
                    <h4 class="title">Who We Are and What This Policy Covers</h4>
                    <p class="m-b30">
                        BookHub is an online platform designed to connect readers, authors, and educational institutions.  
                        We provide a digital environment for discovering, reading, and sharing books and learning materials.
                        This Privacy Policy covers how BookHub handles information collected through our website, mobile applications,
                        and any related services offered under the BookHub brand.
                    </p>
                
                    <h4 class="title">What Personal Information We Collect</h4>
                    <ul class="list-check primary m-a0">
                        <li>
                            <strong>Account Information:</strong> When you create an account, we collect your name, email address,
                            and password to identify you and manage your profile.
                        </li>
                        <li>
                            <strong>Usage Data:</strong> We collect information about how you use the platform — such as pages visited,
                            books read, or searches performed — to improve user experience and platform performance.
                        </li>
                        <li>
                            <strong>Content Uploads:</strong> If you upload or publish books, notes, or other materials,
                            we may collect related metadata (e.g., file name, upload date, author details).
                        </li>
                        <li>
                            <strong>Communication Data:</strong> When you contact us or interact with authors/readers,
                            we collect messages or support inquiries for customer service purposes.
                        </li>
                        <li>
                            <strong>Cookies and Analytics:</strong> We use cookies and analytics tools to understand visitor behavior
                            and optimize the platform’s performance and security.
                        </li>
                    </ul>
                
                    <div class="dlab-divider bg-gray-dark"></div>
                
                    <h4 class="title">How We Use Your Information</h4>
                    <p class="m-b30">
                        The data we collect is used to:
                    </p>
                    <ul class="list-check primary m-a0">
                        <li>Provide and maintain access to your BookHub account and library.</li>
                        <li>Personalize book recommendations and improve your reading experience.</li>
                        <li>Notify you about updates, promotions, or new features (only with your consent).</li>
                        <li>Monitor usage to detect and prevent unauthorized or harmful activities.</li>
                        <li>Comply with legal obligations and platform policies.</li>
                    </ul>
                
                    <div class="dlab-divider bg-gray-dark"></div>
                
                    <h4 class="title">Data Protection and Security</h4>
                    <p class="m-b30">
                        We implement appropriate technical and organizational measures to protect your personal information.
                        This includes encryption, access controls, and regular security audits to prevent unauthorized access,
                        disclosure, or misuse of your data.
                    </p>
                
                    <h4 class="title">Your Rights and Choices</h4>
                    <ul class="list-check primary m-a0">
                        <li>Access and update your personal information in your account settings.</li>
                        <li>Request deletion of your account and associated data at any time.</li>
                        <li>Opt-out of non-essential email communications or newsletters.</li>
                        <li>Request a copy of the data we store about you (subject to verification).</li>
                    </ul>
                
                    <div class="dlab-divider bg-gray-dark"></div>
                
                    <h4 class="title">Policy Updates</h4>
                    <p class="m-b30">
                        BookHub may update this Privacy Policy from time to time to reflect changes in our services or legal requirements.
                        The latest version will always be available on this page with an updated “Last Modified” date.
                        We encourage you to review it regularly to stay informed about how we protect your information.
                    </p>
                
                    <p class="m-b0">
                        <strong>Effective Date:</strong> 10 November 2025<br>
                        <strong>Contact Email:</strong> <a href="mailto:support@bookhub.com">support@bookhub.com</a>
                    </p>
                </div>
                
                <div class="col-lg-4 col-md-5 col-sm-12 m-b30 mt-md-0 mt-4">
                    <aside class="side-bar sticky-top right">
                        <div class="service_menu_nav widget style-1">
                            <ul class="menu">
                                <li class="menu-item"><a href="{{ url('/about') }}">About Us</a></li>
                                <li class="menu-item active"><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                                <li class="menu-item "><a href="{{ url('/help-desk') }}">Help Desk</a></li>
                                <li class="menu-item"><a href="{{ url('/contact') }}">Contact Us</a></li>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </section>
    <!-- Privacy Policy END -->
</div>

@endsection