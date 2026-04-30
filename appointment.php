<?php
session_start();

if(isset($_SESSION["user"])){
    if($_SESSION["user"]=="" || $_SESSION['usertype']!='p'){
        header("location: ../login.php");
        exit();
    } else {
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
    exit();
}

include("../connection.php");

// USER
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch=$userrow->fetch_assoc();
$username=$userfetch["pname"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Bookings - Medcheck</title>

<link rel="stylesheet" href="../css/index.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- Accessibility Buttons -->
<div class="accessibility-controls">
    <button onclick="changeFontSize(1)">A+</button>
    <button onclick="changeFontSize(-1)">A-</button>
    <button onclick="resetFontSize()">Reset</button>
</div>
<!-- TOPBAR -->
<div class="topbar">
    <div class="logo">
        <i class="fa-solid fa-heart-pulse"></i> MEDCHECK
    </div>
    <div class="user">
        <i class="fa-solid fa-user"></i>
        Welcome back, <?php echo $username; ?>
    </div>
</div>

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="profile">
        <img src="../img/user.png">
        <h3><?php echo $username; ?></h3>
        <p>Patient</p>
    </div>

    <a href="index.php"><i class="fa-solid fa-house"></i>Home</a>
    <a href="doctors.php"><i class="fa-solid fa-user-doctor"></i> Doctors</a>
    <a href="schedule.php"><i class="fa-solid fa-calendar-check"></i> Sessions</a>


    <!-- UPDATED LABEL -->
    <a class="active" href="appointment.php">
        <i class="fa-solid fa-book"></i> My Bookings
    </a>

    <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
    <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<!-- HEADER -->
<div class="page-header">
    <h1><i class="fa-solid fa-book"></i> My Bookings</h1>
    <p>View and manage all your scheduled appointments.</p>
</div>

<!-- SEARCH / FILTER -->
<div class="card search-card">
    <form method="POST" class="search-form">
        <input type="date" name="sheduledate">
        <button type="submit">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
    </form>
</div>

<?php

$sqlmain = "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,
schedule.scheduledate,schedule.scheduletime,
appointment.apponum,appointment.appodate
from schedule
inner join appointment on schedule.scheduleid=appointment.scheduleid
inner join doctor on schedule.docid=doctor.docid
where appointment.pid=$userfetch[pid]
order by appointment.appodate desc";

$result = $database->query($sqlmain);

?>

<p style="margin:15px 5px;">
    Total Bookings: <b><?php echo $result->num_rows; ?></b>
</p>

<!-- GRID -->
<div class="session-grid">

<?php

if($result->num_rows == 0){
    echo "
    <div class='card'>
        <h3>No Bookings Found</h3>
        <p>You have no scheduled appointments yet.</p>
    </div>";
}else{

    while($row=$result->fetch_assoc()){

        $appoid=$row["appoid"];
        $title=$row["title"];
        $docname=$row["docname"];
        $date=$row["scheduledate"];
        $time=$row["scheduletime"];
        $num=$row["apponum"];
        $appodate=$row["appodate"];

        echo "
        <div class='session-card'>

            <div class='session-header'>
                <span><i class='fa-solid fa-hashtag'></i> OC-000-$appoid</span>
            </div>

            <h3><i class='fa-solid fa-calendar-check'></i> $title</h3>

            <p><i class='fa-solid fa-user-doctor'></i> Doctor: $docname</p>
            <p><i class='fa-solid fa-hashtag'></i> Appointment No: $num</p>

            <p><i class='fa-solid fa-calendar'></i> Date: $date</p>
            <p><i class='fa-solid fa-clock'></i> Time: $time</p>

            <p><i class='fa-solid fa-calendar-day'></i> Booked On: $appodate</p>

            <br>

            <a href='?action=drop&id=$appoid&title=$title&doc=$docname'>
                <button class='book-btn' style='background:#e74c3c'>
                    <i class='fa-solid fa-xmark'></i> Cancel
                </button>
            </a>

        </div>
        ";
    }
}
?>

</div>

</div>
</div>

<!-- POPUP -->
<?php
if($_GET){

    $id=$_GET["id"];
    $action=$_GET["action"];

    if($action=='drop'){
        $title=$_GET["title"];
        $doc=$_GET["doc"];

        echo "
        <div class='popup'>
            <div class='popup-content'>

                <h2><i class='fa-solid fa-triangle-exclamation'></i> Cancel Booking</h2>

                <p><b>Session:</b> $title</p>
                <p><b>Doctor:</b> $doc</p>

                <a href='delete-appointment.php?id=$id'>
                    <button class='book-btn' style='background:#e74c3c'>
                        Confirm Cancel
                    </button>
                </a>

                <a href='appointment.php'>
                    <button class='book-btn'>Back</button>
                </a>

            </div>
        </div>
        ";
    }
}
?>

</body>
</html>
