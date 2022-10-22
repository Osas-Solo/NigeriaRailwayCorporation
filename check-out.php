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

$page_title = "Check Out";
require_once "header.php";

if (!isset($_POST["station"])) {
    $reserve_seat_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/reserve-seat.php";
    header("Location: " . $reserve_seat_url);
}

$station = $_POST["station"];
$destination = $_POST["destination"];
$journey_date = $_POST["journey-date"];
$journey_time = $_POST["journey-time"];

$passenger = new Passenger($database_connection, $username);
$train = new Train($database_connection, $station, $destination);

$seat_number = $_POST["seat-number"];

$ticket_types = TicketType::get_ticket_types($database_connection);
$ticket_type = TicketType::get_ticket_type_by_id($_POST["ticket-type"], $ticket_types);

$ticket_price = $train->route->fare + $ticket_type->extra_fare;

if (!isset($_POST["reservation-id"])) {
    $reservation_id = $train->route->route_id . "-" . date("YmdGis");
} else {
    $reservation_id = $_POST["reservation-id"];
    $transaction_reference = $_POST["transaction-reference"];
}

if (isset($_POST["make-payment"])) {
    $reservation_date = date("Y-m-d");
    $train_id = $train->train_id;
    $route_id = $train->route->route_id;
    $ticket_type_id = $ticket_type->ticket_type_id;

    $insert_query = "INSERT INTO reservations(reservation_id, transaction_reference, reservation_date, 
                        username, train_id, ticket_price, seat_number, route_id, journey_date, journey_time, 
                         ticket_type_id) 
                        VALUES 
                        ('$reservation_id', '$transaction_reference', '$reservation_date',
                         '$username', '$train_id', $ticket_price, $seat_number, '$route_id', '$journey_date', 
                         '$journey_time', '$ticket_type_id')";

    if ($database_connection->connect_error) {
        die("Connection failed: " . $database_connection->connect_error);
    }

    if ($database_connection->query($insert_query)) {
        $alert = "<script>
                    if (confirm('Reservation booked successfully')) {";
        $reservation_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/view-reservation.php?reservation-id=" . $reservation_id;
        $alert .=           "window.location.replace('$reservation_url');
                    } else {";
        $alert .=           "window.location.replace('$reservation_url');
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
                                <form id = "checkout-form">
                                    <h1 class = "text-center">Check Out</h1>

                                    <input type="text" id="reservation-id" name="reservation-id" class="form-control" value="<?php echo $reservation_id?>" style="display:none;"/>
                                    <input type="text" id="transaction-reference" name="transaction-reference" class="form-control" style="display:none;"/>

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
                                    <div class="form-group mb-4">
                                        <label for="train-id" class="font-weight-bold">Train Id:</label>
                                        <input type="text" id="train-id" name="train-id" class="form-control" value="<?php echo $train->train_id?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="seat-number" class="font-weight-bold">Seat Number:</label>
                                        <input type="number" name="seat-number" class="form-control py-1" id="seat-number" value="<?php echo $seat_number?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="passenger-name" class="font-weight-bold">Passenger Name:</label>
                                        <input type="text" id="passenger-name" name="passenger-name" class="form-control" value="<?php echo $passenger->get_full_name()?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="email-address" class="font-weight-bold">Email Address:</label>
                                        <input type="text" id="email-address" name="email-address" class="form-control" value="<?php echo $passenger->email_address?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="ticket-price" class="font-weight-bold">Ticket Price:</label>
                                        <input type="number" id="ticket-price" name="ticket-price" class="form-control" value="<?php echo $ticket_price?>" readonly/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="ticket-type" class="font-weight-bold">Ticket Type:</label>
                                        <input name="ticket-type" class="form-control py-1" id="ticket-type" value="<?php echo $ticket_type->ticket_type_id?>" style="display:none;" readonly/>
                                        <input name="ticket-type-name" class="form-control py-1" id="ticket-type-name" value="<?php echo $ticket_type->ticket_type_name?>" disabled/>
                                    </div>
                                    <button type="submit" id ="make-payment" class="btn btn-primary tm-btn-primary" name="make-payment" onclick="payWithPaystack()">Make Payment</button>
                                </form>

                                <script src = "https://js.paystack.co/v1/inline.js"></script>
                                <script src = "js/paystack.js"></script>

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