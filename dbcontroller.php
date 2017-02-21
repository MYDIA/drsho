<?php

	// this will avoid mysql_connect() deprecation error.
//	error_reporting( ~E_DEPRECATED & ~E_NOTICE );
//
//	define('DBHOST', 'mpwebservicesnet.ipagemysql.com');
//	define('DBUSER', 'drsho');
//	define('DBPASS', 'metcs633');
//	define('DBNAME', 'drsho');
//
//	$conn = mysqli_connect(DBHOST,DBUSER,DBPASS,DBNAME);
//	//$dbcon = mysqli_select_db(DBNAME);
//
//	if ( !$conn ) {
//		die("Connection failed : " . mysqli_error());
//	}
	
//	if ( !$dbcon ) {
//		die("Database Connection failed : " . mysqli_error());
//	}


session_start();

class DBController {
    private $host = "mpwebservicesnet.ipagemysql.com";
    private $user = "drsho";
    private $password = "metcs633";
    private $database = "drsho";
    private $conn;

    function __construct() {

        $this->connectDB();

    }

    function connectDB() {
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die('Connect Error (' . $this->conn->connect_errno . ') '
                . $$this->conn->connect_error);
        }

        if (mysqli_connect_error()) {
            die('Connect Error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
        }

        $_SESSION['mysqli'] = $this->conn;

//        global $conn;
//        $conn = new mysqli($this->host,$this->user,$this->password, $this->database);
//        return $conn;
    }



    function runQuery($query) {
       // global $conn;
        //$result = mysqli_query($conn,$query);
        $result = $this->conn->query($query);

        if(!empty($result))
            return $result;
    }

    function numRows($query) {
        //global $conn;
        $result  = $this->conn->query($query);
        $rowcount = mysqli_num_rows($result);
        return $rowcount;
    }

    function updateQuery($query) {
        //global $conn;
        $result = $this->conn->query($query);
        if (!$result) {
            die('Invalid query: ' . mysqli_error());
        } else {
            return $result;
        }
    }

    function insertQuery($query) {
       // global $conn;
        $result = $this->conn->query($query);
        if (!$result) {
            die('Invalid query: ' . mysqli_error());
        } else {
            return $result;
        }
    }

    function deleteQuery($query) {
        //global $conn;
        $result = $this->conn->query($query);
        if (!$result) {
            die('Invalid query: ' . mysqli_error());
        } else {
            return $result;
        }
    }


    public function showHealthRecord()
    {

        $mysqli = $_SESSION['mysqli'];
        $userid = $_SESSION['uid'];

        $query = "SELECT id, date FROM user_health_record 
                    WHERE user_account_id = $userid
                    ORDER BY date ASC";

        $result = $mysqli->query($query);

        echo "<table border='1'><thead><tr><th>No.</th><th>Exam Date</th><th>Action</th></tr></thead><tbody>";

        while ($row = $result->fetch_array()) {
            $rows[] = $row;
        }
        $count = 1;
        foreach ($rows as $row) {

            echo "<tr><td>$count</td>";
            echo "<td>" . $row['date'] . "</td>";
            echo "<td><a href='details.php?id=" . $row['id'] . "'>Details</a></td></tr>";

            $count++;
        }

        echo "</tbody></table>";
        $result->free();

    }



}
?>