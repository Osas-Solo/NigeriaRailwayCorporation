<?php
if (isset($_SESSION["admin"])) {
    session_destroy();
}

require_once("../database_configuration.php");
require_once("../entities.php");

if (isset($_POST["submit"])) {
    $username = cleanse_data($_POST["username"], $database_connection);
    $password = cleanse_data($_POST["password"], $database_connection);

    $admin = new Admin($database_connection, $username, $password);

    if ($admin->username != null) {
        session_start();
        $_SESSION["admin"] = $admin->username;

        $dashboard_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/dashboard.php";
        header("Location: " . $dashboard_url);
    }
}   //  end of if submit is set

$page_title = "Admin Login";
require_once "dashboard-header.php";
?>


            <div class="tm-section tm-section-pad tm-bg-img tm-position-relative">
                <div class="container ie-h-align-center-fix">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 mt-3 mt-md-0 ml-auto mr-auto">
                            <div class="tm-bg-white tm-p-4">
                                <form action="index.php" method="post">
                                    <h1 class = "text-center">Admin Login</h1>
                                    
                                    <div class="form-group">
                                        <label for="username" class="font-weight-bold">Username:</label>
                                        <input type="text" id="username" name="username" class="form-control" placeholder="Username"  required/>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="font-weight-bold">Password:</label>
                                        <input type="password" id="pasword" name="password" class="form-control" placeholder="Password"  required/>
                                    </div>
                                    <button type="submit" class="btn btn-primary tm-btn-primary" name="submit">Login</button>
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
                        Copyright &copy; <span class="tm-current-year">2021</span> Swift Limited
                        </p>        
                    </div>
                </div>                
            </footer>
        </div>
        
        <!-- load JS files -->
        <script src="../js/jquery-1.11.3.min.js"></script>             <!-- jQuery (https://jquery.com/download/) -->
        <script src="../js/popper.min.js"></script>                    <!-- https://popper.js.org/ -->       
        <script src="../js/bootstrap.min.js"></script>                 <!-- https://getbootstrap.com/ -->
        <script src="../js/datepicker.min.js"></script>                <!-- https://github.com/qodesmith/datepicker -->
        <script src="../js/jquery.singlePageNav.min.js"></script>      <!-- Single Page Nav (https://github.com/ChrisWojcik/single-page-nav) -->
        <script src="../slick/slick.min.js"></script>                  <!-- http://kenwheeler.github.io/slick/ -->
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

                // Date Picker
                const pickerCheckIn = datepicker('#inputCheckIn');
                const pickerCheckOut = datepicker('#inputCheckOut');
                
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

<?php
$database_connection->close();
?>