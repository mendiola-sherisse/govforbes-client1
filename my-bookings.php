<?php
require 'auth.php';
require 'db.php';

$customerID = $_SESSION['CustomerID'];

/* CANCEL BOOKING */
if (isset($_GET['cancel']) && !empty($_GET['cancel'])) {
    $reservationID = $_GET['cancel'];

    $stmtCancel = $conn->prepare("UPDATE `transaction` SET ReservationStatus='Cancelled' WHERE ReservationID=? AND CustomerID=?");
    $stmtCancel->bind_param("ss", $reservationID, $customerID);
    $stmtCancel->execute();
    $stmtCancel->close();

    echo "<script>alert('Booking cancelled successfully.'); window.location.href='my-bookings.php';</script>";
    exit();
}

/* FETCH BOOKINGS */
$stmt = $conn->prepare("
    SELECT 
        t.ReservationID, 
        t.CheckInDate, 
        t.CheckOutDate, 
        t.TotalAmount, 
        t.ReservationStatus, 
        rt.TypeName,
        rt.RoomTypeID
    FROM `transaction` t
    JOIN reservation_room rr ON t.ReservationID = rr.ReservationID
    JOIN room r ON rr.RoomID = r.RoomID
    JOIN room_type rt ON r.RoomTypeID = rt.RoomTypeID
    WHERE t.CustomerID = ?
    ORDER BY t.BookingDate DESC
");
$stmt->bind_param("s", $customerID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Governor Forbes Inn | My Bookings</title>
    <link rel="stylesheet" href="checkout.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500&display=swap');

        /* VARIABLES */
        :root {
            --gold: #d4a83f;
            --dark-gold: #b78f2c;
            --bg: #f6f4f1;
            --white: #ffffff;
            --border: #e2e2e2;
            --text: #2b2b2b;
            --muted: #666;
        }

        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        /* NAVBAR */
        nav {
            background: var(--white);
            padding: 15px 60px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            width: 45px;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 20px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            transition: 0.3s;
        }

        nav a:hover,
        nav a.active {
            color: var(--gold);
        }

        .logout-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s ease;
            z-index: 999;
        }

        .logout-modal.show {
            opacity: 1;
            visibility: visible;
        }

        .logout-box {
            background: white;
            padding: 40px;
            border-radius: 14px;
            width: 600px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            animation: pop 0.25s ease;
        }

        @keyframes pop {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .logout-actions {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .logout-actions button {
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #confirmLogout {
            background: var(--gold);
            color: white;
        }

        #confirmLogout:hover {
            background: var(--dark-gold);
        }

        #cancelLogout {
            background: #eee;
        }

        #cancelLogout:hover {
            background: #ddd;
        }

        /* HERO */
        .hero {
            position: relative;
            height: 50vh;
            overflow: hidden;
        }

        .hero img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(80, 50, 15, 0.5);
        }

        .hero-text {
            position: absolute;
            bottom: 60px;
            left: 60px;
            color: white;
        }

        .hero-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
        }

        .hero-text p {
            font-size: 18px;
        }

        /* CONTAINER */
        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .center {
            text-align: center;
            margin-bottom: 20px;
        }

        /* DESCRIPTION BOX */
        .inn-description {
            background: var(--white);
            padding: 25px;
            border-radius: 6px;
            border: 1px solid var(--border);
            line-height: 1.6;
            color: var(--muted);
        }

        .room-card-horizontal {
            display: flex;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 25px;
            transition: 0.3s ease;
            min-height: 200px;
        }

        .room-card-horizontal:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

         .room-img {
            flex: 0 0 250px;
            height: 200px;
            overflow: hidden;
        }

        .room-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }


        .room-info {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .room-info h3 {
            margin-bottom: 8px;
        }

        .room-info p {
            color: var(--muted);
            margin-bottom: 6px;
        }

        .room-info button {
            margin-top: 10px;
            padding: 10px 18px;
            background: var(--gold);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            width: auto;
            align-self: flex-start;
        }

        .room-info button:hover {
            background: var(--dark-gold);
        }

        /* CANCEL BUTTON */
        .cancel-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 8px;
            transition: 0.3s;
        }

        .cancel-btn:hover {
            background: #c0392b;
        }

        /* FOOTER */
        footer {
            text-align: center;
            padding: 25px;
            margin-top: 40px;
            background: var(--white);
            border-top: 1px solid var(--border);
            color: var(--muted);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 15px;
            }

            .room-card-horizontal {
                flex-direction: column;
            }

            .room-img {
                width: 100%;
                height: 200px;
            }

            .hero-text {
                left: 20px;
                bottom: 30px;
            }

            .hero-text h1 {
                font-size: 30px;
            }
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {

            nav {
                flex-direction: column;
                gap: 15px;
            }

            .room-card-horizontal {
                flex-direction: column;
            }

            .room-img {
                width: 100%;
                height: 200px;
            }

            .hero-text {
                left: 20px;
                bottom: 30px;
            }

            .hero-text h1 {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>

    <nav>
        <div class="nav-left">
            <div class="logo"><img src="logo.png" alt="Logo"></div>
            <span class="brand">Governor Forbes Inn</span>
        </div>
        <ul id="nav-links">
            <li><a href="homepage.php" class="nav-link">Home</a></li>
            <li><a href="rooms.php" class="nav-link">Rooms</a></li>
            <li><a href="checkout.php" class="nav-link">Checkout</a></li>
            <li><a href="my-bookings.php" class="nav-link active">My Bookings</a></li>
            <li><a href="logout.php" class="nav-link">Logout</a></li>
        </ul>
    </nav>

    <header class="hero">
        <img src="https://pix10.agoda.net/property/77022358/0/2327109d10c0f86ff4e52108e3b4a171.jpeg?ce=2&s=414x232"
            alt="My Bookings">
        <div class="hero-overlay"></div>
        <div class="hero-text">
            <h1>My Bookings</h1>
            <p>Review your current and past reservations</p>
        </div>
    </header>

    <section class="container">

        <?php if ($result->num_rows === 0): ?>

            <p>You have no bookings yet. <a href="rooms.php">Book a room now!</a></p>

        <?php else: ?>

            <?php while ($booking = $result->fetch_assoc()): ?>
                <div class="room-card-horizontal">
                    <div class="room-img">

                        <!-- ✅ PERFECT FIX -->
                        <img src="images/rooms/<?= $booking['RoomTypeID']; ?>.jpg">

                    </div>
                    <div class="room-info">
                        <h3><?= $booking['TypeName']; ?></h3>
                        <p>Check-in: <?= date("M d, Y", strtotime($booking['CheckInDate'])); ?></p>
                        <p>Check-out: <?= date("M d, Y", strtotime($booking['CheckOutDate'])); ?></p>
                        <p>Total Amount: ₱<?= number_format($booking['TotalAmount']); ?></p>
                        <p>Status: <?= $booking['ReservationStatus']; ?></p>

                        <?php if ($booking['ReservationStatus'] !== 'Cancelled'): ?>
                            <a href="my-bookings.php?cancel=<?= $booking['ReservationID']; ?>">
                                <button class="cancel-btn">Cancel Booking</button>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>

        <?php endif; ?>

    </section>

    <!-- LOGOUT MODAL -->
    <div class="logout-modal" id="logoutModal">
        <div class="logout-box">
            <h3>Confirm Logout</h3>
            <p>Are you sure?</p>

            <div class="logout-actions">
                <button id="cancelLogout">Cancel</button>
                <button id="confirmLogout">Logout</button>
            </div>
        </div>
    </div>

    <script>
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogout = document.getElementById('cancelLogout');
        const confirmLogout = document.getElementById('confirmLogout');

        logoutBtn.onclick = () => logoutModal.classList.add('show');
        cancelLogout.onclick = () => logoutModal.classList.remove('show');
        confirmLogout.onclick = () => window.location.href = "logout.php";
    </script>

</body>

</html>