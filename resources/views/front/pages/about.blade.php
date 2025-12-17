@extends('front.layout.layout3')

@section('content')
    <!--banner-->
    <div class="dz-bnr-inr overlay-secondary-dark dz-bnr-inr-sm" style="background-image:url(images/background/bg3.jpg);">
        <div class="container">
            <div class="dz-bnr-inr-entry">
                <h1>About Us</h1>
                <nav aria-label="breadcrumb" class="breadcrumb-row">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active">About Us</li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!--Our Mission Section-->
    <section class="content-inner overlay-white-middle">
        <div class="container">
            <div class="row about-style1 align-items-center">
                <div class="col-lg-6 m-b30">
                    <div class="row sp10 about-thumb">
                        <div class="col-sm-6 aos-item" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                            <div class="split-box">
                                <div>
                                    <img class="m-b30" src="images/about/about1.jpg" alt="">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="split-box ">
                                <div>
                                    <img class="m-b20 aos-item" src="images/about/about2.jpg" alt="" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
                                </div>
                            </div>
                            <div class="exp-bx aos-item" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
                                <div class="exp-head">
                                    <div class="counter-num">
                                        <h2><span class="counter">10</span><small>k+</small></h2>
                                    </div>
                                    <h6 class="title">Active Readers</h6>
                                </div>
                                <div class="exp-info">
                                    <ul class="list-check primary">
                                        <li>Quality Learning Resources</li>
                                        <li>Trusted Authors & Publishers</li>
                                        <li>Community-driven Reading</li>
                                        <li>Personalized Book Recommendations</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 m-b30 aos-item" data-aos="fade-up" data-aos-duration="800" data-aos-delay="500">
                    <div class="about-content px-lg-4">
                        <div class="section-head style-1">
                            <h2 class="title">About BookHub</h2>
                            <p>
                                <strong>BookHub</strong> is a modern digital platform built to make reading, learning, 
                                and knowledge sharing simple and accessible to everyone. Whether you’re a student, 
                                teacher, or lifelong learner, BookHub connects you to a vast collection of books and 
                                study materials through an easy-to-use online library system.
                            </p>
                            <p>
                                With BookHub, we aim to empower readers and authors by providing an engaging space to 
                                explore, publish, and collaborate. We believe in the transformative power of books 
                                and the importance of connecting communities through knowledge.
                            </p>
                        </div>
                        <a href="{{ url('/contact') }}" class="btn btn-primary btnhover shadow-primary">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--icon-box3 section-->
    <section class="content-inner-1 bg-light">
        <div class="container">
            <div class="section-head text-center">
                <h2 class="title">Our Mission</h2>
                <p>
                    Our mission is to make learning resources and books available to everyone, anytime, anywhere.
                    BookHub bridges the gap between readers, authors, and educational institutions by building a
                    digital environment that promotes curiosity, creativity, and collaboration.
                </p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="icon-bx-wraper style-3 m-b30">
                        <div class="icon-lg m-b20">
                            <i class="flaticon-open-book-1 icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h4 class="title">Extensive Digital Library</h4>
                            <p>
                                Explore thousands of books, notes, and research materials from diverse genres and domains. 
                                Our digital library supports students, educators, and avid readers worldwide.
                            </p>
                            <a href="{{ url('/about') }}">Learn More <i class="fa-solid fa-angles-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="icon-bx-wraper style-3 m-b30">
                        <div class="icon-lg m-b20">
                            <i class="flaticon-exclusive icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h4 class="title">Trusted by Educators</h4>
                            <p>
                                We collaborate with institutions and teachers to ensure BookHub offers reliable and 
                                high-quality content aligned with learners’ needs.
                            </p>
                            <a href="{{ url('/about') }}">Learn More <i class="fa-solid fa-angles-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="icon-bx-wraper style-3 m-b30">
                        <div class="icon-lg m-b20">
                            <i class="flaticon-store icon-cell"></i>
                        </div>
                        <div class="icon-content">
                            <h4 class="title">Growing Community</h4>
                            <p>
                                BookHub is more than a platform — it’s a community of passionate readers and thinkers 
                                who believe in lifelong learning and sharing ideas that inspire progress.
                            </p>
                            <a href="{{ url('/about') }}">Learn More <i class="fa-solid fa-angles-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
