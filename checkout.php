<?php
require 'auth.php';
require 'db.php';

$customerID = $_SESSION['CustomerID'] ?? null;
if (!$customerID) {
    header("Location: index.php");
    exit();
}

$customerStmt = $conn->prepare("SELECT FirstName, LastName, Email FROM customer WHERE CustomerID = ?");
$customerStmt->bind_param("s", $customerID);
$customerStmt->execute();
$customerResult = $customerStmt->get_result();
$customer = $customerResult->fetch_assoc();
$customerStmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomTypeID = $_POST['roomTypeID'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $extras = $_POST['extras'] ?? [];
    $totalAmount = $_POST['totalAmount'];

    $reservationID = uniqid('RES');
    $bookingDate = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO `transaction` 
        (ReservationID, CustomerID, CheckInDate, CheckOutDate, TotalAmount, DownPayment, PaymentStatus, ReservationStatus, BookingDate, Discount, PromoID) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $downPayment = 0;
    $paymentStatus = "Pending";
    $reservationStatus = "Booked";
    $discount = "";
    $promoID = "";
    $stmt->bind_param("ssssddsssss", $reservationID, $customerID, $checkin, $checkout, $totalAmount, $downPayment, $paymentStatus, $reservationStatus, $bookingDate, $discount, $promoID);
    $stmt->execute();
    $stmt->close();

    $roomQuery = $conn->prepare("SELECT RoomID, BasePrice FROM room WHERE RoomTypeID = ? AND Status = 'Available' LIMIT 1");
    $roomQuery->bind_param("s", $roomTypeID);
    $roomQuery->execute();
    $roomResult = $roomQuery->get_result();
    if ($roomResult->num_rows > 0) {
        $room = $roomResult->fetch_assoc();
        $reservationRoomID = uniqid('RR');
        $priceAtBooking = $room['BasePrice'];

        $stmt2 = $conn->prepare("INSERT INTO reservation_room 
            (ReservationRoomID, ReservationID, RoomID, PriceAtBooking, CheckInTime, CheckOutTime) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("sssdds", $reservationRoomID, $reservationID, $room['RoomID'], $priceAtBooking, $checkin, $checkout);
        $stmt2->execute();
        $stmt2->close();

        $updateRoom = $conn->prepare("UPDATE room SET Status='Booked' WHERE RoomID=?");
        $updateRoom->bind_param("s", $room['RoomID']);
        $updateRoom->execute();
        $updateRoom->close();
    }

    echo "<script>alert('Booking Successful!'); window.location.href='my-bookings.php';</script>";
    exit();
}


$rooms = $conn->query("
    SELECT r.RoomTypeID, rt.TypeName, rt.Description, rt.BasePrice, rt.MaxCapacity 
    FROM room r 
    JOIN room_type rt ON r.RoomTypeID = rt.RoomTypeID 
    WHERE r.Status='Available' 
    GROUP BY r.RoomTypeID
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Governor Forbes Inn | Checkout</title>
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500&display=swap');

        :root {
            --gold: #d4a83f;
            --dark-gold: #b78f2c;
            --bg: #f6f4f1;
            --white: #ffffff;
            --border: #e2e2e2;
            --text: #2b2b2b;
            --muted: #666;
        }

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
            background: var(--white);
            padding: 50px 45px;
            border-radius: 16px;
            width: 440px;
            text-align: center;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
            animation: pop 0.25s ease;
        }

        @keyframes pop {
            from {
                transform: scale(0.92);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .logout-actions {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin-top: 25px;
        }

        .logout-actions button {
            padding: 13px 32px;
            font-size: 14px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            transition: 0.25s ease;
            min-width: 110px;
        }

        #confirmLogout {
            background: var(--gold);
            color: white;
        }

        #confirmLogout:hover {
            background: var(--dark-gold);
            transform: translateY(-1px);
        }

        #cancelLogout {
            background: #f1f1f1;
        }

        #cancelLogout:hover {
            background: #e2e2e2;
            transform: translateY(-1px);
        }


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


        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }


        .checkout-container {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 60px;
        }

        .checkout-left {
            flex: 2;
            min-width: 300px;
            background: var(--white);
            padding: 25px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .checkout-left h2,
        .checkout-left h3 {
            font-family: 'Playfair Display', serif;
        }

        .checkout-left label {
            display: block;
            margin-top: 14px;
            margin-bottom: 6px;
            font-weight: 500;
        }

        .checkout-left input[type="text"],
        .checkout-left input[type="email"],
        .checkout-left input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .checkout-left .rooms-selection {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-top: 12px;
        }


        .room-card-horizontal {
            display: flex;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            transition: 0.3s ease;
            cursor: pointer;
        }

        .room-card-horizontal:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .room-card-horizontal.selected {
            border: 2px solid var(--gold);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .room-card-horizontal .room-img {
            flex: 1 1 40%;
            max-height: 180px;
            overflow: hidden;
        }

        .room-card-horizontal .room-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .room-card-horizontal .room-info {
            flex: 1 1 60%;
            padding: 20px;
        }

        .room-card-horizontal .room-info h3 {
            margin-bottom: 8px;
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: #333;
        }

        .room-card-horizontal .room-info p {
            margin-bottom: 6px;
            color: var(--muted);
            font-size: 0.95rem;
        }


        .checkout-left .amenities {
            margin-top: 20px;
        }

        .checkout-left .amenities label {
            display: block;
            margin-top: 6px;
            cursor: pointer;
        }


        .receipt {
            flex: 1;
            min-width: 250px;
            background: var(--white);
            border-radius: 8px;
            border: 1px solid var(--border);
            padding: 25px;
            position: sticky;
            top: 100px;
            height: max-content;
        }

        .receipt h3 {
            margin-bottom: 16px;
            font-family: 'Playfair Display', serif;
        }

        .receipt p {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .receipt .vat-note {
            font-size: 0.85rem;
            color: var(--muted);
            margin-top: 12px;
        }


        button,
        .room-button {
            background: var(--gold);
            color: var(--white);
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 12px;
            transition: 0.3s ease;
        }

        button:hover,
        .room-button:hover {
            background: var(--dark-gold);
        }

        footer {
            text-align: center;
            padding: 30px;
            background: var(--white);
            border-top: 1px solid var(--border);
            color: var(--muted);
        }

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 15px;
            }

            .checkout-container {
                flex-direction: column;
                gap: 25px;
            }

            .hero-text {
                bottom: 30px;
                left: 20px;
            }

            .hero-text h1 {
                font-size: 30px;
            }

            .room-card-horizontal {
                flex-direction: column;
            }

            .room-card-horizontal .room-img {
                width: 100%;
                height: 200px;
            }
        }

        .checkout-left .amenities {
            margin-top: 20px;
        }

        .checkout-left .amenities label {
            display: block;
            margin-top: 6px;
            cursor: pointer;
        }

        .receipt {
            flex: 1;
            min-width: 250px;
            background: #fff;
            border-radius: 6px;
            border: 1px solid #e2e2e2;
            padding: 20px;
            height: max-content;
        }

        .receipt h3 {
            margin-bottom: 12px;
        }

        .receipt p {
            margin-bottom: 6px;
            font-size: 0.95rem;
        }

        .receipt .vat-note {
            font-size: 0.8rem;
            color: #777;
            margin-top: 12px;
        }


        .room-button,
        .checkout-left button {
            background: #d4a83f;
            color: #fff;
            border: none;
            padding: 12px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 12px;
        }

        .room-button:hover,
        .checkout-left button:hover {
            background: #b78f2c;
        }

        footer {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        #paymentMode {
            width: 100%;
            padding: 12px 14px;
            border-radius: 6px;
            border: 1px solid #e2e2e2;
            margin-top: 6px;
            font-size: 16px;
            background-color: #fff;
            cursor: pointer;
            transition: border 0.3s ease;
        }

        #paymentMode:focus {
            outline: none;
            border-color: #d4a83f;
        }

        #gcashUpload label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
        }

        #gcashUpload input[type="file"] {
            display: block;
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e2e2e2;
            background-color: #fefefe;
            cursor: pointer;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        #gcashUpload input[type="file"]:hover {
            border-color: #b78f2c;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        #gcashUpload small {
            display: block;
            margin-top: 4px;
            font-size: 0.85rem;
            color: #555;
        }

        #paymentMode+#gcashUpload {
            margin-top: 15px;
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
            <li><a href="checkout.php" class="nav-link active">Checkout</a></li>
            <li><a href="my-bookings.php" class="nav-link">My Bookings</a></li>
            <li><a href="#" class="nav-link" id="logoutBtn">Logout</a></li>
        </ul>
    </nav>


    <header class="hero">
        <img src="https://pix10.agoda.net/property/77022358/0/2327109d10c0f86ff4e52108e3b4a171.jpeg?ce=2&s=414x232"
            alt="Governor Forbes Inn">
        <div class="hero-overlay"></div>
        <div class="hero-text">
            <h1>Check Out & Booking</h1>
            <p>Finalize your stay at Governor Forbes Inn.</p>
        </div>
    </header>


    <section class="container main checkout-container">
        <div class="checkout-left">
            <h2>Guest Information</h2>
            <p><strong>Name:</strong> <?= htmlspecialchars($customer['FirstName'] . " " . $customer['LastName']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($customer['Email']); ?></p>

            <form method="POST" id="bookingForm" enctype="multipart/form-data">
                <label>Check-in</label>
                <input type="date" name="checkin" id="checkinDate" required>
                <label>Check-out</label>
                <input type="date" name="checkout" id="checkoutDate" required>

                <h3>Select Room</h3>
                <div class="rooms-selection">
                    <?php while ($room = $rooms->fetch_assoc()): ?>
                        <div class="room-card-horizontal" data-price="<?= $room['BasePrice']; ?>"
                            data-id="<?= $room['RoomTypeID']; ?>" onclick="selectRoom(this)">
                            <div class="room-img">
                                <img src="images/rooms/<?= $room['RoomTypeID']; ?>.jpg"
                                    alt="<?= htmlspecialchars($room['TypeName']); ?>">
                            </div>
                            <div class="room-info">
                                <h3><?= htmlspecialchars($room['TypeName']); ?></h3>
                                <p>₱<?= number_format($room['BasePrice']); ?> / night</p>
                                <p>Max <?= $room['MaxCapacity']; ?> Guests</p>
                                <p><?= htmlspecialchars($room['Description']); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <h3>Extras (₱500/night each)</h3>
                <label><input type="checkbox" name="extras[]" value="500"> Extra Pillow</label>
                <label><input type="checkbox" name="extras[]" value="500"> Extra Towel</label>
                <label><input type="checkbox" name="extras[]" value="500"> Extra Bed</label>

                <h3>Payment Method</h3>
                <select name="paymentMode" id="paymentMode" required onchange="toggleProofUpload()">
                    <option value="">-- Select Payment Mode --</option>
                    <option value="gcash_downpayment">GCash - Downpayment ₱1000</option>
                    <option value="gcash_full">GCash - Full Payment</option>
                    <option value="otc_downpayment">Over the Counter - Downpayment ₱1000</option>
                    <option value="otc_full">Over the Counter - Full Payment</option>
                </select>


                <div id="gcashUpload" style="display:none; margin-top: 15px;">
                    <label for="proofPayment" style="font-weight:600;">Upload Proof of Payment</label>
                    <input type="file" name="proofPayment" id="proofPayment" accept="image/*"
                        style="display:block; margin-top:8px; padding:10px; border:1px solid #ccc; border-radius:6px; width:100%;">
                    <small style="color:#777;">Accepted formats: JPG, PNG. Max size: 5MB</small>
                </div>


                <input type="hidden" name="roomTypeID" id="roomTypeID">
                <input type="hidden" name="totalAmount" id="totalAmount">

                <button type="submit">Confirm Booking</button>
            </form>
        </div>

        <div class="receipt">
            <h3>Your Receipt</h3>
            <div id="receiptContent">Select a room and dates</div>
        </div>
    </section>

    <!-- LOGOUT MODAL -->
    <div class="logout-modal" id="logoutModal">
        <div class="logout-box">
            <h3 class="center">Confirm Logout</h3>
            <p class="center">Are you sure you want to logout?</p>
            <div class="logout-actions">
                <button id="cancelLogout">Cancel</button>
                <button id="confirmLogout">Logout</button>
            </div>
        </div>
    </div>

    <footer>© Governor Forbes Inn</footer>

    <!-- SCRIPTS -->
    <script>
        // LOGOUT MODAL
        const logoutBtn = document.getElementById('logoutBtn');
        const logoutModal = document.getElementById('logoutModal');
        const cancelLogout = document.getElementById('cancelLogout');
        const confirmLogout = document.getElementById('confirmLogout');
        logoutBtn.onclick = () => logoutModal.classList.add('show');
        cancelLogout.onclick = () => logoutModal.classList.remove('show');
        confirmLogout.onclick = () => window.location.href = "logout.php";

        // ROOM SELECTION & RECEIPT
        let selectedRoom = null;
        const roomCards = document.querySelectorAll('.room-card-horizontal');
        const checkinDate = document.getElementById('checkinDate');
        const checkoutDate = document.getElementById('checkoutDate');
        const extras = document.querySelectorAll('input[name="extras[]"]');
        const receiptContent = document.getElementById('receiptContent');
        const roomTypeInput = document.getElementById('roomTypeID');
        const totalAmountInput = document.getElementById('totalAmount');

        function selectRoom(card) {
            roomCards.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            selectedRoom = card;
            roomTypeInput.value = card.dataset.id;
            updateReceipt();
        }

        function updateReceipt() {
            if (!selectedRoom) {
                receiptContent.innerHTML = 'Please select a room';
                totalAmountInput.value = '';
                return;
            }

            const checkin = new Date(checkinDate.value);
            const checkout = new Date(checkoutDate.value);

            if (isNaN(checkin) || isNaN(checkout) || checkout <= checkin) {
                receiptContent.innerHTML = 'Select valid check-in and check-out dates';
                totalAmountInput.value = '';
                return;
            }

            const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
            let price = parseInt(selectedRoom.dataset.price) * nights;
            let extrasTotal = 0;
            extras.forEach(e => { if (e.checked) extrasTotal += parseInt(e.value) * nights; });
            const total = price + extrasTotal;
            totalAmountInput.value = total;

            receiptContent.innerHTML = `
        Room: ${selectedRoom.querySelector('h3').innerText} <br>
        Nights: ${nights} <br>
        Room Price: ₱${price.toLocaleString()} <br>
        Extras: ₱${extrasTotal.toLocaleString()} <br>
        Total: ₱${total.toLocaleString()}
    `;
        }
        function toggleProofUpload() {
            const payment = document.getElementById('paymentMode').value;
            const uploadDiv = document.getElementById('gcashUpload');

            if (payment.startsWith('gcash')) {
                uploadDiv.style.display = 'block';
            } else {
                uploadDiv.style.display = 'none';
            }
        }


        // DEFAULT DATES
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);
        checkinDate.valueAsDate = today;
        checkoutDate.valueAsDate = tomorrow;

        checkinDate.addEventListener('change', () => {
            const ci = new Date(checkinDate.value);
            const co = new Date(checkoutDate.value);
            if (co <= ci) checkoutDate.valueAsDate = new Date(ci.getTime() + 24 * 60 * 60 * 1000);
            updateReceipt();
        });
        checkoutDate.addEventListener('change', updateReceipt);
        extras.forEach(e => e.addEventListener('change', updateReceipt));

        // PAYMENT MODE - SHOW GCASH UPLOAD
        const paymentRadios = document.querySelectorAll('input[name="paymentMode"]');
        const gcashUpload = document.getElementById('gcashUpload');
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value.includes('GCash')) {
                    gcashUpload.style.display = 'block';
                } else {
                    gcashUpload.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>