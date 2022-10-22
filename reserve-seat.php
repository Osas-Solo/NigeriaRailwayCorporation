<?php
session_start();

if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    $login_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/login.php";
    header("Location: " . $login_url);
}

require_once("database_configuration.php");
require_once("entities.php");

$page_title = "Reserve Seat";
require_once "header.php";

if (!isset($_POST["station"])) {
    $home_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/index.php";
    header("Location: " . $home_url);
}

$station = $_POST["station"];
$destination = $_POST["destination"];
$journey_date = $_POST["journey-date"];
$journey_time = $_POST["journey-time"];

$train = new Train($database_connection, $station, $destination);

if (!isset($train->train_id)) {
    $is_route_available = 0;
} else {
    $is_route_available = 1;
}
?>


            <div class="tm-section tm-section-pad tm-bg-img tm-position-relative">
                <div class="container ie-h-align-center-fix">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 mt-3 mt-md-0 ml-auto mr-auto">
                            <div class="tm-bg-white tm-p-4">
                                <form action="check-out.php" method="post">
                                    <h1 class = "text-center">Reserve Seat</h1>
                                    
                                    <div class="form-group mb-4">
                                        <label for="station" class="font-weight-bold">Station:</label>
                                        <input type="text" id="station" name="station" class="form-control" value="<?php echo $station?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="destination" class="font-weight-bold">Destination:</label>
                                        <input type="text" id="destination" name="destination" class="form-control" value="<?php echo $destination?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="journey-date" class="font-weight-bold">Journey Date:</label>
                                        <input type="date" id="journey-date" name="journey-date" class="form-control" value="<?php echo $journey_date?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="journey-time" class="font-weight-bold">Journey Time:</label>
                                        <input type="text" id="journey-time" name="journey-time" class="form-control" value="<?php echo $journey_time?>" readonly/>
                                    </div>

                                    <?php
                                        if (!$is_route_available) {
                                    ?>
                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold">
                                            Sorry, we do not currently offer a route from
                                            <?php echo $station?> to <?php echo $destination?>. 
                                            Please go back to pick a different destination.
                                        </label>
                                    </div>
                                    <?php        
                                        } else {
                                    ?>
                                    <div class="form-group mb-4">
                                        <label for="train-id" class="font-weight-bold">Train Id:</label>
                                        <input type="text" id="train-id" name="train-id" class="form-control" value="<?php echo $train->train_id?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="fare" class="font-weight-bold">Fare:</label>
                                        <input type="number" id="fare" name="fare" class="form-control" value="<?php echo $train->route->fare?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="seat-number" class="font-weight-bold">Seat Number:</label>
                                        <select name="seat-number" class="form-control py-1" id="seat-number">
                                            <?php
                                            $reservations = Reservation::get_reservations_by_date($database_connection,
                                                $train->train_id, $journey_date, $journey_time);
                                            $seat_numbers = array();

                                            for ($i = 1; $i <= $train->number_of_seats; $i++) {
                                                if (!Reservation::is_seat_reserved($i, $reservations)) {
                                                    array_push($seat_numbers, $i);
                                            ?>
                                            <option value="<?php echo $i?>">
                                                <?php echo $i?>
                                            </option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <?php
                                            $are_seats_available = 1;

                                            if (count($seat_numbers) == 0) {
                                                $are_seats_available = 0;
                                        ?>
                                        <div>
                                            <label class="font-weight-bold">
                                                Sorry, you can no longer make a reservation on 
                                                <?php echo convert_date_to_readable_form($journey_date) ?> 
                                                as there is no seat currently available. Please go back to
                                                select a different journey date.
                                            </label>
                                        </div>                                      
                                        <?php
                                            }
                                        ?>
                                    </div>

                                    <?php
                                        if ($are_seats_available) {
                                    ?>
                                    <div class="form-group mb-4">
                                        <label for="ticket-type" class="font-weight-bold">Ticket Type:</label>
                                        <select name="ticket-type" class="form-control py-1" id="ticket-type">
                                            <?php
                                            $ticket_types = TicketType::get_ticket_types($database_connection);
                                            foreach ($ticket_types as $ticket_type) {
                                            ?>
                                            <option value="<?php echo $ticket_type->ticket_type_id?>">
                                                <?php echo $ticket_type->ticket_type_name?>
                                            </option>
                                            <?php
                                            }
                                            ?>
                                        </select>                                      
                                        <label class="font-weight-bold">
                                            A gold ticket provides extra comfort, space and access to entertainment. 
                                            It costs an extra  &#8358;<?php echo $ticket_types[1]->extra_fare?>.<br>
                                            While a VIP ticket provides extra comfort, space, access to entertainment and meals during your journey.
                                            It costs an extra  &#8358;<?php echo $ticket_types[2]->extra_fare?>.
                                        </label> 
                                    </div>
                                    <button type="submit" class="btn btn-primary tm-btn-primary" name="submit">Check Out</button>
                                    <?php
                                        }
                                    ?>

                                    <?php
                                        }
                                    ?>
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

<?php
$database_connection->close();
?>