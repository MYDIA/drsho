<?php
ob_start();
session_start();
require_once 'dbcontroller.php';

$userName = "";

// if session is not set this will redirect to home page
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
} else {
    $userName = $_SESSION['user'];
}

$curLoc = basename($_SERVER['PHP_SELF'], ".php");

?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Welcome - <?php echo $userName; ?></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="style.css" type="text/css"/>

    </head>
    <body>


    <div id="wrapper">

        <?php include("navigation.php"); ?>

        <div class="container">

            <div class="page-header">
                <h1>Your Dashboard</h1>
            </div>


            <h2>My Health Exam History</h2>
            <p>Click a row for a detail view.</p>

            <div class="panel-group" id="accordion">

                <?php

                $db_handle = new DBController();

                $query = "SELECT * FROM user_health_record 
                    WHERE user_account_id = 1
                    ORDER BY date ASC";

                $result = $db_handle->runQuery($query);

                while ($row = $result->fetch_array()) {
                    $rows[] = $row;
                }
                $count = 1;
                foreach ($rows as $row) {
                    $collapse = "collapse" . $count;
                    ?>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion"
                                   href="#<?=$collapse?>"><?= $row['date'] ?></a>
                            </h4>
                        </div>

                        <div id="<?=$collapse?>" class="panel-collapse collapse">
                            <ul class="list-group">
                                <li class="list-group-item"><?= $row['health_type1'] ?> |
                                    <?= $row['health_type1_level'] ?> |

                                    <?php
                                    if ($row['health_type1_level'] >= 0 && $row['health_type1_level'] <= 149)
                                        echo "Normal";
                                    else if ($row['health_type1_level'] >= 150 && $row['health_type1_level'] <= 199)
                                        echo "Borderline-high";
                                    else if ($row['health_type1_level'] >= 200 && $row['health_type1_level'] <= 499)
                                        echo "High";
                                    else
                                        echo "Very High";
                                    ?>

                                </li>
                                <li class="list-group-item">
                                    <?= $row['health_type2'] ?> |
                                    <?= $row['health_type2_level'] ?> |

                                    <?php
                                    if ($row['health_type2_level'] >= 50 && $row['health_type2_level'] <= 99)
                                        echo "Ideal";
                                    else if ($row['health_type2_level'] >= 100 && $row['health_type2_level'] <= 129)
                                        echo "Close to Ideal";
                                    else if ($row['health_type2_level'] >= 130 && $row['health_type2_level'] <= 159)
                                        echo "Borderline-high";
                                    else if ($row['health_type2_level'] >= 160 && $row['health_type2_level'] <= 189)
                                        echo "High";
                                    else if ($row['health_type2_level'] >= 190 && $row['health_type2_level'] <= 300)
                                        echo "Very High";
                                    ?>

                                </li>
                                <li class="list-group-item">
                                    <?= $row['health_type3'] ?> |
                                    <?= $row['health_type3_level'] ?> |

                                    <?php
                                    if ($row['health_type3_level'] >= 20 && $row['health_type3_level'] <= 39)
                                        echo "Low (high heart disease risk)";
                                    else if ($row['health_type3_level'] >= 40 && $row['health_type3_level'] <= 59)
                                        echo "Normal (but the higher the better)";
                                    else if ($row['health_type3_level'] >= 60 && $row['health_type3_level'] <= 90)
                                        echo "Best (offers protection against heart disease)";
                                    ?>

                                </li>
                                <li class="list-group-item">
                                    <?= $row['health_type4'] ?> |
                                    <?= $row['health_type4_level'] ?> |

                                    <?php
                                    if ($row['health_type4_level'] >= 80 && $row['health_type4_level'] <= 200)
                                        echo "Ideal";
                                    else if ($row['health_type4_level'] >= 201 && $row['health_type4_level'] <= 239)
                                        echo "Borderline-high";
                                    else if ($row['health_type4_level'] >= 240 && $row['health_type4_level'] <= 500)
                                        echo "High";
                                    ?>

                                </li>

                            </ul>
                            <div class="panel-footer"><p><span style="color:red;">Recommendation</span>: Information goes here...</p></div>
                        </div>
                    </div>

                <?
                $count++;
                }

                $result->free();
                ?>

            </div>
        </div>


    </div>

    <footer>
        <?php include "footer.php"; ?>
    </footer>

    </div>



    </body>
    </html>
<?php ob_end_flush(); ?>