<?php
$page_title = "Home";
require_once "header.php";
?>


            <div class="tm-section tm-bg-img" id="tm-section-1">
                <div class="tm-bg-white ie-container-width-fix-2">
                    <div class="container ie-h-align-center-fix">
                        <div class="row">
                            <div class="col-xs-12 ml-auto mr-auto ie-container-width-fix">
                                <form action="reserve-seat.php" method="post" class="tm-search-form tm-section-pad-2">
                                    <div class="form-row tm-search-form-row mb-2">
                                        <div class="form-group tm-form-element tm-form-element-50">
                                            <label for="journey-date" class="font-weight-bold">Journey Date:</label>                                            
                                            <input name="journey-date" type="date" class="form-control" id="journey-date" required>
                                        </div>
                                    </div>
                                    <div class="form-row tm-search-form-row mb-2">
                                        <div class="form-group tm-form-element tm-form-element-50">
                                            <label for="journey-time" class="font-weight-bold">Journey Time:</label>
                                            <select name="journey-time" class="form-control py-1" id="journey-time" required>
                                                <option value="08:00:00">08:00:00</option>
                                                <option value="11:15:00">11:15:00</option>
                                                <option value="14:30:00">14:30:00</option>
                                                <option value="17:45:00">17:45:00</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row tm-search-form-row mx-auto mb-2">
                                        <div class="form-group tm-form-element tm-form-element-2">
                                            <label for="station" class="font-weight-bold">Station:</label>                                            
                                            <select name="station" class="form-control py-1" id="station" required onchange="updateDestinations()">
                                                <option value="Warri">Warri</option>
                                                <option value="Benin">Benin</option>
                                                <option value="Asaba">Asaba</option>
                                                <option value="Lagos">Lagos</option>
                                                <option value="Onitsha">Onitsha</option>
                                                <option value="Port Harcourt">Port Harcourt</option>
                                            </select>                                        
                                        </div>
                                        <div class="form-group tm-form-element tm-form-element-2">
                                            <label for="destination" class="font-weight-bold">Destination:</label>                                            
                                            <select name="destination" class="form-control py-1" id="destination" required>
                                                <option value="Benin">Benin</option>
                                                <option value="Asaba">Asaba</option>
                                                <option value="Lagos">Lagos</option>
                                                <option value="Onitsha">Onitsha</option>
                                                <option value="Port Harcourt">Port Harcourt</option>
                                                <option value="Warri">Warri</option>
                                            </select>                                        
                                        </div>
                                        <div class="form-group tm-form-element tm-form-element-2 mt-4">
                                            <button type="submit" class="btn btn-primary tm-btn-search">Check Availability</button>
                                        </div>
                                      </div>
                                    </div>  

                                    <script src="js/journey-validation.js"></script>
                                </form>
                            </div>                        
                        </div>      
                    </div>
                </div>                  
            </div>
                                    
            
            <div class="tm-section tm-section-pad tm-bg-img tm-position-relative" id="contact">
                <div class="container ie-h-align-center-fix">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 mt-3 mt-md-0 ml-auto mr-auto">
                            <div class="tm-bg-white tm-p-4">
                                <form action="" method="post" class="contact-form">
                                    <div class="form-group">
                                        <input type="text" id="contact_name" name="contact_name" class="form-control" placeholder="Name"  required/>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" id="contact_email" name="contact_email" class="form-control" placeholder="Email"  required/>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" id="contact_subject" name="contact_subject" class="form-control" placeholder="Subject"  required/>
                                    </div>
                                    <div class="form-group">
                                        <textarea id="contact_message" name="contact_message" class="form-control" rows="9" placeholder="Message" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary tm-btn-primary">Send Message Now</button>
                                </form>
                            </div>                            
                        </div>
                    </div>        
                </div>
            </div>
            
            <footer class="tm-bg-dark-blue">
                <div class="container">
                    <div class="row">
                        <p class="col-sm-12 text-center tm-font-light tm-color-white p-4 tm-margin-b-0">
                        Copyright &copy; <span class="tm-current-year">2022</span> Nigeria Railway Corporation
                        </p>        
                    </div>
                </div>                
            </footer>
        </div>
        
        <!-- load JS files -->
        <script src="js/jquery-1.11.3.min.js"></script>             <!-- jQuery (https://jquery.com/download/) -->
        <script src="js/popper.min.js"></script>                    <!-- https://popper.js.org/ -->       
        <script src="js/bootstrap.min.js"></script>                 <!-- https://getbootstrap.com/ -->
        <script src="js/datepicker.min.js"></script>                <!-- https://github.com/qodesmith/datepicker -->
        <script src="js/jquery.singlePageNav.min.js"></script>      <!-- Single Page Nav (https://github.com/ChrisWojcik/single-page-nav) -->
        <script src="slick/slick.min.js"></script>                  <!-- http://kenwheeler.github.io/slick/ -->
        <script>
            function setCarousel() {
                
                if ($('.tm-article-carousel').hasClass('slick-initialized')) {
                    $('.tm-article-carousel').slick('destroy');
                } 

                if($(window).width() < 438){
                    // Slick carousel
                    $('.tm-article-carousel').slick({
                        infinite: false,
                        dots: true,
                        slidesToShow: 1,
                        slidesToScroll: 1
                    });
                }
                else {
                 $('.tm-article-carousel').slick({
                        infinite: false,
                        dots: true,
                        slidesToShow: 2,
                        slidesToScroll: 1
                    });   
                }
            }

       
            $(document).ready(function(){

                $(window).on("scroll", function() {
                    if($(window).scrollTop() > 100) {
                        $(".tm-top-bar").addClass("active");
                    } else {
                        //remove the background property so it comes transparent again (defined in your css)
                       $(".tm-top-bar").removeClass("active");
                    }
                });      
                
                // Slick carousel
                setCarousel();
                setPageNav();

                $(window).resize(function() {
                  setCarousel();
                  setPageNav();
                });

                // Close navbar after clicked
                $('.nav-link').click(function(){
                    $('#mainNav').removeClass('show');
                });


                // Update the current year in copyright
                $('.tm-current-year').text(new Date().getFullYear());                           
            });

        </script>             

</body>
</html>