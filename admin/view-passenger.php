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

$page_title = "View Passenger";
require_once "dashboard-header.php";

$username = $_GET["username"];

$passenger = new Passenger($database_connection, $username);
$reservations = Reservation::get_reservations_by_passenger($database_connection, $username);

$completed_journey_reservations = Reservation::filter_reservations_by_journey_status($reservations, 1);
$uncompleted_journey_reservations = Reservation::filter_reservations_by_journey_status($reservations, 0);
?>


            <div class="tm-section tm-section-pad tm-bg-img tm-position-relative">
                <div class="container ie-h-align-center-fix">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 mt-3 mt-md-0 ml-auto mr-auto">
                            <div class="tm-bg-white tm-p-4">
                            
                                <h1 class = "text-center">View Passenger</h1>

                                <?php
                                    if ($passenger->username != null) {
                                ?>
                                        
                                <div class="table-responsive mx-auto">
                                    <table class="table table-striped">
                                        <h2 class="caption-top text-center mt-4">Passenger Details</h2>
                                        <tbody>
                                            <tr>
                                                <th>Username</th>
                                                <td><?php echo $passenger->username?></td>
                                            </tr>

                                            <tr>
                                                <th>Name</th>
                                                <td><?php echo $passenger->get_full_name()?></td>
                                            </tr>

                                            <tr>
                                                <th>Gender</th>
                                                <td><?php echo $passenger->gender?></td>
                                            </tr>

                                            <tr>
                                                <th>Phone Number</th>
                                                <td><?php echo $passenger->phone_number?></td>
                                            </tr>

                                            <tr>
                                                <th>Email Address</th>
                                                <td><?php echo $passenger->email_address?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="table-responsive mx-auto">
                                    <table class="table table-striped">
                                        <h2 class="caption-top text-center mt-4">Upcoming Journeys</h2>
                                        <thead>
                                            <tr>
                                                <th class="text-center">Reservation ID</th>
                                                <th class="text-center">Station</th>
                                                <th class="text-center">Destination</th>
                                                <th class="text-center">Journey Date</th>
                                                <th class="text-center">Journey Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach ($uncompleted_journey_reservations as $reservation) {
                                            ?>

                                            <tr>
                                                <td>
                                                    <a href = "view-reservation.php?reservation-id=<?php echo $reservation->reservation_id?>">
                                                        <?php echo $reservation->reservation_id?>
                                                    </a>
                                                </td>
                                                <td><?php echo $reservation->train->route->station?></td>
                                                <td><?php echo $reservation->train->route->destination?></td>
                                                <td><?php echo convert_date_to_readable_form($reservation->journey_date)?></td>
                                                <td><?php echo $reservation->journey_time?></td>
                                            </tr>

                                            <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="table-responsive mx-auto">
                                    <table class="table table-striped">
                                        <h2 class="caption-top text-center mt-4">Completed Journeys</h2>
                                        <thead>
                                            <tr>
                                                <th class="text-center">Reservation ID</th>
                                                <th class="text-center">Station</th>
                                                <th class="text-center">Destination</th>
                                                <th class="text-center">Journey Date</th>
                                                <th class="text-center">Journey Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                foreach ($completed_journey_reservations as $reservation) {
                                            ?>

                                            <tr>
                                                <td>
                                                    <a href = "view-reservation.php?reservation-id=<?php echo $reservation->reservation_id?>">
                                                        <?php echo $reservation->reservation_id?>
                                                    </a>
                                                </td>
                                                <td><?php echo $reservation->train->route->station?></td>
                                                <td><?php echo $reservation->train->route->destination?></td>
                                                <td><?php echo convert_date_to_readable_form($reservation->journey_date)?></td>
                                                <td><?php echo $reservation->journey_time?></td>
                                            </tr>

                                            <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php
                                    } else {
                                ?>
                                <div class="text-center font-weight-bold">
                                    No passenger with the username: <?php echo $username?> was found.
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