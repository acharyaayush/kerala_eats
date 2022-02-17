<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 4 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <!-- Title  -->
    <!-- <title><?php //echo APP_NAME .' | '?><?php if(isset($title)){echo $title;}?> <?php if(isset($pageTitle)){echo $pageTitle;}?></title> -->
    <title><?php echo APP_NAME ?></title>

    <!-- Favicon  -->
    <link rel="icon" href="<?php echo base_url(); ?>assets/web/img/fav-icon.png">

    <!-- ***** All CSS Files ***** -->

    <!-- Style css -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/web/css/style.css">

</head>

<body>
    <!--====== Scroll To Top Area Start ======-->
    <div id="scrollUp" title="Scroll To Top">
        <i class="fas fa-arrow-up"></i>
    </div>
    <!--====== Scroll To Top Area End ======-->

    <div class="main">
        <!-- ***** Header Start ***** -->
        <header class="navbar navbar-sticky navbar-expand-lg navbar-dark">
            <div class="container position-relative">
                <a class="navbar-brand" href="<?php echo base_url('web'); ?>"> 
                    <img class="navbar-brand-regular" src="<?php echo base_url(); ?>assets/web/img/logo/logo-white.png" alt="brand-logo">
                    <img class="navbar-brand-sticky" src="<?php echo base_url(); ?>assets/web/img/logo/logo-orange.png" alt="sticky brand-logo">
                </a>
                <button class="navbar-toggler d-lg-none" type="button" data-toggle="navbarToggler" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="navbar-inner">
                    <!--  Mobile Menu Toggler -->
                    <button class="navbar-toggler d-lg-none" type="button" data-toggle="navbarToggler" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <nav>
                        <ul class="navbar-nav" id="navbar-nav">
                          
                            <li class="nav-item">
                                <a class="nav-link scroll" href="#home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link scroll" href="#features">Features</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link scroll" href="#howKerala_eatsWork">How it Works</a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link scroll" href="#screenshots">Screenshots</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link scroll" href="#review">Reviews</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link scroll" href="#faq">Faq</a>
                            </li> -->
                            <li class="nav-item">
                                <a class="nav-link scroll" href="#contact">Contact</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>
        <!-- ***** Header End ***** -->

        <!-- ***** Welcome Area Start ***** -->
        <section id="home" class="section welcome-area bg-overlay overflow-hidden d-flex align-items-center">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Welcome Intro Start -->
                    <div class="col-12 col-md-7 col-lg-6">
                        <div class="welcome-intro">
                            <!-- <h1 class="text-white">Order foods with Kerala eats</h1> -->
                            <h1 class="text-white"> Kerala Eats – Delivering Memories & Tradition </h1>
                            <p class="text-white my-4">Kerala eats is specially catered to delivery Kerala Food in island - wide Singapore. Are you a fan of authentic Kerala spices? think no more Kerala eats is here to satisfy your cravings.</p>
                            <!-- Store Buttons -->
                            <div class="button-group store-buttons d-flex">
                                <a target="_blank" href="https://play.google.com/store/apps/details?id=com.kerala_eats">
                                    <img src="<?php echo base_url(); ?>assets/web/img/icon/google-play.png" alt="">
                                </a>
                                <a target="_blank" href="https://apps.apple.com/in/app/kerala-eats/id1572846103">
                                    <img src="<?php echo base_url(); ?>assets/web/img/icon/app-store.png" alt="">
                                </a>
                            </div>
                            <span class="d-inline-block text-white fw-3 font-italic mt-3">* Available on iPhone, iPad and all Android devices</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 col-lg-6">
                        <!-- Welcome Thumb -->
                        <div class="welcome-thumb mx-auto" data-aos="fade-left" data-aos-delay="500" data-aos-duration="1000">
                            <img src="<?php echo base_url(); ?>assets/web/img/welcome/banner-mocup.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Shape Bottom -->
            <div class="shape-bottom">
                <svg viewBox="0 0 1920 310" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="svg replaced-svg">
                    <title>Kerala eats Shape</title>
                    <desc>Created with Sketch</desc>
                    <defs></defs>
                    <g id="Kerala eats-Landing-Page" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Kerala eats-v1.0" transform="translate(0.000000, -554.000000)" fill="#FFFFFF">
                            <path d="M-3,551 C186.257589,757.321118 319.044414,856.322454 395.360475,848.004007 C509.834566,835.526337 561.525143,796.329212 637.731734,765.961549 C713.938325,735.593886 816.980646,681.910577 1035.72208,733.065469 C1254.46351,784.220361 1511.54925,678.92359 1539.40808,662.398665 C1567.2669,645.87374 1660.9143,591.478574 1773.19378,597.641868 C1848.04677,601.75073 1901.75645,588.357675 1934.32284,557.462704 L1934.32284,863.183395 L-3,863.183395" id="Kerala eats-v1.0"></path>
                        </g>
                    </g>
                </svg>
            </div>
        </section>
        <!-- ***** Welcome Area End ***** -->

        <!-- ***** Counter Area Start ***** -->
        <section class="section counter-area ptb_50">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-5 col-sm-3 single-counter text-center">
                        <div class="counter-inner p-3 p-md-0">
                            <!-- Counter Item -->
                            <div class="counter-item d-inline-block mb-3">
                                <span class="counter fw-7">10</span><span class="fw-7">M</span>
                            </div>
                            <h5>Users</h5>
                        </div>
                    </div>
                    <div class="col-5 col-sm-3 single-counter text-center">
                        <div class="counter-inner p-3 p-md-0">
                            <!-- Counter Item -->
                            <div class="counter-item d-inline-block mb-3">
                                <span class="counter fw-7">23</span><span class="fw-7">K</span>
                            </div>
                            <h5>Download</h5>
                        </div>
                    </div>
                    <div class="col-5 col-sm-3 single-counter text-center">
                        <div class="counter-inner p-3 p-md-0">
                            <!-- Counter Item -->
                            <div class="counter-item d-inline-block mb-3">
                                <span class="counter fw-7">9</span><span class="fw-7">M</span>
                            </div>
                            <h5>Customer</h5>
                        </div>
                    </div>
                    <div class="col-5 col-sm-3 single-counter text-center">
                        <div class="counter-inner p-3 p-md-0">
                            <!-- Counter Item -->
                            <div class="counter-item d-inline-block mb-3">
                                <span class="counter fw-7">12</span><span class="fw-7"></span>
                            </div>
                            <h5>Cities</h5>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Counter Area End ***** -->

        <!-- ***** Features Area Start ***** -->
        <section id="features" class="section features-area style-two overflow-hidden ptb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        <!-- Section Heading -->
                        <div class="section-heading text-center">
                            <span class="d-inline-block rounded-pill shadow-sm fw-5 px-4 py-2 mb-3">
                                <i class="far fa-lightbulb text-primary mr-1"></i>
                                <span class="text-primary">Premium</span>
                                Features
                            </span>
                            <h2> Services offered by Kerala Eats </h2>
                            <!-- <p class="d-none d-sm-block mt-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati dignissimos quae quo ad iste ipsum officiis deleniti asperiores sit.</p>
                            <p class="d-block d-sm-none mt-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati.</p> -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="card col-12 col-md-6 col-lg-3 res-margin">
                        <!-- Image Box -->
                        <div class="card-body image-box text-center icon-1 pd-5 wow fadeInLeft" data-wow-delay="0.4s">
                            <!-- Featured Image -->
                            <div class="featured-img mb-3">
                                <img class="avatar-sm" src="<?php echo base_url(); ?>assets/web/img/icon/Dining.png" alt="">
                            </div>
                            <!-- Icon Text -->
                            <div class="icon-text">
                                <h3 class="mb-2">Dining</h3>
                                <p> Make a reservation to your favorite Kerala Restaurants and get additional perks when dine-in. </p>
                            </div>
                        </div>
                    </div>
                    <div class="card col-12 col-md-6 col-lg-3 res-margin">
                        <!-- Image Box -->
                        <div class="card-body image-box text-center icon-1 pd-5 wow fadeInDown" data-wow-delay="0.4s">
                            <!-- Featured Image -->
                            <div class="featured-img mb-3">
                                <img class="avatar-sm" src="<?php echo base_url(); ?>assets/web/img/icon/food-delivery.png" alt="">
                            </div>
                            <!-- Icon Text -->
                            <div class="icon-text">
                                <h3 class="mb-2"> Food Delivery </h3>
                                <p> On-demand deliveries with average delivery from or 1.15 to 1.30 hours </p>
                            </div>
                        </div>
                    </div>
                    <div class="card col-12 col-md-6 col-lg-3 res-margin">
                        <!-- Image Box -->
                        <div class="card-body image-box text-center icon-1 pd-5 wow fadeInUp" data-wow-delay="0.2s">
                            <!-- Featured Image -->
                            <div class="featured-img mb-3">
                                <img class="avatar-sm" src="<?php echo base_url(); ?>assets/web/img/icon/groceries.png" alt="">
                            </div>
                            <!-- Icon Text -->
                            <div class="icon-text">
                                <h3 class="mb-2"> Groceries </h3>
                                <p> Groceries and vegetables at your doorstep from Kerala Minimarts.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card col-12 col-md-6 col-lg-3 res-margin">
                        <!-- Image Box -->
                        <div class="card-body image-box text-center icon-1 pd-5 wow fadeInRight" data-wow-delay="0.4s">
                            <!-- Featured Image -->
                            <div class="featured-img mb-3">
                                <img class="avatar-sm" src="<?php echo base_url(); ?>assets/web/img/icon/alcohol.png" alt="">
                            </div>
                            <!-- Icon Text -->
                            <div class="icon-text">
                                <h3 class="mb-2"> Alcohol </h3>
                                <p> Premium alcohol delivery with custom request at a wholesale price </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Features Area End ***** -->

        <!-- ***** Service Area Start ***** -->
        <section class="section service-area bg-gray overflow-hidden ptb_100">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-12 col-lg-6 order-2 order-lg-1">
                        <!-- Service Text -->
                        <div class="service-text pt-4 pt-lg-0">
                            <h2 class="text-capitalize mb-4"> Advance Features & Functions </h2>
                            <!-- Service List -->
                            <ul class="service-list">
                                <!-- Single Service -->
                                <li class="single-service media py-2 align-items-center">
                                    <div class="service-icon  pr-4">
                                        <span>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M14.6698 3.12709H22.8954C23.0287 3.12764 23.1565 3.18245 23.2507 3.27957C23.345 3.37669 23.3982 3.50825 23.3988 3.6456V9.40591H23.4477C23.5944 9.40647 23.7348 9.46687 23.8384 9.57389C23.9419 9.68091 24 9.82583 24 9.97691V10.6067C23.9995 10.7602 23.94 10.9072 23.8347 11.0158C23.7293 11.1243 23.5865 11.1855 23.4375 11.1861H19.551C22.4389 11.4506 24.0897 14.2174 23.9103 17.8155H21.3404C21.2953 18.6178 20.9542 19.372 20.3868 19.9237C19.8193 20.4753 19.0686 20.7826 18.2884 20.7826C17.5081 20.7826 16.7574 20.4753 16.19 19.9237C15.6226 19.372 15.2814 18.6178 15.2364 17.8155H8.53533C8.53533 17.7588 8.53533 17.7021 8.53533 17.6475C8.54031 18.0546 8.51716 18.4614 8.46603 18.8651L6.85394 18.0821C6.80842 18.5956 6.64343 19.0904 6.37293 19.5243C6.10243 19.9583 5.73439 20.3188 5.29998 20.5752C4.86556 20.8316 4.37756 20.9764 3.87729 20.9974C3.37702 21.0183 2.87921 20.9148 2.42601 20.6956C1.97282 20.4763 1.57758 20.1478 1.27377 19.7379C0.969954 19.328 0.766509 18.8487 0.680658 18.3406C0.594807 17.8325 0.629078 17.3106 0.78057 16.8191C0.932062 16.3276 1.19631 15.8808 1.55095 15.5168L0 14.7548C1.29212 13.2412 2.85326 12.6052 4.73845 13.0418C5.41304 13.2181 6.04484 13.3756 4.72215 13.0418C5.04976 10.5048 5.77596 8.03953 6.87228 5.74274L5.91033 5.72805C5.70652 5.72805 5.49253 5.78053 5.35598 5.67767C5.23468 5.6686 5.1175 5.62853 5.01501 5.56109C4.91253 5.49365 4.82798 5.40096 4.76902 5.29141C4.63016 5.05107 4.55955 4.77541 4.56522 4.49579C4.5596 4.21552 4.63019 3.93921 4.76902 3.69808C4.86163 3.53587 5.00553 3.41137 5.17663 3.34541C5.17356 3.32454 5.17356 3.30331 5.17663 3.28243C5.40285 2.90247 5.84918 3.01583 6.2466 3.01583L6.63383 3.04102C6.92081 3.08155 7.19756 3.17847 7.44905 3.32652L7.8913 3.58263H8.25L9.45652 3.82194C9.84986 3.90171 9.90693 3.86812 9.81522 4.36564C9.80273 4.4296 9.7864 4.4927 9.7663 4.55457C9.6481 4.91774 9.5625 4.78339 9.1875 4.70572L8.06046 4.4685C8.10326 4.93663 7.95856 5.51812 7.75679 6.17938C8.40285 6.74408 8.69633 7.54809 8.25204 8.6103L7.17595 12.0258C8.98166 13.3441 10.0659 14.5448 10.1291 16.495H11.3743C12.8458 15.3468 12.5971 13.3021 10.9871 11.9964V11.1861V9.96011C10.9871 9.56965 11.1909 9.39332 11.5679 9.40591H14.1664V3.6456C14.167 3.50825 14.2202 3.37669 14.3145 3.27957C14.4088 3.18245 14.5365 3.12764 14.6698 3.12709ZM4.96467 17.1752L3.42595 16.4342C3.14907 16.5008 2.89919 16.6545 2.71097 16.8741C2.52275 17.0936 2.40553 17.368 2.37558 17.6592C2.34562 17.9505 2.40441 18.2441 2.54379 18.4994C2.68317 18.7547 2.89623 18.9589 3.15341 19.0838C3.41059 19.2087 3.69914 19.2481 3.97902 19.1964C4.2589 19.1448 4.51624 19.0047 4.71533 18.7956C4.91442 18.5865 5.04539 18.3188 5.09007 18.0295C5.13475 17.7403 5.09092 17.4439 4.96467 17.1815V17.1752ZM5.18886 3.68549C5.2072 4.2166 5.23981 4.89885 5.26427 5.36488C5.21621 5.32069 5.17436 5.26982 5.13995 5.21373C5.01521 4.99365 4.9524 4.74216 4.95856 4.4874C4.95235 4.2333 5.01517 3.98246 5.13995 3.76316C5.15625 3.73797 5.17255 3.71278 5.18886 3.69179V3.68549ZM18.9742 4.01927C19.0967 4.01984 19.2139 4.07042 19.3003 4.15992C19.3732 4.23619 19.4198 4.3351 19.4327 4.44135C19.4456 4.54761 19.4241 4.65528 19.3716 4.7477C19.6114 4.78489 19.8452 4.85551 20.0666 4.95763C21.0856 5.48244 21.644 6.30324 21.644 7.5229C21.6441 7.55845 21.6311 7.59272 21.6077 7.61882C21.5843 7.64491 21.5521 7.66089 21.5177 7.66354H16.4368C16.419 7.66355 16.4013 7.65989 16.3849 7.6528C16.3684 7.64571 16.3535 7.63531 16.341 7.62223C16.3285 7.60914 16.3186 7.59361 16.312 7.57656C16.3053 7.55951 16.302 7.54127 16.3023 7.5229C16.2861 6.98213 16.4278 6.4488 16.709 5.99186C16.9902 5.53491 17.398 5.17537 17.8798 4.95973C18.1007 4.85639 18.3346 4.78572 18.5747 4.7498C18.5219 4.65748 18.5002 4.54973 18.5131 4.44339C18.526 4.33704 18.5728 4.2381 18.6461 4.16202C18.733 4.0722 18.851 4.02161 18.9742 4.02137V4.01927ZM15.7418 8.47595V8.08339C15.7416 8.07798 15.7424 8.07257 15.7442 8.06748C15.746 8.0624 15.7488 8.05775 15.7524 8.05382C15.7561 8.04989 15.7604 8.04677 15.7653 8.04463C15.7701 8.0425 15.7753 8.0414 15.7806 8.04141H22.1474C22.1527 8.0414 22.1579 8.0425 22.1627 8.04463C22.1676 8.04677 22.1719 8.04989 22.1755 8.05382C22.1792 8.05775 22.182 8.0624 22.1838 8.06748C22.1856 8.07257 22.1864 8.07798 22.1861 8.08339V8.47595C22.1861 8.48653 22.1821 8.49667 22.1748 8.50415C22.1675 8.51163 22.1577 8.51584 22.1474 8.51584H15.7806C15.7703 8.51584 15.7605 8.51163 15.7532 8.50415C15.7459 8.49667 15.7418 8.48653 15.7418 8.47595ZM19.6325 17.8197C19.6189 18.1775 19.4714 18.5161 19.2208 18.7643C18.9702 19.0126 18.6361 19.1512 18.2884 19.1512C17.9407 19.1512 17.6065 19.0126 17.3559 18.7643C17.1054 18.5161 16.9578 18.1775 16.9443 17.8197H19.6325Z" fill="#FF5A00"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="service-text media-body">
                                        <p> On-Demand food delivery from Kerala Restaurants .</p>
                                    </div>
                                </li>
                                <!-- Single Service -->
                                <li class="single-service media py-2 align-items-center">
                                    <div class="service-icon pr-4">
                                        <span>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M13.3505 5.74585H13.9298C14.2418 5.79042 14.5426 5.86841 14.8212 6.01325C15.6345 6.41434 16.1804 7.06055 16.5592 7.86274C16.7932 8.35297 16.9158 8.86548 16.9938 9.38913C17.0049 9.46712 17.0383 9.4894 17.1163 9.4894L17.523 9.50612L17.9296 9.52283C18.0968 9.53397 18.2639 9.54511 18.4199 9.60082C18.6761 9.68995 18.8767 9.86822 18.9324 10.1245C19.0057 10.4322 19.0596 10.7447 19.1134 11.0557C19.1414 11.2177 19.1693 11.3792 19.1998 11.5394C19.332 12.2465 19.4666 12.9535 19.6014 13.6617C19.7618 14.5045 19.9225 15.3489 20.08 16.1966C20.2081 16.8595 20.3334 17.5196 20.4588 18.1798C20.5841 18.8399 20.7095 19.5001 20.8376 20.163C21.0159 21.0766 21.183 21.9902 21.3501 22.9149V23.1601C21.3278 23.2492 21.2944 23.3495 21.2498 23.4275C21.0716 23.706 20.8599 23.9177 20.5033 23.9511C20.1802 23.9734 19.8571 23.9845 19.534 23.9845C16.6372 23.9957 13.7516 23.9957 10.8548 23.9957C10.5607 23.9957 10.2662 23.9968 9.97129 23.9979L9.97037 23.9979C8.93587 24.0017 7.89762 24.0056 6.86611 23.9623C6.49844 23.9511 6.11963 23.6614 5.99707 23.3272V22.9929C6.01378 22.8815 6.02771 22.7701 6.04164 22.6587C6.05556 22.5473 6.06949 22.4359 6.0862 22.3244C6.25496 21.3272 6.44485 20.33 6.63405 19.3364L6.63405 19.3364L6.63405 19.3364C6.71965 18.8869 6.8051 18.4381 6.88839 17.9904C7.00538 17.3665 7.12515 16.7453 7.24492 16.1242C7.36469 15.5031 7.48446 14.8819 7.60145 14.258C7.68385 13.8391 7.76519 13.4213 7.84645 13.0039L7.84657 13.0033L7.84668 13.0027L7.8467 13.0026L7.84671 13.0026C8.02907 12.0659 8.21103 11.1313 8.40364 10.1913C8.40819 10.1731 8.41228 10.1554 8.41628 10.138C8.43187 10.0703 8.44616 10.0083 8.48163 9.94621C8.61533 9.71224 8.82702 9.63424 9.07213 9.58968C9.31765 9.55248 9.56813 9.54509 9.82026 9.53765C9.94571 9.53395 10.0716 9.53023 10.1974 9.52283C10.2754 9.52283 10.3088 9.50055 10.32 9.42255C10.398 8.86548 10.5317 8.31954 10.7879 7.81817C11.0999 7.20539 11.5121 6.6706 12.0803 6.2695C12.4703 5.97982 12.8937 5.8127 13.3505 5.74585ZM13.6624 9.52283H16.0022C16.147 9.52283 16.1581 9.51169 16.147 9.36685C16.1359 9.09945 16.0467 8.8432 15.9576 8.58694C15.7459 7.99644 15.4451 7.47279 14.9549 7.06055C14.4646 6.65946 13.9076 6.49233 13.2836 6.61489C12.7711 6.71516 12.37 7.00484 12.0358 7.38366C11.6458 7.81818 11.423 8.34183 11.267 8.8989L11.267 8.89891C11.2224 9.05489 11.1779 9.21087 11.1667 9.36685C11.1556 9.51169 11.1667 9.52283 11.3116 9.52283H13.6624ZM11.4666 12.0411C11.4666 12.4979 11.0989 12.8655 10.6644 12.8544C10.2299 12.8544 9.85108 12.4644 9.86222 12.0299C9.87336 11.5843 10.2188 11.2277 10.6533 11.2389C11.0989 11.2389 11.4666 11.5954 11.4666 12.0411ZM16.6595 11.2389C16.225 11.2277 15.8685 11.5843 15.8685 12.0299C15.8685 12.4756 16.2473 12.8544 16.6707 12.8544C17.1163 12.8544 17.484 12.4867 17.484 12.0411C17.484 11.5954 17.1163 11.2389 16.6595 11.2389Z" fill="#FF5A00"/>
                                                <path d="M9.7792 0C10.2973 0.00802875 10.6698 0.0835299 10.9165 0.217342L14.2886 1.55922L16.1665 3.45097C16.5612 3.90274 16.2801 4.77819 15.0448 4.21713L13.6946 2.8957C11.9774 1.79661 8.4717 2.30302 10.1341 4.50637C10.9508 5.78227 10.5665 6.40522 9.51771 6.66667L9.03833 5.47314C8.20709 4.08805 6.6608 3.83882 6.72389 2.72213L2.99967 2.92092L3.02973 0.0376476L9.7792 0ZM11.6565 2.65922C12.2608 2.6515 12.8534 2.83672 13.285 3.10633L14.6401 4.42721C14.2683 4.6461 13.8969 4.52909 13.5252 4.26347C13.0869 4.42801 12.7489 4.17284 12.4713 3.66397C12.3561 3.31685 12.1863 3.07115 11.6565 2.65922H11.6565Z" fill="#FF5A00"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="service-text media-body">
                                        <p> Order & Self-collect to enjoy additional discounts. </p>
                                    </div>
                                </li>
                                <!-- Single Service -->
                                <li class="single-service media py-2 align-items-center">
                                    <div class="service-icon pr-4">
                                        <span>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0)">
                                                    <path d="M2.26139 6.78502C2.46735 6.888 3.05089 7.19694 3.68838 6.48099C3.94338 6.12301 4.19837 5.76504 4.50241 5.41197C4.86038 5.00496 4.80644 4.34295 4.39943 3.98497C3.99242 3.627 3.33041 3.68094 2.97243 4.08795C2.61446 4.49496 2.25649 4.95592 1.95245 5.41197C1.65332 5.86802 1.80534 6.47608 2.26139 6.78502Z" fill="#FF5A00"/>
                                                    <path d="M12.2102 1.9892C13.1811 1.9892 14.1472 2.14121 15.0691 2.45015C15.6869 2.63159 16.1381 2.24419 16.344 1.83718C16.4961 1.27815 16.192 0.71422 15.682 0.562203C14.5591 0.204228 13.4361 0.00317383 12.2641 0.00317383C11.7051 0.00317383 11.2441 0.410186 11.2441 0.974119C11.2441 1.53805 11.6512 1.9892 12.2102 1.9892Z" fill="#FF5A00"/>
                                                    <path d="M21.2921 8.21193C21.6501 9.07989 21.8512 9.9969 21.9541 10.9139C21.9541 10.9139 22.013 12.0516 23.0771 11.8848C24.1314 11.7475 23.9941 10.7128 23.9941 10.7128C23.8911 9.58989 23.5871 8.46692 23.1801 7.44694C22.9251 6.93695 22.3661 6.68195 21.8561 6.88791C21.3412 7.14291 21.0862 7.70194 21.2921 8.21193Z" fill="#FF5A00"/>
                                                    <path d="M17.6192 3.72502C18.4333 4.28405 19.1492 4.95096 19.7622 5.71594C20.2182 6.28478 20.9881 6.0494 21.1401 5.86796C21.5472 5.50999 21.6501 4.84798 21.2922 4.44097C20.5762 3.573 19.7082 2.75898 18.7422 2.09207C18.2812 1.78804 17.6732 1.88611 17.3642 2.34707C17.0553 2.80802 17.1583 3.41608 17.6192 3.72502Z" fill="#FF5A00"/>
                                                    <path d="M6.6008 3.56826C7.41482 3.05827 8.33673 2.65126 9.25373 2.39627C9.81276 2.24425 10.1217 1.68032 9.96968 1.17032C9.81766 0.611295 9.25373 0.302358 8.74374 0.454375C7.62078 0.758408 6.55176 1.21936 5.58081 1.83233C5.11986 2.13637 4.96784 2.74934 5.27678 3.21029C5.47783 3.51923 5.90937 3.82326 6.6008 3.56826Z" fill="#FF5A00"/>
                                                    <path d="M22.9742 13.1601C22.4151 13.0081 21.9051 13.366 21.8022 13.9251C21.6992 14.386 21.3853 15.4599 21.3804 15.5629C20.0319 19.0691 16.7954 21.5357 13.0784 21.9329C9.42015 22.3154 5.86002 20.6628 3.79553 17.6912L5.68348 18.108C6.19348 18.211 6.7525 17.902 6.85548 17.343C6.95846 16.833 6.64952 16.274 6.0905 16.171L2.11354 15.303C1.85855 15.254 1.06414 15.303 0.887603 16.068L0.0196369 20.0989C-0.0833422 20.6089 0.225595 21.1679 0.784624 21.2709C1.4074 21.3347 1.85364 20.9669 2.01056 20.4569L2.3146 19.074C4.58504 22.1487 8.68459 24.424 13.2843 23.9287C17.9233 23.4286 21.8561 20.3049 23.3861 15.9209C23.4351 15.7934 23.6656 14.7244 23.744 14.337C23.8961 13.7731 23.5381 13.2631 22.9742 13.1601Z" fill="#FF5A00"/>
                                                    <path d="M8.07174 13.008H12.7744C13.3335 13.008 13.7454 12.5471 13.7944 11.988V5.01486C13.7944 4.45583 13.3335 3.99487 12.7744 3.99487C12.2154 3.99487 11.7545 4.45583 11.7545 5.01486V10.968H8.07174C7.51271 10.968 7.05176 11.429 7.05176 11.988C7.05176 12.5471 7.50781 13.008 8.07174 13.008Z" fill="#FF5A00"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0">
                                                <rect width="24" height="24" fill="white"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="service-text media-body">
                                        <p> Advance ordering feature and get timely deliveries.</p>
                                    </div>
                                </li>
                                <!-- Single Service -->
                                <li class="single-service media py-2 align-items-center">
                                    <div class="service-icon pr-4">
                                        <span>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0)">
                                                <path d="M17.6226 18.0032C16.4726 18.0031 15.5369 17.0676 15.5369 15.9175V12.4538C15.5369 11.3038 16.4726 10.3682 17.6226 10.3682H23.0572C23.0901 10.3682 23.1228 10.3692 23.1554 10.3706V6.92019C23.1554 6.03202 22.4354 5.31201 21.5472 5.31201H1.60823C0.720004 5.31196 0 6.03196 0 6.92014V21.4511C0 22.3393 0.720004 23.0593 1.60823 23.0593H21.5472C22.4354 23.0593 23.1554 22.3393 23.1554 21.4511V18.0007C23.1228 18.0022 23.0901 18.0032 23.0572 18.0032H17.6226Z" fill="#FF5A00"/>
                                                <path d="M23.0571 11.511H17.6225C17.1018 11.511 16.6797 11.9331 16.6797 12.4538V15.9175C16.6797 16.4382 17.1018 16.8603 17.6225 16.8603H23.0571C23.5778 16.8603 23.9999 16.4383 23.9999 15.9175V12.4538C23.9999 11.9331 23.5778 11.511 23.0571 11.511ZM19.4952 15.5464C18.7436 15.5464 18.1344 14.9372 18.1344 14.1856C18.1344 13.434 18.7436 12.8248 19.4952 12.8248C20.2467 12.8248 20.856 13.434 20.856 14.1856C20.856 14.9372 20.2467 15.5464 19.4952 15.5464Z" fill="#FF5A00"/>
                                                <path d="M18.7474 2.36618C18.3876 1.27717 17.2131 0.68603 16.1242 1.04582L7.9541 3.745H19.2029L18.7474 2.36618Z" fill="#FF5A00"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0">
                                                <rect width="24" height="24" fill="white"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="service-text media-body">
                                        <p> Wallet – Top up wallet for hassle free check-outs.</p>
                                    </div>
                                </li>
                                <!-- Single Service -->
                                <li class="single-service media py-2 align-items-center">
                                    <div class="service-icon  pr-4">
                                        <span>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0)">
                                                <path d="M17.7584 13.7907C17.7116 13.7682 15.962 12.9067 15.651 12.7948C15.5241 12.7492 15.3881 12.7047 15.2435 12.7047C15.0073 12.7047 14.8088 12.8224 14.6542 13.0537C14.4794 13.3135 13.9503 13.932 13.7868 14.1167C13.7655 14.1411 13.7363 14.1702 13.7189 14.1702C13.7032 14.1702 13.4324 14.0587 13.3505 14.0231C11.4736 13.2078 10.0489 11.2472 9.85357 10.9166C9.82567 10.8691 9.8245 10.8475 9.82427 10.8475C9.83112 10.8223 9.89424 10.759 9.92681 10.7264C10.0221 10.6321 10.1254 10.5078 10.2253 10.3876C10.2726 10.3306 10.3199 10.2736 10.3664 10.2198C10.5114 10.0512 10.5759 9.9203 10.6507 9.76866L10.6899 9.68988C10.8726 9.327 10.7166 9.02076 10.6661 8.92188C10.6248 8.83913 9.88598 7.05612 9.80744 6.86879C9.61855 6.41677 9.36897 6.2063 9.02214 6.2063C8.98996 6.2063 9.02214 6.2063 8.88718 6.21199C8.72284 6.21892 7.8279 6.33674 7.43222 6.58617C7.0126 6.85071 6.30273 7.69399 6.30273 9.177C6.30273 10.5117 7.14975 11.772 7.51341 12.2513C7.52245 12.2633 7.53905 12.2879 7.56312 12.3231C8.95583 14.357 10.692 15.8643 12.452 16.5674C14.1464 17.2443 14.9488 17.3225 15.405 17.3225C15.405 17.3225 15.405 17.3225 15.405 17.3225C15.5967 17.3225 15.7502 17.3075 15.8855 17.2941L15.9714 17.286C16.5567 17.2341 17.8431 16.5675 18.1357 15.7544C18.3662 15.114 18.4269 14.4142 18.2736 14.1603C18.1686 13.9876 17.9875 13.9007 17.7584 13.7907Z" fill="#FF5A00"/>
                                                <path d="M12.213 0C5.71307 0 0.424969 5.24836 0.424969 11.6995C0.424969 13.786 0.983358 15.8284 2.04115 17.6159L0.0165014 23.5883C-0.0212129 23.6996 0.00683904 23.8227 0.0892027 23.9066C0.148657 23.9673 0.229307 24 0.31167 24C0.343229 24 0.375021 23.9952 0.406034 23.9854L6.63357 22.0064C8.33772 22.917 10.2638 23.3976 12.2131 23.3976C18.7124 23.3977 24 18.1498 24 11.6995C24 5.24836 18.7124 0 12.213 0ZM12.213 20.9606C10.3788 20.9606 8.60227 20.4309 7.07515 19.4289C7.0238 19.3951 6.96419 19.3778 6.90419 19.3778C6.87248 19.3778 6.84068 19.3826 6.80975 19.3925L3.69014 20.3841L4.6972 17.413C4.72977 17.3169 4.71349 17.2108 4.65349 17.1288C3.49058 15.5398 2.87585 13.6625 2.87585 11.6995C2.87585 6.59221 7.06448 2.43709 12.2129 2.43709C17.3608 2.43709 21.5489 6.59221 21.5489 11.6995C21.549 16.8061 17.3609 20.9606 12.213 20.9606Z" fill="#FF5A00"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0">
                                                <rect width="24" height="24" fill="white"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="service-text media-body">
                                        <p> WhatsApp Chat support for easier communication. </p>
                                    </div>
                                </li>
                                <!-- Single Service -->
                                <li class="single-service media py-2 align-items-center">
                                    <div class="service-icon  pr-4">
                                        <span>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#clip0)">
                                                <path d="M12.0177 0H11.9834C7.83869 0 4.4668 3.37077 4.4668 7.51395C4.4668 10.2298 5.70364 13.814 8.14295 18.1673C9.95144 21.3947 11.7853 23.8665 11.8037 23.8911C11.8547 23.9597 11.9352 24.0001 12.0204 24.0001C12.0228 24.0001 12.0253 24.0001 12.0277 23.9999C12.1157 23.9975 12.1969 23.9525 12.2457 23.8792C12.2638 23.8518 14.0845 21.1022 15.8807 17.7392C18.3052 13.2 19.5345 9.75966 19.5345 7.51395C19.5343 3.37071 16.1623 0 12.0177 0ZM15.4873 7.69757C15.4873 9.62021 13.9232 11.1843 12.0005 11.1843C10.0779 11.1843 8.5138 9.62016 8.5138 7.69757C8.5138 5.77498 10.0779 4.21084 12.0005 4.21084C13.9232 4.21084 15.4873 5.77498 15.4873 7.69757Z" fill="#FF5A00"/>
                                                </g>
                                                <defs>
                                                <clipPath id="clip0">
                                                <rect width="24" height="24" fill="white"/>
                                                </clipPath>
                                                </defs>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="service-text media-body">
                                        <p> Save multiple location for office, Home etc. </p>
                                    </div>
                                </li>
                                <!-- Single Service -->
                                <li class="single-service media py-2 align-items-center">
                                    <div class="service-icon pr-4">
                                        <span>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M19.166 10.6401C18.9319 10.4558 16.8647 8.17933 15.6941 7.11831C15.6891 7.11333 15.6891 7.11333 15.6891 7.10834V2.20672C15.6891 0.991283 14.6978 0 13.4823 0H6.25445C5.12369 0 4.20215 0.926526 4.20215 2.0523V13.5492C4.20215 14.7646 5.19343 15.7559 6.40887 15.7559H8.51099H9.04399C9.13366 15.7559 9.20838 15.6762 9.19841 15.5866L9.08384 14.6102C9.07388 14.5305 9.00912 14.4757 8.92942 14.4757H8.73515H8.6156C8.53092 14.4757 8.46118 14.406 8.46118 14.3213V1.26027C8.46118 1.17559 8.53092 1.10585 8.6156 1.10585H13.4973C13.9904 1.10585 14.3939 1.50934 14.3939 2.00249V12.6725V13.7235C14.3939 14.0623 14.1598 14.3512 13.846 14.4408C13.841 14.4408 13.841 14.4408 13.836 14.4408C13.303 14.1519 12.6754 12.9166 12.5807 12.7223C12.5708 12.7073 12.5658 12.6874 12.5658 12.6725C12.5309 12.3587 12.1772 9.32005 10.5583 9.32005C10.5135 9.32005 10.4686 9.32005 10.4238 9.32503C10.4238 9.32503 9.12867 9.43462 10.1997 13.1258C10.1997 13.1357 10.2046 13.1407 10.2046 13.1507L10.6779 17.2404C10.6779 17.2453 10.6779 17.2503 10.6828 17.2603C10.7227 17.4147 11.2607 19.5019 12.6505 20.9813C12.6754 21.0112 12.6903 21.0461 12.6903 21.0859V21.4396C12.6903 21.4496 12.6853 21.4545 12.6754 21.4545H12.5508C12.247 21.4545 11.9979 21.7036 11.9979 22.0075V23.4471C11.9979 23.7509 12.247 24 12.5508 24H18.4139C18.7177 24 18.9668 23.7509 18.9668 23.4471V22.0174C18.9668 21.7136 18.7177 21.4645 18.4139 21.4645C18.4039 21.4645 18.3989 21.4595 18.3989 21.4496C18.5932 19.6463 19.2906 13.4795 19.6642 12.9963C19.6741 12.9813 19.6841 12.9664 19.6891 12.9465C19.7538 12.7223 20.0776 11.3524 19.166 10.6401ZM6.5085 14.3313C6.5085 14.411 6.44374 14.4707 6.36902 14.4707C5.88085 14.4707 5.48235 14.0722 5.48235 13.5841V1.99253C5.48235 1.50436 5.88085 1.10585 6.36902 1.10585C6.44872 1.10585 6.5085 1.17061 6.5085 1.24533V14.3313Z" fill="#FF5A00"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="service-text media-body">
                                        <p> Save/ Manage card for faster check-out. </p>
                                    </div>
                                </li>
                                <!-- Single Service -->
                                <li class="single-service media py-2 align-items-center">
                                    <div class="service-icon pr-4">
                                        <span>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21.7267 6.76732C21.8001 6.76732 21.8001 6.76732 21.8731 6.76732L20.7734 2.51481C20.4801 1.26787 19.1598 0.534565 17.9865 0.901217L1.70785 5.22771C0.46123 5.52103 -0.272074 6.84098 0.094578 8.01427L1.48786 13.2941V10.1408C1.48786 8.23491 3.0278 6.69497 4.93439 6.69497H21.7267V6.76732Z" fill="#FF5A00"/>
                                                <path d="M21.7268 7.94043H4.86081C3.61419 7.94043 2.58789 8.96738 2.58789 10.2137V20.9193C2.58789 22.1665 3.61452 23.1925 4.86081 23.1925H21.7268C22.9734 23.1925 23.9997 22.1656 23.9997 20.9193V10.2143C24.0004 8.96706 22.9734 7.94043 21.7268 7.94043ZM22.4604 20.9202C22.4604 21.3606 22.0941 21.6532 21.7275 21.6532H4.86146C4.42148 21.6532 4.12848 21.2869 4.12848 20.9199V18.2073H22.5341V20.9199L22.4604 20.9202ZM22.4604 12.9272H4.12783V10.2143C4.12783 9.77402 4.49448 9.48102 4.86081 9.48102H21.7268C22.1671 9.48102 22.4598 9.84768 22.4598 10.2143L22.4604 12.9272Z" fill="#FF5A00"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="service-text media-body">
                                        <p> Flexible Payment options, Visa, Master, Amex, PayNow, PayLah etc. </p>
                                    </div>
                                </li>
                                <!-- Single Service -->
                                <li class="single-service media py-2 align-items-center">
                                    <div class="service-icon pr-4">
                                        <span>
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M22.007 8.43895H21.1411C20.9043 8.43895 20.8963 8.28385 20.8882 8.20667C20.4341 3.92859 16.804 0.584473 12.4075 0.584473H11.5925C7.22851 0.584473 3.61967 3.87919 3.12241 8.11187C3.10971 8.2203 3.10306 8.43895 2.90362 8.43895H1.99292C1.3952 8.43895 0 9.06791 0 12.2442V13.9112C0 16.267 1.3952 16.8103 1.99292 16.8103H4.89036C5.48807 16.8103 5.9771 16.3213 5.9771 15.7236V9.52569C5.9771 8.92797 5.48807 8.43895 4.89036 8.43895C4.89036 8.43895 4.75394 8.42698 4.78871 8.184C5.26683 4.83882 8.12126 2.24652 11.5925 2.24652H12.4075C15.8551 2.24652 18.7172 4.80053 19.2017 8.11586C19.2173 8.22276 19.2756 8.43895 19.1096 8.43895C18.5119 8.43895 18.0229 8.92797 18.0229 9.52569V15.7236C18.0229 16.3213 18.5119 16.8103 19.1096 16.8103H19.6537C19.8033 16.8103 19.7779 16.9298 19.7645 16.9901C19.3213 18.9914 18.3261 21.2618 16.0923 21.2618H14.4377C14.2689 21.2618 14.2272 21.138 14.1951 21.0794C13.927 20.5897 13.4072 20.2575 12.8096 20.2575H11.285C10.4129 20.2575 9.70593 20.9644 9.70593 21.8365C9.70593 22.7087 10.4129 23.4156 11.285 23.4156H12.8096C13.4153 23.4156 13.9413 23.0744 14.206 22.5738C14.2291 22.5301 14.2481 22.4391 14.4268 22.4391H16.0923C17.6096 22.4391 19.64 21.6852 20.6966 18.0932C20.8093 17.7104 20.9006 17.3289 20.975 16.9613C20.9852 16.9107 20.9916 16.8104 21.1495 16.8104H22.0071C22.6048 16.8104 24 16.267 24 13.9113V12.2443C23.9999 9.11318 22.6047 8.43895 22.007 8.43895Z" fill="#FF5A00"/>
                                            </svg>
                                        </span>
                                    </div> 
                                    <div class="service-text media-body">
                                        <p> Amazing customers service support calls & Chats. </p>
                                    </div>
                                </li>
                            </ul>
                            <a href="#" class="btn btn-bordered mt-4">Learn More</a>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 order-1 order-lg-2 d-none d-lg-block">
                        <!-- Service Thumb -->
                        <div class="service-thumb mx-auto">
                            <img src="<?php echo base_url(); ?>assets/web/img/screenshots/screen-2.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Service Area End ***** -->

        <!-- ***** Discover Area Start ***** -->
        <!-- <section class="section discover-area overflow-hidden ptb_100">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-12 col-lg-6 order-2 order-lg-1">
                        Discover Thumb
                        <div class="service-thumb discover-thumb mx-auto pt-5 pt-lg-0">
                            <img src="assets/img/screenshots/screen-3.png" alt="">
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 order-1 order-lg-2">
                        Discover Text
                        <div class="discover-text pt-4 pt-lg-0">
                            <h2 class="pb-4 pb-sm-0">Order food easily using Kerala eats.</h2>
                            <p class="d-none d-sm-block pt-3 pb-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Similique dolor ut iusto vitae autem neque eum ipsam.</p>
                            Check List
                            <ul class="check-list">
                                <li class="py-1">
                                    List Box
                                    <div class="list-box media">
                                        <span class="icon align-self-center"><i class="fas fa-check"></i></span>
                                        <span class="media-body pl-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Similique dolor ut iusto vitae autem neque eum ipsam.</span>
                                    </div>
                                </li>
                                <li class="py-1">
                                    List Box
                                    <div class="list-box media">
                                        <span class="icon align-self-center"><i class="fas fa-check"></i></span>
                                        <span class="media-body pl-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Similique dolor ut iusto vitae autem neque eum ipsam.</span>
                                    </div>
                                </li>
                                <li class="py-1">
                                    List Box
                                    <div class="list-box media">
                                        <span class="icon align-self-center"><i class="fas fa-check"></i></span>
                                        <span class="media-body pl-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Similique dolor ut iusto vitae autem neque eum ipsam.</span>
                                    </div>
                                </li>
                                <li class="py-1">
                                    List Box
                                    <div class="list-box media">
                                        <span class="icon align-self-center"><i class="fas fa-check"></i></span>
                                        <span class="media-body pl-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Similique dolor ut iusto vitae autem neque eum ipsam.</span>
                                    </div>
                                </li>
                            </ul>
                            <div class="icon-box d-flex mt-3">
                                <div class="service-icon">
                                    <span><i class="fas fa-bell"></i></span>
                                </div>
                                <div class="service-icon px-3">
                                    <span><i class="fas fa-envelope-open"></i></span>
                                </div>
                                <div class="service-icon">
                                    <span><i class="fas fa-video"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!-- ***** Discover Area End ***** -->

        <!-- ***** Work Area Start ***** -->
        <section class="section work-area bg-overlay overflow-hidden ptb_100" id="howKerala_eatsWork">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        <!-- Work Content -->
                        <div class="work-content text-center mb-4">
                            <h2 class="text-white">How Kerala eats works?</h2>
                            <!-- <p class="d-none d-sm-block text-white my-3 mt-sm-4 mb-sm-5">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati dignissimos quae quo ad iste ipsum officiis deleniti asperiores sit.</p>
                            <p class="d-block d-sm-none text-white my-3">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati.</p> -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4">
                        <!-- Single Work -->
                        <div class="single-work text-center p-3">
                            <!-- Work Icon -->
                            <div class="work-icon">
                                <img class="avatar-md" src="<?php echo base_url(); ?>assets/web/img/icon/work/download.png" alt="">
                            </div>
                            <h3 class="text-white py-3">Install the App</h3>
                            <!-- <p class="text-white">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius saepe, voluptates quis enim incidunt obcaecati?</p> -->
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <!-- Single Work -->
                        <div class="single-work text-center p-3">
                            <!-- Work Icon -->
                            <div class="work-icon">
                                <img class="avatar-md" src="<?php echo base_url(); ?>assets/web/img/icon/work/settings.png" alt="">
                            </div>
                            <h3 class="text-white py-3">Setup your profile</h3>
                            <!-- <p class="text-white">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius saepe, voluptates quis enim incidunt obcaecati?</p> -->
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <!-- Single Work -->
                        <div class="single-work text-center p-3">
                            <!-- Work Icon -->
                            <div class="work-icon">
                                <img class="avatar-md" src="<?php echo base_url(); ?>assets/web/img/icon/work/food-order.png" alt="">
                            </div>
                            <h3 class="text-white py-3">Order Food!</h3>
                            <!-- <p class="text-white">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius saepe, voluptates quis enim incidunt obcaecati?</p> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Work Area End ***** -->

        <!-- ***** Screenshots Area Start ***** -->
        <section id="screenshots" class="section screenshots-area ptb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        <!-- Section Heading -->
                        <div class="section-heading text-center">
                            <span class="d-inline-block rounded-pill shadow-sm fw-5 px-4 py-2 mb-3">
                                <i class="far fa-lightbulb text-primary mr-1"></i>
                                <span class="text-primary">Awesome</span>
                                Interface
                            </span>
                            <h2 class="text-capitalize">Simple &amp; Beautiful Interface</h2>
                           <!--  <p class="d-none d-sm-block mt-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati dignissimos quae quo ad iste ipsum officiis deleniti asperiores sit.</p>
                            <p class="d-block d-sm-none mt-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati.</p> -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <!-- App Screenshot Slider Area -->
                        <div class="app-screenshots">
                            <!-- Single Screenshot Item -->
                            <div class="single-screenshot">
                                <!-- <img src="assets/img/screenshots/1.jpg" alt=""> -->
                                <img src="<?php echo base_url(); ?>assets/web/img/screenshots/screen-3.png" alt="">
                            </div>
                            <!-- Single Screenshot Item -->
                            <div class="single-screenshot">
                               <!--  <img src="assets/img/screenshots/2.jpg" alt=""> -->
                               <img src="<?php echo base_url(); ?>assets/web/img/screenshots/screen-2.png" alt="">
                            </div>
                            <!-- Single Screenshot Item -->
                            <div class="single-screenshot">
                                <!-- <img src="assets/img/screenshots/3.jpg" alt=""> -->
                                <img src="<?php echo base_url(); ?>assets/web/img/screenshots/screen-3.png" alt="">
                            </div>
                            <!-- Single Screenshot Item -->
                            <div class="single-screenshot">
                                <!-- <img src="assets/img/screenshots/4.jpg" alt=""> -->
                                <img src="<?php echo base_url(); ?>assets/web/img/screenshots/screen-4.png" alt="">
                            </div>
                            <!-- Single Screenshot Item -->
                            <div class="single-screenshot">
                               <!--  <img src="assets/img/screenshots/5.jpg" alt=""> -->
                               <img src="<?php echo base_url(); ?>assets/web/img/screenshots/screen-5.png" alt="">
                            </div>
                            <div class="single-screenshot">
                               <img src="<?php echo base_url(); ?>assets/web/img/screenshots/screen-6.png" alt="">
                            </div>
                            <div class="single-screenshot">
                               <img src="<?php echo base_url(); ?>assets/web/img/screenshots/screen-7.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Screenshots Area End ***** -->

        <!-- ***** Review Area Start ***** -->
        <!-- <section id="review" class="review-area ptb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        Section Heading
                        <div class="section-heading text-center">
                            <span class="d-inline-block rounded-pill shadow-sm fw-5 px-4 py-2 mb-3">
                                <i class="far fa-lightbulb text-primary mr-1"></i>
                                <span class="text-primary">Customer's</span>
                                Reviews
                            </span>
                            <h2 class="text-capitalize">What our customers are saying</h2>
                            <p class="d-none d-sm-block mt-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati dignissimos quae quo ad iste ipsum officiis deleniti asperiores sit.</p>
                            <p class="d-block d-sm-none mt-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4 res-margin">
                        Single Review
                        <div class="single-review card">
                            Card Top
                            <div class="card-top p-4">
                                <div class="review-icon">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <h4 class="text-primary mt-4 mb-3">Excellent service &amp; support!!</h4>
                                Review Text
                                <div class="review-text">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quis nam id facilis, provident doloremque placeat eveniet molestias laboriosam. Optio, esse.</p>
                                </div>
                                Quotation Icon
                                <div class="quot-icon">
                                    <img class="avatar-md" src="assets/img/icon/quote.png" alt="">
                                </div>
                            </div>
                            Reviewer
                            <div class="reviewer media bg-gray p-4">
                                Reviewer Thumb
                                <div class="reviewer-thumb">
                                    <img class="avatar-lg radius-100" src="assets/img/avatar/avatar-1.png" alt="">
                                </div>
                                Reviewer Media
                                <div class="reviewer-meta media-body align-self-center ml-4">
                                    <h5 class="reviewer-name color-primary mb-2">Junaid Hasan</h5>
                                    <h6 class="text-secondary fw-6">CEO, Themeland</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4 res-margin">
                        Single Review
                        <div class="single-review card">
                            Card Top
                            <div class="card-top p-4">
                                <div class="review-icon">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star-half-alt text-warning"></i>
                                </div>
                                <h4 class="text-primary mt-4 mb-3">Nice work! Keep it up</h4>
                                Review Text
                                <div class="review-text">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quis nam id facilis, provident doloremque placeat eveniet molestias laboriosam. Optio, esse.</p>
                                </div>
                                Quotation Icon
                                <div class="quot-icon">
                                    <img class="avatar-md" src="assets/img/icon/quote.png" alt="">
                                </div>
                            </div>
                            Reviewer
                            <div class="reviewer media bg-gray p-4">
                                Reviewer Thumb
                                <div class="reviewer-thumb">
                                    <img class="avatar-lg radius-100" src="assets/img/avatar/avatar-2.png" alt="">
                                </div>
                                Reviewer Media
                                <div class="reviewer-meta media-body align-self-center ml-4">
                                    <h5 class="reviewer-name color-primary mb-2">Junaid Hasan</h5>
                                    <h6 class="text-secondary fw-6">CEO, Themeland</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-4">
                        Single Review
                        <div class="single-review card">
                            Card Top
                            <div class="card-top p-4">
                                <div class="review-icon">
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                    <i class="fas fa-star text-warning"></i>
                                </div>
                                <h4 class="text-primary mt-4 mb-3">Great support!!</h4>
                                Review Text
                                <div class="review-text">
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quis nam id facilis, provident doloremque placeat eveniet molestias laboriosam. Optio, esse.</p>
                                </div>
                                Quotation Icon
                                <div class="quot-icon">
                                    <img class="avatar-md" src="assets/img/icon/quote.png" alt="">
                                </div>
                            </div>
                            Reviewer
                            <div class="reviewer media bg-gray p-4">
                                Reviewer Thumb
                                <div class="reviewer-thumb">
                                    <img class="avatar-lg radius-100" src="assets/img/avatar/avatar-3.png" alt="">
                                </div>
                                Reviewer Media
                                <div class="reviewer-meta media-body align-self-center ml-4">
                                    <h5 class="reviewer-name color-primary mb-2">Junaid Hasan</h5>
                                    <h6 class="text-secondary fw-6">CEO, Themeland</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!-- ***** Review Area End ***** -->

        <!-- ***** FAQ Area Start ***** -->
        <!-- <section id="faq" class="section faq-area style-two ptb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        Section Heading
                        <div class="section-heading text-center">
                            <h2 class="text-capitalize">Frequently asked questions</h2>
                            <p class="d-none d-sm-block mt-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati dignissimos quae quo ad iste ipsum officiis deleniti asperiores sit.</p>
                            <p class="d-block d-sm-none mt-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati.</p>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-12">
                        FAQ Content
                        <div class="faq-content">
                            Kerala eats Accordion
                            <div class="accordion" id="Kerala eats-accordion">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-6">
                                        Single Card
                                        <div class="card border-0">
                                            Card Header
                                            <div class="card-header bg-inherit border-0 p-0">
                                                <h2 class="mb-0">
                                                    <button class="btn px-0 py-3" type="button">
                                                        How to install Kerala eats?
                                                    </button>
                                                </h2>
                                            </div>
                                            Card Body
                                            <div class="card-body px-0 py-3">
                                                The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text.
                                            </div>
                                        </div>
                                        Single Card
                                        <div class="card border-0">
                                            Card Header
                                            <div class="card-header bg-inherit border-0 p-0">
                                                <h2 class="mb-0">
                                                    <button class="btn px-0 py-3" type="button">
                                                        Can I get support from the Author?
                                                    </button>
                                                </h2>
                                            </div>
                                            Card Body
                                            <div class="card-body px-0 py-3">
                                                Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.
                                            </div>
                                        </div>
                                        Single Card
                                        <div class="card border-0">
                                            Card Header
                                            <div class="card-header bg-inherit border-0 p-0">
                                                <h2 class="mb-0">
                                                    <button class="btn px-0 py-3" type="button">
                                                        Do you have a free trail?
                                                    </button>
                                                </h2>
                                            </div>
                                            Card Body
                                            <div class="card-body px-0 py-3">
                                                It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        Single Card
                                        <div class="card border-0">
                                            Card Header
                                            <div class="card-header bg-inherit border-0 p-0">
                                                <h2 class="mb-0">
                                                    <button class="btn px-0 py-3" type="button">
                                                        How can I edit my personal information?
                                                    </button>
                                                </h2>
                                            </div>
                                            Card Body
                                            <div class="card-body px-0 py-3">
                                                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.
                                            </div>
                                        </div>
                                        Single Card
                                        <div class="card border-0">
                                            Card Header
                                            <div class="card-header bg-inherit border-0 p-0">
                                                <h2 class="mb-0">
                                                    <button class="btn px-0 py-3" type="button">
                                                        Contact form isn't working?
                                                    </button>
                                                </h2>
                                            </div>
                                            Card Body
                                            <div class="card-body px-0 py-3">
                                                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.
                                            </div>
                                        </div>
                                        Single Card
                                        <div class="card border-0">
                                            Card Header
                                            <div class="card-header bg-inherit border-0 p-0">
                                                <h2 class="mb-0">
                                                    <button class="btn px-0 py-3" type="button">
                                                        Contact form isn't working?
                                                    </button>
                                                </h2>
                                            </div>
                                            Card Body
                                            <div class="card-body px-0 py-3">
                                                There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <p class="text-body text-center pt-4 px-3 fw-5">Haven't find suitable answer? <a href="#">Tell us what you need.</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!-- ***** FAQ Area End ***** -->

        <!-- ***** Download Area Start ***** -->
        <section class="section download-area overlay-dark ptb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-9">
                        <!-- Download Text -->
                        <div class="download-text text-center mb-4">
                            <h2 class="text-white">Kerala eats is available for all devices</h2>
                            <!-- <p class="text-white my-3 d-none d-sm-block">Kerala eats is available for all devices, consectetur adipisicing elit. Itaque at harum quam explicabo. Aliquam optio, delectus, dolorem quod neque eos totam. Delectus quae animi tenetur voluptates doloribus commodi dicta modi aliquid deserunt, quis maiores nesciunt autem, aperiam natus.</p> -->
                            <!-- <p class="text-white my-3 d-block d-sm-none">Kerala eats is available for all devices, consectetur adipisicing elit. Vel neque, cumque. Temporibus eligendi veniam, necessitatibus aut id labore nisi quisquam.</p> -->
                            <!-- Store Buttons -->
                            <div class="button-group store-buttons d-flex flex-wrap justify-content-center">
                                <a target="_blank" href="https://play.google.com/store/apps/details?id=com.kerala_eats" class="res-margin">
                                    <img src="<?php echo base_url(); ?>assets/web/img/icon/google-play.png" alt="">
                                </a>
                                <a target="_blank" href="https://apps.apple.com/in/app/kerala-eats/id1572846103" class="res-margin">
                                    <img src="<?php echo base_url(); ?>assets/web/img/icon/app-store.png" alt="">
                                </a>
                            </div>
                            <span class="d-inline-block text-white fw-3 font-italic mt-3">* Available on iPhone, iPad and all Android devices</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ***** Download Area End ***** -->

        <!-- ***** Subscribe Area Start ***** -->
        <!-- <section class="section subscribe-area ptb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        <div class="subscribe-content text-center">
                            <h2>Subscribe to get updates</h2>
                            <p class="my-4">By subscribing you will get newsleter, promotions adipisicing elit. Architecto beatae, asperiores tempore repudiandae saepe aspernatur unde voluptate sapiente quia ex.</p>
                            Subscribe Form
                            <form class="subscribe-form">
                                <div class="form-group">
                                    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter your email">
                                </div>
                                <button type="submit" class="btn btn-lg btn-block">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->
        <!-- ***** Subscribe Area End ***** -->

        <!--====== Contact Area Start ======-->
        <section id="contact" class="contact-area bg-gray ptb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-10 col-lg-8">
                        <!-- Section Heading -->
                        <div class="section-heading text-center">
                            <h2 class="text-capitalize">CONTACT US</h2>
                             <p class="d-none d-sm-block mt-4">Address: 22 Sin Ming Ln, #06-76 Midview City, Singapore 573969 </p>
                            <!--<p class="d-block d-sm-none mt-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati.</p> -->
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between">
                    <div class="col-12 col-md-6 col-lg-5">
                        <!-- Contact Us -->
                        <div class="contact-us">
                            
                            <ul>
                                
                                <li class="py-2">
                                    <a class="media align-items-center" href="tel:+6563030607">
                                        <div class="social-icon mr-3">
                                            <i class="fas fa-phone-alt"></i>
                                        </div>
                                        <div>
                                            <span class="media-body align-self-center">Phone: +65 6303 0607 </span> <br>
                                        </div>
                                    </a>
                                </li>
                                 <li class="py-2">
                                    <a class="media align-items-center" href="https://api.whatsapp.com/send?phone=+917356786320" target="_blank">
                                        <div class="social-icon mr-3">
                                            <i class="fab fa-whatsapp" style="font-size: 22px;"></i>
                                        </div>
                                        <div>
                                            <span class="media-body align-self-center"> WhatsApp: +91 73567 86320 </span> <br>
                                        </div>
                                    </a>
                                </li>
                                <li class="py-2">
                                    <a class="media align-items-center" href="mailto:sales@keralafooddelivery.com" traget="_blank">
                                        <div class="social-icon mr-3">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <span class="media-body align-self-center">Email: sales@keralafooddelivery.com </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 pt-4 pt-md-0">
                        <!-- Contact Box -->
                        <div class="contact-box text-center">
                            <!-- Contact Form -->
                            <form id="contact-form" method="POST" action="https://theme-land.com/Kerala eats/demo/assets/php/mail.php">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="name" placeholder="Name" required="required">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="email" placeholder="Email" required="required">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="subject" placeholder="Subject" required="required">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <textarea class="form-control" name="message" placeholder="Message" required="required"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-lg btn-block mt-3"><span class="text-white pr-3"><i class="fas fa-paper-plane"></i></span>Send Message</button>
                                    </div>
                                </div>
                            </form>
                            <p class="form-message"></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--====== Contact Area End ======-->

        <!--====== Height Emulator Area Start ======-->
        <div class="height-emulator d-none d-lg-block"></div>
        <!--====== Height Emulator Area End ======-->

        <!--====== Footer Area Start ======-->
        <footer class="footer-area footer-fixed">
            <!-- Footer Top -->
            <div class="footer-top ptb_100">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <!-- Logo -->
                                <a class="navbar-brand" href="#">
                                    <img class="logo" src="<?php echo base_url(); ?>assets/web/img/logo/logo-orange.png" alt="">
                                </a>
                                <p class="mt-2 mb-3"> Kerala eats is specially catered to delivery Kerala Food in island-wide Singapore. Are you a fan of authentic Kerala spices? think no more Kerala eats is here to satisfy your cravings.  </p>
                                <!-- Social Icons -->
                                <div class="social-icons d-flex">
                                    <a class="facebook" href="#">
                                        <i class="fab fa-facebook-f"></i>
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a class="twitter" href="#">
                                        <i class="fab fa-twitter"></i>
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a class="google-plus" href="#">
                                        <i class="fab fa-google-plus-g"></i>
                                        <i class="fab fa-google-plus-g"></i>
                                    </a>
                                    <a class="vine" href="#">
                                        <i class="fab fa-vine"></i>
                                        <i class="fab fa-vine"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <!-- Footer Title -->
                                <h3 class="footer-title mb-2">Useful Links</h3>
                                <ul>
                                    <li class="py-2"><a class=" scroll" href="#home">Home</a></li>
                                    <li class="py-2"><a class=" scroll" href="#features">Features</a></li>
                                    <li class="py-2"><a class=" scroll" href="#howKerala_eatsWork">How it Works</a></li>
                                    <li class="py-2"><a class=" scroll" href="#screenshots">Screenshots</a></li>
                                    <li class="py-2"><a class=" scroll" href="#review">Reviews</a></li>
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <!-- Footer Title -->
                                <h3 class="footer-title mb-2">Product Help</h3>
                                <ul>
                                    <!-- <li class="py-2"><a class=" scroll" href="#faq">FAQ</a></li>
                                    <li class="py-2"><a class=" scroll" href="#">Privacy Policy</a></li>
                                    <li class="py-2"><a class=" scroll" href="#">Terms &amp; Conditions</a></li> -->
                                    <li class="py-2"><a class=" scroll" href="#contact">Contact</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <!-- Footer Items -->
                            <div class="footer-items">
                                <!-- Footer Title -->
                                <h3 class="footer-title mb-2">Download</h3>
                                <!-- Store Buttons -->
                                <div class="button-group store-buttons store-black d-flex flex-wrap">
                                    <a target="_blank" href="https://play.google.com/store/apps/details?id=com.kerala_eats">
                                        <img src="<?php echo base_url(); ?>assets/web/img/icon/google-play-black.png" alt="">
                                    </a>
                                    <a target="_blank" href="https://apps.apple.com/in/app/kerala-eats/id1572846103">
                                        <img src="<?php echo base_url(); ?>assets/web/img/icon/app-store-black.png" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <!-- Copyright Area -->
                            <div class="copyright-area d-flex flex-wrap justify-content-center justify-content-sm-between text-center py-4">
                                <!-- Copyright Left -->
                                <div class="copyright-left">&copy; Copyrights 2021  All rights reserved.</div>
                                <!-- Copyright Right -->
                                <!-- <div class="copyright-right">Made with <i class="fas fa-heart"></i> By <a href="#">Theme Land</a></div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!--====== Footer Area End ======-->
    </div>


    <!-- ***** All jQuery Plugins ***** -->

    <!-- jQuery(necessary for all JavaScript plugins) -->
    <script src="<?php echo base_url(); ?>assets/web/js/jquery/jquery.min.js"></script>

    <!-- Bootstrap js -->
    <script src="<?php echo base_url(); ?>assets/web/js/bootstrap/popper.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/web/js/bootstrap/bootstrap.min.js"></script>

    <!-- Plugins js -->
    <script src="<?php echo base_url(); ?>assets/web/js/plugins/plugins.min.js"></script>

    <!-- Active js -->
    <script src="<?php echo base_url(); ?>assets/web/js/active.js"></script>
</body>

</html>