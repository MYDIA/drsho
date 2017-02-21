<?php
ob_start();
session_start();
if (isset($_SESSION['user']) != "") {
    header("Location: home.php");
}
include_once 'dbcontroller.php';

$db_handle = new DBController();

$curLoc = basename($_SERVER['PHP_SELF'], ".php");
$userName = "Guest";
$error = false;
$minPassLength = 6;

if (isset($_POST['btn-signup'])) {

    // clean user inputs to prevent sql injections
    $fname = trim($_POST['fname']);
    $fname = strip_tags($fname);
    $fname = htmlspecialchars($fname);

    $mname = trim($_POST['mname']);
    $mname = strip_tags($mname);
    $mname = htmlspecialchars($mname);

    $lname = trim($_POST['lname']);
    $lname = strip_tags($lname);
    $lname = htmlspecialchars($lname);

    $email = trim($_POST['email']);
    $email = strip_tags($email);
    $email = htmlspecialchars($email);

    $pass = trim($_POST['pass']);
    $pass = strip_tags($pass);
    $pass = htmlspecialchars($pass);

    // basic name validation

    if (empty($fname)) {
        $error = true;
        $fnameError = "Please enter your first name.<br>";
    } else if (strlen($fname) < 3) {
        $error = true;
        $fnameError = "First name must have at least 3 characters.<br>";
    } else if (!preg_match("/^[a-zA-Z ]+$/", $fname)) {
        $error = true;
        $fnameError = "First name must contain alphabets and space.<br>";
    }

    if (!empty($mname)) {
        if (!preg_match("/^[a-zA-Z ]+$/", $mname)) {
            $error = true;
            $mnameError = "Middle name must contain alphabets and space.<br>";
        }
    }

    if (empty($lname)) {
        $error = true;
        $lnameError = "Please enter your last name.<br>";
    } else if (strlen($lname) < 3) {
        $error = true;
        $lnameError = "Last name must have at least 3 character.<br>";
    } else if (!preg_match("/^[a-zA-Z ]+$/", $lname)) {
        $error = true;
        $lnameError = "Last name must contain alphabets and space.<br>";
    }
    //basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $emailError = "Please enter valid email address.";
    } else {
        // check email exist or not
        $query = "SELECT userEmail FROM users WHERE userEmail='$email'";
        $result = $db_handle->runQuery($query);
        $count = mysqli_num_rows($result);
        if ($count != 0) {
            $error = true;
            $emailError = "Provided Email is already in use.";
        }
    }
    // password validation
    if (empty($pass)) {
        $error = true;
        $passError = "Please enter password.";
    } else if (strlen($pass) < $minPassLength) {
        $error = true;
        $passError = "Password must have at least $minPassLength characters.";
    }

    // password encrypt using SHA256();
    $password = hash('sha256', $pass);

    // if there's no error, continue to signup
    if (!$error) {

        $query = "INSERT INTO users(userName,userEmail,userPass) VALUES('$fname','$email','$password')";
        $result = $db_handle->runQuery($query);

        if ($result) {
            $errTyp = "success";
            $errMSG = "Successfully registered, you may login now";
            unset($name);
            unset($email);
            unset($pass);
        } else {
            $errTyp = "danger";
            $errMSG = "Something went wrong, try again later...";
        }

    }


}
?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Dr. Sho - Login & Registration System</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"/>
        <link rel="stylesheet" href="style.css" type="text/css"/>
    </head>
    <body>

    <div id="wrapper">

        <?php include("navigation.php"); ?>

        <div class="container">


            <div id="login-form">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">

                    <div class="col-md-12">


                        <div class="form-group">
                            <h2 class="">Sign Up.</h2>
                        </div>

                        <div class="form-group">
                            <hr/>
                        </div>

                        <?php
                        if (isset($errMSG)) {

                            ?>
                            <div class="form-group">
                                <div class="alert alert-<?php echo ($errTyp == "success") ? "success" : $errTyp; ?>">
                                    <span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <input id="fname" type="text" name="fname" class="form-control" placeholder="First Name"
                                       maxlength="50" value="<?php echo $fname ?>" required/>

                                <input id="mname" type="text" name="mname" class="form-control" placeholder="Middle"
                                       maxlength="5" value="<?php echo $mname ?>"/>

                                <input id="lname" type="text" name="lname" class="form-control" placeholder="Last Name"
                                       maxlength="50" value="<?php echo $lname ?>" required/>
                            </div>
                            <span class="text-danger"><?php echo $fnameError; ?></span>
                            <span class="text-danger"><?php echo $mnameError; ?></span>
                            <span class="text-danger"><?php echo $lnameError; ?></span>

                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <input type="email" name="email" class="form-control"
                                       placeholder="Your Email (your.name@domain.com)"
                                       maxlength="40" value="<?php echo $email ?>" required/>
                            </div>
                            <span class="text-danger"><?php echo $emailError; ?></span>
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <input type="password" name="pass" class="form-control"
                                       placeholder="Password (min <?= $minPassLength ?> characters)" maxlength="15"
                                       required/>
                            </div>
                            <span class="text-danger"><?php echo $passError; ?></span>
                        </div>


                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <input type="text" name="address" class="form-control" placeholder="Street Address"
                                       maxlength="15" required/>
                            </div>
                            <span class="text-danger"><?php echo $addressError; ?></span>
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"></span>
                                <input type="text" name="address2" class="form-control"
                                       placeholder="Street Address 2 (e.g. Apt No, Suite No., Unit No.)"
                                       maxlength="15"/>
                            </div>
                            <span class="text-danger"><?php echo $address2Error; ?></span>
                        </div>

                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"></span>

                                <input id="city" type="text" name="city" class="form-control" placeholder="City"
                                       maxlength="15" required/>

                                <!--                            <input id="state" type="text" name="state" class="form-control" placeholder="State"-->
                                <!--                                   maxlength="2"/>-->

                                <select id="state" name="state" class="form-control" required>
                                    <option value="0">State</option>
                                    <option value="AL">AL</option>
                                    <option value="AK">AK</option>
                                    <option value="AZ">AZ</option>
                                    <option value="AR">AR</option>
                                    <option value="CA">CA</option>
                                    <option value="CO">CO</option>
                                    <option value="CT">CT</option>
                                    <option value="DE">DE</option>
                                    <option value="DC">DC</option>
                                    <option value="FL">FL</option>
                                    <option value="GA">GA</option>
                                    <option value="HI">HI</option>
                                    <option value="ID">ID</option>
                                    <option value="IL">IL</option>
                                    <option value="IN">IN</option>
                                    <option value="IA">IA</option>
                                    <option value="KS">KS</option>
                                    <option value="KY">KY</option>
                                    <option value="LA">LA</option>
                                    <option value="ME">ME</option>
                                    <option value="MD">MD</option>
                                    <option value="MA">MA</option>
                                    <option value="MI">MI</option>
                                    <option value="MN">MN</option>
                                    <option value="MS">MS</option>
                                    <option value="MO">MO</option>
                                    <option value="MT">MT</option>
                                    <option value="NE">NE</option>
                                    <option value="NV">NV</option>
                                    <option value="NH">NH</option>
                                    <option value="NJ">NJ</option>
                                    <option value="NM">NM</option>
                                    <option value="NY">NY</option>
                                    <option value="NC">NC</option>
                                    <option value="ND">ND</option>
                                    <option value="OH">OH</option>
                                    <option value="OK">OK</option>
                                    <option value="OR">OR</option>
                                    <option value="PA">PA</option>
                                    <option value="RI">RI</option>
                                    <option value="SC">SC</option>
                                    <option value="SD">SD</option>
                                    <option value="TN">TN</option>
                                    <option value="TX">TX</option>
                                    <option value="UT">UT</option>
                                    <option value="VT">VT</option>
                                    <option value="VA">VA</option>
                                    <option value="WA">WA</option>
                                    <option value="WV">WV</option>
                                    <option value="WI">WI</option>
                                    <option value="WY">WY</option>
                                </select>


                                <input id="zip" type="text" name="zip" class="form-control" placeholder="Zip Code"
                                       maxlength="10" required/>
                            </div>
                            <span class="text-danger"><?php echo $cityError; ?></span>
                            <span class="text-danger"><?php echo $stateError; ?></span>
                            <span class="text-danger"><?php echo $zipError; ?></span>
                        </div>

                        <div class="form-group">
                            <div class="input-group">

                                Date of Birth:<br>

                                <select id="DOBMonth" name="DOBMonth" class="form-control" required>
                                    <option> - Month -</option>
                                    <option value="January">January</option>
                                    <option value="Febuary">Febuary</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>

                                <select id="DOBDay" name="DOBDay" class="form-control" required>
                                    <option> - Day -</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select>

                                <select id="DOBYear" name="DOBYear" class="form-control" required>
                                    <option> - Year -</option>
                                    <option value="1993">2017</option>
                                    <option value="1993">2016</option>
                                    <option value="1993">2015</option>
                                    <option value="1993">2014</option>
                                    <option value="1993">2013</option>
                                    <option value="1993">2012</option>
                                    <option value="1993">2011</option>
                                    <option value="1993">2010</option>
                                    <option value="1993">2009</option>
                                    <option value="1993">2008</option>
                                    <option value="1993">2007</option>
                                    <option value="1993">2006</option>
                                    <option value="1993">2005</option>
                                    <option value="1993">2004</option>
                                    <option value="1993">2003</option>
                                    <option value="1993">2002</option>
                                    <option value="1993">2001</option>
                                    <option value="1993">2000</option>
                                    <option value="1993">1999</option>
                                    <option value="1993">1998</option>
                                    <option value="1993">1997</option>
                                    <option value="1993">1996</option>
                                    <option value="1993">1995</option>
                                    <option value="1993">1994</option>
                                    <option value="1993">1993</option>
                                    <option value="1992">1992</option>
                                    <option value="1991">1991</option>
                                    <option value="1990">1990</option>
                                    <option value="1989">1989</option>
                                    <option value="1988">1988</option>
                                    <option value="1987">1987</option>
                                    <option value="1986">1986</option>
                                    <option value="1985">1985</option>
                                    <option value="1984">1984</option>
                                    <option value="1983">1983</option>
                                    <option value="1982">1982</option>
                                    <option value="1981">1981</option>
                                    <option value="1980">1980</option>
                                    <option value="1979">1979</option>
                                    <option value="1978">1978</option>
                                    <option value="1977">1977</option>
                                    <option value="1976">1976</option>
                                    <option value="1975">1975</option>
                                    <option value="1974">1974</option>
                                    <option value="1973">1973</option>
                                    <option value="1972">1972</option>
                                    <option value="1971">1971</option>
                                    <option value="1970">1970</option>
                                    <option value="1969">1969</option>
                                    <option value="1968">1968</option>
                                    <option value="1967">1967</option>
                                    <option value="1966">1966</option>
                                    <option value="1965">1965</option>
                                    <option value="1964">1964</option>
                                    <option value="1963">1963</option>
                                    <option value="1962">1962</option>
                                    <option value="1961">1961</option>
                                    <option value="1960">1960</option>
                                    <option value="1959">1959</option>
                                    <option value="1958">1958</option>
                                    <option value="1957">1957</option>
                                    <option value="1956">1956</option>
                                    <option value="1955">1955</option>
                                    <option value="1954">1954</option>
                                    <option value="1953">1953</option>
                                    <option value="1952">1952</option>
                                    <option value="1951">1951</option>
                                    <option value="1950">1950</option>
                                    <option value="1949">1949</option>
                                    <option value="1948">1948</option>
                                    <option value="1947">1946</option>
                                    <option value="1947">1945</option>
                                    <option value="1947">1944</option>
                                    <option value="1947">1943</option>
                                    <option value="1947">1942</option>
                                    <option value="1947">1941</option>
                                    <option value="1947">1940</option>
                                    <option value="1947">1939</option>
                                    <option value="1947">1938</option>
                                    <option value="1947">1937</option>
                                    <option value="1947">1936</option>
                                    <option value="1947">1935</option>
                                    <option value="1947">1934</option>
                                    <option value="1947">1933</option>
                                    <option value="1947">1932</option>
                                    <option value="1947">1931</option>
                                    <option value="1947">1930</option>
                                    <option value="1947">1929</option>
                                    <option value="1947">1928</option>
                                    <option value="1947">1927</option>
                                    <option value="1947">1926</option>
                                    <option value="1947">1925</option>
                                    <option value="1947">1924</option>
                                    <option value="1947">1923</option>
                                    <option value="1947">1922</option>
                                    <option value="1947">1921</option>
                                    <option value="1947">1920</option>

                                </select>

                            </div>
                            <span class="text-danger"><?php echo $dobError; ?></span>
                        </div>

                        <div class="form-group">
                            <div class="input-group">

                                Height (feet and inches):<br>

                                <select id="feet" name="HeightFeet" class="form-control" required>
                                    <option> - Feet -</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>

                                <select id="inch" name="HeightInches" class="form-control" required>
                                    <option> - Inches -</option>
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                </select>


                                <!--                            <input  id="feet" type="text" name="height-feet" class="form-control" placeholder="Feet"-->
                                <!--                                    maxlength="2"/>-->
                                <!--                            <input id="inch" type="text" name="height-inch" class="form-control" placeholder="Inches"-->
                                <!--                                   maxlength="2"/>-->
                            </div>
                            <span class="text-danger"><?php echo $heightError; ?></span>
                        </div>

                        <div class="form-group">
                            <hr/>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn-primary" name="btn-signup">Sign Up</button>
                        </div>

                        <div class="form-group">
                            <hr/>
                        </div>

                        <div class="form-group">
                            <a href="index.php">Sign in Here...</a>
                        </div>

                    </div>

                </form>
            </div>
        </div>
        <footer>
            <?php include "footer.php"; ?>
        </footer>

    </div>
    <script src="assets/jquery-1.11.3-jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    </body>
    </html>
<?php ob_end_flush(); ?>