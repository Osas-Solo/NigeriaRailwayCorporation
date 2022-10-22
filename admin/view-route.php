<?php
session_start();

if (isset($_SESSION["admin"])) {
    $username = $_SESSION["admin"];
} else {
    $login_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/index.php";
    header("Location: " . $login_url);
}

require_once("../database_configuration.php");
require_once("../entities.php");

$page_title = "View Route";
require_once "dashboard-header.php";

$route_id = $_GET["route-id"];

$route = new Route($database_connection, $route_id);

if (isset($_GET["submit"])) {
    $route_id = cleanse_data(($_GET["route-id"]), $database_connection);    
    $station = cleanse_data($_GET["station"], $database_connection);
    $destination = cleanse_data($_GET["destination"], $database_connection);
    $fare = cleanse_data($_GET["fare"], $database_connection);

    $update_query = "UPDATE routes SET station = '$station', destination = '$destination', fare = $fare 
	                    WHERE route_id = '$route_id'";

    if ($database_connection->connect_error) {
        die("Connection failed: " . $database_connection->connect_error);
    }
    
    if ($database_connection->query($update_query)) {
        $alert = "<script>
                    if (confirm('Route details updated successfully')) {";
        $route_url =    "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/view-route.php?route-id=" . $route_id;
        $alert .=       "window.location.replace('$route_url');
                    } else {";
        $alert .=       "window.location.replace('$route_url');
                    }";    
        $alert .= "</script>";
        echo $alert;
    }
}   //  end of if submit is set
?>


            <div class="tm-section tm-section-pad tm-bg-img tm-position-relative">
                <div class="container ie-h-align-center-fix">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 mt-3 mt-md-0 ml-auto mr-auto">
                            <div class="tm-bg-white tm-p-4">

                                <?php
                                    if ($route->route_id != null) {
                                ?>

                                <form action="view-route.php" method="get">
                                    <h1 class = "text-center">Route Details</h1>
                                    
                                    <div class="form-group">
                                        <label for="route-id" class="font-weight-bold">Route ID:</label>
                                        <input type="text" id="route-id" name="route-id" class="form-control" value="<?phP echo $route->route_id?>" required readonly/>
                                    </div>
                                    <div class="form-group">
                                        <label for="station" class="font-weight-bold">Station:</label>
                                        <input type="text" id="station" name="station" class="form-control" value="<?phP echo $route->station?>" required/>
                                    </div>
                                    <div class="form-group">
                                        <label for="destination" class="font-weight-bold">Destination:</label>
                                        <input type="text" id="destination" name="destination" class="form-control" value="<?phP echo $route->destination?>" required/>
                                    </div>
                                    <div class="form-group">
                                        <label for="fare" class="font-weight-bold">Fare:</label>
                                        <input type="number" id="fare" name="fare" class="form-control" value="<?phP echo $route->fare?>" min="500" step="50" required/>
                                        <label class="font-weight-bold">
                                            Fare must be a minumum of &#8358;500 and should be a multiple of 50 and 100.
                                        </label> 
                                    </div>
                                    <button type="submit" class="btn btn-primary tm-btn-primary" name="submit">Update Route</button>
                                </form>

                                <?php
                                    } else {
                                ?>
                                <div class="text-center font-weight-bold">
                                    No route with the ID: <?php echo $route_id?> was found.
                                </div>
                                <?php
                                    }
                                ?>

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