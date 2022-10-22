<?php
date_default_timezone_set("Africa/Lagos");
require_once("database_configuration.php");

class Passenger {
    public $username;
    public $first_name;
    public $last_name; 
    public $gender;
    public $phone_number;
    public $email_address;
    public $password;

    function __construct(mysqli $database_connection = null, string $username = "", string $password = "") {
        if (isset($database_connection)) {
            $query = "SELECT * FROM passengers WHERE username = '$username'";
            $query .= ($password != "") ? " AND password = SHA('$password')" : "";
    
            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }
    
            $query_result = $database_connection->query($query);
    
            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();
    
                $this->username = $row["username"];
                $this->first_name = $row["first_name"];
                $this->last_name = $row["last_name"];
                $this->phone_number = $row["phone_number"];
                $this->email_address = $row["email_address"];
                $this->password = $row["password"];
    
                switch($row["gender"]) {
                    case 'M':
                        $this->gender = "Male";
                        break;
    
                    case 'F':
                        $this->gender = "Female";
                        break;
                }
            }   //  end of if number of rows > 0    
        }   //  end of if $database_connection is set
    }   //  end of constructor

    public function get_full_name() {
        return $this->first_name . " " . $this->last_name;
    }

    public static function get_passengers(mysqli $database_connection) {
        $passengers = array();
        
        $query = "SELECT * FROM passengers ORDER BY username";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $passenger = new Passenger();

                $passenger->username = $row["username"];
                $passenger->first_name = $row["first_name"];
                $passenger->last_name = $row["last_name"];
                $passenger->phone_number = $row["phone_number"];
                $passenger->email_address = $row["email_address"];
    
                switch($row["gender"]) {
                    case 'M':
                        $passenger->gender = "Male";
                        break;
    
                    case 'F':
                        $passenger->gender = "Female";
                        break;
                }
    
                array_push($passengers, $passenger);    
            }
        }   //  end of if number of rows > 0

        return $passengers;
    }   //  end of get_passengers()
}   //  end of Passenger class

class Admin {
    public $username;
    public $password;

    function __construct(mysqli $database_connection, string $username, string $password) {
        $query = "SELECT * FROM admins WHERE username = '$username' AND password = SHA('$password')";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            $row = $query_result->fetch_assoc();

            $this->username = $row["username"];
            $this->password = $row["password"];
        }   //  end of if number of rows > 0
    }   //  end of constructor
}   //  end of Admin class

class Route {
    public $route_id;
    public $station;
    public $destination;
    public $fare;

    function __construct(mysqli $database_connection = null, string $route_id = "") {
        if (isset($database_connection)) {
            $query = "SELECT * FROM routes WHERE route_id = '$route_id'";

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->route_id = $row["route_id"];
                $this->station = $row["station"];
                $this->destination = $row["destination"];
                $this->fare = $row["fare"];
            }   //  end of if number of rows > 0
        }   //  end of if $database_connection is set
    }   //  end of constructor

    public static function get_routes(mysqli $database_connection) {
        $routes = array();
        
        $query = "SELECT * FROM routes ORDER BY station";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $route = new Route();

                $route->route_id = $row["route_id"];
                $route->station = $row["station"];
                $route->destination = $row["destination"];
                $route->fare = $row["fare"];
    
                array_push($routes, $route);    
            }
        }   //  end of if number of rows > 0

        return $routes;
    }   //  end of get_routes()
}   //  end of Route class

class TicketType {
    public $ticket_type_id;
    public $ticket_type_name; 
    public $extra_fare;

    function __construct(string $ticket_type_id, string $ticket_type_name, float $extra_fare) {
        $this->ticket_type_id = $ticket_type_id;
        $this->ticket_type_name = $ticket_type_name;
        $this->extra_fare = $extra_fare;
    }

    public static function get_ticket_types(mysqli $database_connection) {
        $ticket_types = array();
        
        $query = "SELECT * FROM ticket_types ORDER BY extra_fare";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {    
                $ticket_type = new TicketType($row["ticket_type_id"], $row["ticket_type_name"], $row["extra_fare"]);

                array_push($ticket_types, $ticket_type);    
            }
        }   //  end of if number of rows > 0

        return $ticket_types;
    }   //  end of get_ticket_types()

    public static function get_ticket_type_by_id(string $ticket_type_id, array $ticket_types) {
        foreach ($ticket_types as $ticket_type) {
            if (strcmp($ticket_type_id, $ticket_type->ticket_type_id) == 0) {
                return $ticket_type;
            }
        }
    }   //  end of get_ticket_type_by_id()
}   //  end of TicketType class

class Train {
    public $train_id;
    public $number_of_seats;
    public $route;

    function __construct(mysqli $database_connection = null, string $station = "", string $destination = "") {
        if (isset($database_connection)) {
            $query = "SELECT * FROM trains b 
                        INNER JOIN routes r ON b.route_id = r.route_id
                        WHERE r.station = '$station' AND r.destination = '$destination'";

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->train_id = $row["train_id"];
                $this->number_of_seats = $row["number_of_seats"];
                $this->route = new Route($database_connection, $row["route_id"]);
            }   //  end of if number of rows > 0
        }   //  end of if $database_connection is set
    }   //  end of constructor

    public static function get_trains(mysqli $database_connection) {
        $trains = array();
        
        $query = "SELECT * FROM trains";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $train = new Train();

                $train->train_id = $row["train_id"];
                $train->number_of_seats = $row["number_of_seats"];
                $train->route = new Route($database_connection, $row["route_id"]);
    
                array_push($trains, $train);    
            }
        }   //  end of if number of rows > 0

        return $trains;
    }   //  end of get_trains()
}   //  end of Train class

class Reservation {
    public $reservation_id;
    public $transaction_reference;
    public $reservation_date;
    public $passenger;
    public $train;
    public $ticket_price; 
    public $seat_number;
    public $journey_date;
    public $journey_time;
    public $is_journey_completed;
    public $ticket_type;

    function __construct(mysqli $database_connection = null, string $reservation_id = "") {
        if (isset($database_connection)) {
            $query = "SELECT * FROM reservations r 
                        INNER JOIN passengers p ON r.username = p.username
                        INNER JOIN trains b ON r.train_id = b.train_id
                        INNER JOIN routes t ON r.route_id = t.route_id
                        WHERE reservation_id = '$reservation_id'";

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->reservation_id = $row["reservation_id"];
                $this->transaction_reference = $row["transaction_reference"];
                $this->reservation_date = $row["reservation_date"];
                $this->passenger = new Passenger($database_connection, $row["username"]);
                $this->train = new Train($database_connection, $row["station"], $row["destination"]);
                $this->ticket_price = $row["ticket_price"];
                $this->seat_number = $row["seat_number"];
                $this->journey_date = $row["journey_date"];
                $this->journey_time = $row["journey_time"];
                $this->is_journey_completed = $row["journey_completed"];
                $ticket_types = TicketType::get_ticket_types($database_connection);
                $this->ticket_type = TicketType::get_ticket_type_by_id($row["ticket_type_id"], $ticket_types);
            }   //  end of if number of rows > 0
        }   //  end of if $database_connection is set
    }   //  end of constructor

    public static function get_reservations(mysqli $database_connection) {
        $reservations = array();
        
        $query = "SELECT * FROM reservations r 
                    INNER JOIN passengers p ON r.username = p.username
                    INNER JOIN trains b ON r.train_id = b.train_id
                    INNER JOIN routes t ON r.route_id = t.route_id
                    ORDER BY journey_date DESC, journey_time DESC";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $reservation = new Reservation();

                $reservation->reservation_id = $row["reservation_id"];
                $reservation->transaction_reference = $row["transaction_reference"];
                $reservation->reservation_date = $row["reservation_date"];
                $reservation->passenger = new Passenger($database_connection, $row["username"]);
                $reservation->train = new Train($database_connection, $row["station"], $row["destination"]);
                $reservation->ticket_price = $row["ticket_price"];
                $reservation->seat_number = $row["seat_number"];
                $reservation->journey_date = $row["journey_date"];
                $reservation->journey_time = $row["journey_time"];
                $reservation->is_journey_completed = $row["journey_completed"];
                $ticket_types = TicketType::get_ticket_types($database_connection);
                $reservation->ticket_type = TicketType::get_ticket_type_by_id($row["ticket_type_id"], $ticket_types);
    
                array_push($reservations, $reservation);    
            }
        }   //  end of if number of rows > 0

        return $reservations;
    }   //  end of get_reservations()

    public static function get_reservations_by_passenger(mysqli $database_connection, string $username) {
        $reservations = array();
        
        $query = "SELECT * FROM reservations r 
                    INNER JOIN passengers p ON r.username = p.username
                    INNER JOIN trains b ON r.train_id = b.train_id
                    INNER JOIN routes t ON r.route_id = t.route_id
                    WHERE p.username = '$username'
                    ORDER BY journey_date DESC, journey_time DESC";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $reservation = new Reservation();

                $reservation->reservation_id = $row["reservation_id"];
                $reservation->transaction_reference = $row["transaction_reference"];
                $reservation->reservation_date = $row["reservation_date"];
                $reservation->passenger = new Passenger($database_connection, $row["username"]);
                $reservation->train = new Train($database_connection, $row["station"], $row["destination"]);
                $reservation->ticket_price = $row["ticket_price"];
                $reservation->seat_number = $row["seat_number"];
                $reservation->journey_date = $row["journey_date"];
                $reservation->journey_time = $row["journey_time"];
                $reservation->is_journey_completed = $row["journey_completed"];
                $ticket_types = TicketType::get_ticket_types($database_connection);
                $reservation->ticket_type = TicketType::get_ticket_type_by_id($row["ticket_type_id"], $ticket_types);
    
                array_push($reservations, $reservation);    
            }
        }   //  end of if number of rows > 0

        return $reservations;
    }   //  end of get_reservations_by_passenger()

    public static function filter_reservations_by_journey_status(array $reservations, int $is_journey_completed) {
        $completed_journey_reservations = array();
        $uncompleted_journey_reservations = array();

        foreach ($reservations as $reservation) {
            if ($is_journey_completed) {
                if ($reservation->is_journey_completed) {
                    array_push($completed_journey_reservations, $reservation);
                }
            } else if (!$is_journey_completed) {
                if (!$reservation->is_journey_completed) {
                    array_push($uncompleted_journey_reservations, $reservation);
                }                
            }
        }   //  end of foreach

        if ($is_journey_completed) {
            return $completed_journey_reservations;
        } else {
            return $uncompleted_journey_reservations;
        }
    }   //  end of filter_reservations_by_journey_status()

    public static function get_reservations_by_date(mysqli $database_connection, string $train_id, string $journey_date,
                                                    string $journey_time) {
        $reservations = array();
        
        $query = "SELECT * FROM reservations r 
                    INNER JOIN passengers p ON r.username = p.username
                    INNER JOIN trains b ON r.train_id = b.train_id
                    INNER JOIN routes t ON r.route_id = t.route_id
                    WHERE b.train_id = '$train_id' AND journey_date = '$journey_date' AND journey_time = '$journey_time' 
                    AND journey_completed = 0";

        echo $query . "<br>";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $reservation = new Reservation();

                $reservation->reservation_id = $row["reservation_id"];
                $reservation->transaction_reference = $row["transaction_reference"];
                $reservation->reservation_date = $row["reservation_date"];
                $reservation->passenger = new Passenger($database_connection, $row["username"]);
                $reservation->train = new Train($database_connection, $row["station"], $row["destination"]);
                $reservation->ticket_price = $row["ticket_price"];
                $reservation->seat_number = $row["seat_number"];
                $reservation->journey_date = $row["journey_date"];
                $reservation->journey_time = $row["journey_time"];
                $reservation->is_journey_completed = $row["journey_completed"];
                $ticket_types = TicketType::get_ticket_types($database_connection);
                $reservation->ticket_type = TicketType::get_ticket_type_by_id($row["ticket_type_id"], $ticket_types);
    
                array_push($reservations, $reservation);    
            }
        }   //  end of if number of rows > 0

        return $reservations;
    }   //  end of get_reservations_by_date()

    public static function is_seat_reserved(int $seat_number, array $reservations) {
        foreach ($reservations as $reservation) {
            if ($seat_number == $reservation->seat_number) {
                return 1;
            }
        }

        return 0;
    }   //  end of is_seat_reserved
}   //  end of Reservation class

function cleanse_data($data, $database_connection) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_escape_string($database_connection, $data);
    
    return $data;
}

function is_name_valid(string $name) {
    return strlen($name) > 0;
}

function is_email_address_valid(string $email_address) {
    $email_regex = "/^[A-Za-z0-9+_.-]+@(.+\..+)$/";

    return preg_match($email_regex, $email_address);
}

function is_phone_number_valid(string $phone_number) {
    $phone_number_regex = "/[0-9]{11}/";

    return preg_match($phone_number_regex, $phone_number);
}

function is_password_valid(string $password) {
    $lowercase_regex = "/[a-z]/";
    $uppercase_regex = "/[A-Z]/";
    $digit_regex = "/[0-9]/";

    return preg_match($lowercase_regex, $password) && preg_match($uppercase_regex, $password) 
            && preg_match($digit_regex, $password) && strlen($password) >= 8;
}

function is_password_confirmed(string $password, string $password_confirmer) {
    return $password == $password_confirmer;
}

function convert_date_to_readable_form(string $reverse_date) {
    $reverse_date_regex = "/(\d{4})-(\d{2})-(\d{2})/";

    preg_match($reverse_date_regex, $reverse_date, $match_groups);

    $year = $match_groups[1];
    $month = $match_groups[2];
    $day = $match_groups[3];

    $month_names = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"];

    $month = $month_names[$month - 1];

    return $month . " " . $day . ", " . $year;
}
?>