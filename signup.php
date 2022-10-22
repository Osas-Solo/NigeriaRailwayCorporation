<?php
session_start();

if (isset($_SESSION["username"])) {
    session_destroy();
}

require_once("database_configuration.php");
require_once("entities.php");

if (isset($_POST["submit"])) {
    $first_name = cleanse_data($_POST["first-name"], $database_connection);
    $last_name = cleanse_data($_POST["last-name"], $database_connection);
    $username = cleanse_data($_POST["username"], $database_connection);
    $email_address = cleanse_data($_POST["email-address"], $database_connection);
    $phone_number = cleanse_data($_POST["phone-number"], $database_connection);
    $gender = cleanse_data($_POST["gender"], $database_connection);
    $password = cleanse_data($_POST["password"], $database_connection);
    $password_confirmer = cleanse_data($_POST["confirm-password"], $database_connection);

    $passenger = new Passenger($database_connection, $username);

    if ($passenger->username != null) {
        $is_username_in_use = 1;
    } else if (is_name_valid($first_name) && is_name_valid($last_name) && is_name_valid($username) 
        && is_email_address_valid($email_address) && is_password_valid($password) 
        && is_password_confirmed($password, $password_confirmer) && is_phone_number_valid($phone_number)) {
        $insert_query = "INSERT INTO passengers(username, first_name, last_name, gender, phone_number, email_address, password) VALUES 
                            ('$username', '$first_name', '$last_name', '$gender', '$phone_number', '$email_address', SHA('$password'))";
        
        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        if ($database_connection->query($insert_query)) {
            $alert = "<script>
                        if (confirm('You\'ve successfully completed your registration. You may now proceed to login.')) {";
            $login_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/login.php";
            $alert .= "window.location.replace('$login_url');
                        }";    
            $alert .= "</script>";
            echo $alert;
        }
    }   //  end of if details are valid
}   //  end of if submit is set


$page_title = "Signup";
require_once "header.php";
?>


            <div class="tm-section tm-section-pad tm-bg-img tm-position-relative">
                <div class="container ie-h-align-center-fix">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-5 mt-3 mt-md-0 ml-auto mr-auto">
                            <div class="tm-bg-white tm-p-4">
                                <form action="signup.php" method="post">
                                    <h1 class = "text-center">Signup</h1>
                                    
                                    <div class="form-group">
                                        <label for="first-name" class="font-weight-bold">First Name:</label>
                                        <input type="text" id="first-name" name="first-name" class="form-control" placeholder="First Name"  required value="<?php
                                            if (isset($_POST["first-name"])) {
                                                echo $_POST["first-name"];
                                            }
                                        ?>" onfocus="hideFirstNameErorMessage()"/>
                                        <label id = "first-name-error-message" class="font-weight-bold">
                                            <?php
                                                if (isset($_POST["first_name"])) {
                                                    if (!is_name_valid($_POST["first-name"])) {
                                                        echo "Please enter a first name";
                                                    }
                                                }
                                            ?>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="last-name" class="font-weight-bold">Last Name:</label>
                                        <input type="text" id="last-name" name="last-name" class="form-control" placeholder="Last Name"  required value="<?php
                                            if (isset($_POST["last-name"])) {
                                                echo $_POST["last-name"];
                                            }
                                        ?>" onfocus="hideLastNameErorMessage()"/>
                                        <label id = "last-name-error-message" class="font-weight-bold">
                                            <?php
                                                if (isset($_POST["last_name"])) {
                                                    if (!is_name_valid($_POST["last-name"])) {
                                                        echo "Please enter a last name";
                                                    }
                                                }
                                            ?>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="username" class="font-weight-bold">Username:</label>
                                        <input type="text" id="username" name="username" class="form-control" placeholder="Username"  required value="<?php
                                            if (isset($_POST["username"])) {
                                                echo $_POST["username"];
                                            }                
                                        ?>" onfocus="hideUserNameErrorMessage()"/>
                                        <label id = "username-error-message" class="font-weight-bold">
                                            <?php
                                                if(isset($_POST["username"])) {
                                                    if (!is_name_valid($_POST["username"])) {
                                                        echo "Please enter a username";
                                                    } else {
                                                        $is_username_in_use = 0;

                                                        $passenger = new Passenger($database_connection, $_POST["username"]);

                                                        if ($passenger->username != null) {
                                                            $is_username_in_use = 1;
                                                        }   //  end of if username is null

                                                        if (isset($is_username_in_use)) {
                                                            if ($is_username_in_use) {
                                                                echo $_POST["username"] . " is already in use";
                                                            }   //  end of if username is in use
                                                        }   //  end of if is_username_in_use is set
                                                    }   //  end of else
                                                }   //  if username is set
                                            ?>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="font-weight-bold">Password:</label>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required onchange="checkPasswordValidity()"/>
                                        <label class="font-weight-bold">
                                            Password length should be at least 8 characters. 
                                            Password must contain a lowercase character, uppercase character and a digit

                                            <br><br>
                                            <span id = "password-error-message"
                                            <?php
                                                if(isset($_POST["password"])) {
                                                    if (!is_password_valid($_POST["password"])) {
                                                        echo "";
                                                    } else {
                                                        echo "style = 'display: none'";
                                                    }
                                                } else {
                                                    echo "style = 'display: none'";
                                                }
                                            ?>
                                            >Please enter a valid password</span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm-password" class="font-weight-bold">Confirm Password:</label>
                                        <input type="password" id="confirm-password" name="confirm-password" class="form-control" placeholder="Confirm Password"  required onchange="checkPasswordConfirmation()"/>
                                        <label id = "confirm-password-error-message" class="font-weight-bold"
                                            <?php
                                                if (isset($_POST["confirm-password"])) {
                                                    if (!is_password_confirmed($_POST["password"], $_POST["confirm-password"])) {
                                                        echo "";
                                                    } else {
                                                        echo "style = 'display: none'";
                                                    }
                                                } else {
                                                    echo "style = 'display: none'";
                                                }
                                            ?>>
                                                Passwords do not match
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone-number" class="font-weight-bold">Phone Number:</label>
                                        <input type="text" id="phone-number" name="phone-number" class="form-control" placeholder="Phone Number" required value="<?php
                                            if (isset($_POST["phone-number"])) {
                                                echo $_POST["phone-number"];
                                            }                
                                        ?>" onfocus="hidePhoneNumberErrorMessage()"/>
                                        <label id = "phone-number-error-message" class="font-weight-bold">
                                            <?php
                                                if (isset($_POST["phone-number"])) {
                                                    if (!is_phone_number_valid($_POST["phone-number"])) {
                                                        echo "Please enter a valid phone number";
                                                    }
                                                }
                                            ?>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="email-address" class="font-weight-bold">Email Address:</label>
                                        <input type="email" id="email-address" name="email-address" class="form-control" placeholder="Email Address" required value="<?php
                                            if (isset($_POST["email-address"])) {
                                                echo $_POST["email-address"];
                                            }                
                                        ?>" onfocus="hideEmailAddressErrorMessage()"/>
                                        <label id = "email-address-error-message" class="font-weight-bold">
                                            <?php
                                                if (isset($_POST["email-address"])) {
                                                    if (!is_email_address_valid($_POST["email-address"])) {
                                                        echo "Please enter a valid email address";
                                                    }
                                                }
                                            ?>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="gender" class="font-weight-bold">Gender:</label><br>
                                        <input type="radio" name="gender" value="M" checked> Male<br>
                                        <input type="radio" name="gender" value="F"> Female<br>
                                    </div>
                                    <button type="submit" class="btn btn-primary tm-btn-primary" name="submit">Signup</button>

                                    <div class="form-group mt-4 font-weight-bold text-center">
                                        Already have an account? <a href="login.php">Login instead.</a>
                                    </div>
                                </form>

                                <script src="js/signup-validation.js"></script>
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