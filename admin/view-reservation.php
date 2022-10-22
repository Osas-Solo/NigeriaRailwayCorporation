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

$page_title = "View Reservation";
require_once "dashboard-header.php";

$reservation_id = $_GET["reservation-id"];

$reservation = new Reservation($database_connection, $reservation_id);

if (isset($_GET["submit"])) {
    $today = date("Y-m-d");

    $update_query = "UPDATE reservations SET journey_completed = 1 
	                    WHERE reservation_id = '$reservation_id' AND journey_date <= '$today'";

    if ($database_connection->connect_error) {
        die("Connection failed: " . $database_connection->connect_error);
    }

    $database_connection->query($update_query);
    $reservation = new Reservation($database_connection, $reservation_id);
    
    if ($reservation->is_journey_completed) {
        echo "<script>alert('Journey status changed to completed')</script>";
    } else {
        echo "<script>alert('Sorry, this journey hasn\'t been made yet')</script>";
    }
}   //  end of if submit is set

?>


            <div class="tm-section tm-section-pad tm-bg-img tm-position-relative">
                <div class="container ie-h-align-center-fix">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 mt-3 mt-md-0 ml-auto mr-auto">
                            <div class="tm-bg-white tm-p-4">
                            
                                <h1 class = "text-center">View Reservation</h1>
                                        
                                <?php
                                    if ($reservation->reservation_id != null) {
                                ?>

                                <div class="table-responsive-md mx-auto">
                                    <table class="table table-striped">
                                        <h2 class="caption-top text-center mt-4">Reservation Details</h2>
                                        <tbody>
                                            <tr>
                                                <th>Reservation ID</th>
                                                <td><?php echo $reservation->reservation_id?></td>
                                            </tr>
                                            <tr>
                                                <th>Transaction Reference</th>
                                                <td><?php echo $reservation->transaction_reference?></td>
                                            </tr>
                                            <tr>
                                                <th>Reservation Date</th>
                                                <td><?php echo convert_date_to_readable_form($reservation->reservation_date)?></td>
                                            </tr>
                                            <tr>
                                                <th>Passenger</th>
                                                <td><?php echo $reservation->passenger->get_full_name()?></td>
                                            </tr>
                                            <tr>
                                                <th>Station</th>
                                                <td><?php echo $reservation->train->route->station?></td>
                                            </tr>
                                            <tr>
                                                <th>Destination</th>
                                                <td><?php echo $reservation->train->route->destination?></td>
                                            </tr>
                                            <tr>
                                                <th>Bus ID</th>
                                                <td><?php echo $reservation->train->train_id?></td>
                                            </tr>
                                            <tr>
                                                <th>Ticket Type</th>
                                                <td><?php echo $reservation->ticket_type->ticket_type_name?></td>
                                            </tr>
                                            <tr>
                                                <th>Ticket Price</th>
                                                <td>&#8358; <?php echo $reservation->ticket_price?></td>
                                            </tr>
                                            <tr>
                                                <th>Seat Number</th>
                                                <td><?php echo $reservation->seat_number?></td>
                                            </tr>
                                            <tr>
                                                <th>Journey Date</th>
                                                <td><?php echo convert_date_to_readable_form($reservation->journey_date)?></td>
                                            </tr>
                                            <tr>
                                                <th>Journey Status</th>
                                                <td>
                                                    <?php 
                                                        switch($reservation->is_journey_completed) {
                                                            case 1:
                                                                echo "Completed";
                                                                break;
                                                            case 0:
                                                                echo "Upcoming";
                                                                break;
                                                        }
                                                    ?>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>

                                    <?php
                                        if (!$reservation->is_journey_completed) {
                                    ?>
                                    <div class="tm-bg-white tm-p-4">
                                        <form action="view-reservation.php" method="get">
                                            <input type="text" name="reservation-id" value="<?php echo $reservation_id?>" style="display:none"/>

                                            <button type="submit" class="btn btn-primary tm-btn-primary" name="submit">Complete Journey</button>
                                        </form>
                                    </div>
                                    <?php
                                        }
                                    ?>
                                </div>

                                <?php
                                    } else {
                                ?>
                                <div class="text-center font-weight-bold">
                                    No reservation with the ID: <?php echo $reservation_id?> was found.
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