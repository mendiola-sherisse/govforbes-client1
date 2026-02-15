<?php
require 'auth.php';  // starts session & checks login
require 'db.php';    // $conn for DB

$customerName = $_SESSION['FirstName'] ?? "Guest";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Governor Forbes Inn | Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    background: rgba(0,0,0,0.35);
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
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    animation: pop 0.25s ease;
}

@keyframes pop {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
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

/* ROOM CARD (Horizontal Layout) */
.room-card-horizontal {
    display: flex;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 25px;
    transition: 0.3s ease;
}

.room-card-horizontal:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.room-img {
    width: 250px;
}

.room-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
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

.room-slider {
    position: relative;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    background: var(--white);
    margin: 20px 0;
}
.slides {
    display: flex;
    transition: transform 0.4s ease;
}
.slide {
    min-width: 100%;
}
.slide-placeholder {
    height: 280px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
}

.slide-placeholder img {
    width: 100%;
    height: 100%;
    object-fit: cover; 
}

.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: var(--gold);
    color: #fff;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
}
.slider-btn:hover {
    background: var(--dark-gold);
}
.slider-btn.prev {
    left: 12px;
}
.slider-btn.next {
    right: 12px;
}
.slider-dots {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: 12px;
    display: flex;
    gap: 8px;
}
.slider-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ccc;
}
.slider-dot.active {
    background: var(--gold);
}
@media (max-width: 768px) {
    .slide-placeholder {
        height: 200px;
        font-size: 22px;
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
            <li><a href="homepage.php" class="nav-link active">Home</a></li>
            <li><a href="rooms.php" class="nav-link">Rooms</a></li>
            <li><a href="checkout.php" class="nav-link">Checkout</a></li>
            <li><a href="my-bookings.php" class="nav-link">My Bookings</a></li>
            <li><a href="#" class="nav-link" id="logoutBtn">Logout</a></li>

        </ul>
    </nav>


    <header class="hero">
        <img src="https://pix10.agoda.net/property/77022358/0/2327109d10c0f86ff4e52108e3b4a171.jpeg?ce=2&s=414x232"
            alt="Governor Forbes Inn">
        <div class="hero-overlay"></div>
        <div class="hero-text">
            <h1>Governor Forbes Inn</h1>
            <p>Changing Lives in Manila</p>
        </div>
    </header>

    <section class="container center">
        <h1>Welcome, <?= htmlspecialchars($customerName); ?>!</h1>
    </section>

    

    <section class="container inn-description">
        <p>
            Nestled in the heart of Sampaloc, Governor Forbes Inn is an ideal spot from which to discover Manila.
            From here, guests can enjoy easy access to all that the lively city has to offer. With its convenient
            location near major bus stations, review centers, universities and hospitals.
        </p>
    </section>

    <section class="container">
        <h2>Our Rooms</h2>
        <section class="container">
        <div class="room-slider" id="roomSlider">
            <div class="slides">
                <div class="slide"><div class="slide-placeholder"><img src="images/FAMILY2.jpg" alt="Description of image"></div></div>
                <div class="slide"><div class="slide-placeholder"><img src="images/SUPERIOR2.jpg" alt="Description of image"></div></div>
                <div class="slide"><div class="slide-placeholder"><img src="images/TWIN1.jpg" alt="Description of image"></div></div>
                <div class="slide"><div class="slide-placeholder"><img src="images/QUADRUPLE2.jpg" alt="Description of image"></div></div>
            </div>
            <button class="slider-btn prev" aria-label="Previous">&#10094;</button>
            <button class="slider-btn next" aria-label="Next">&#10095;</button>
            <div class="slider-dots">
                <span class="slider-dot active"></span>
                <span class="slider-dot"></span>
                <span class="slider-dot"></span>
            </div>
        </div>
    </section>
   <?php
$stmt = $conn->query("SELECT * FROM room_type");
while ($room = $stmt->fetch_assoc()) {

    $images = [
        'FAMILY'    => 'FAMILY.jpg',
        'SUPERIOR'  => 'SUPERIOR.jpg',
        'TWIN'      => 'TWIN.jpg',
        'QUADRUPLE' => 'QUADRUPLE.jpg'
    ];

 
    $key = $room['RoomTypeID']; 
    $imageFile = $images[$key] ?? 'default.jpg';
    $imagePath = 'images/rooms/' . $imageFile;

    echo '
    <div class="room-card-horizontal">

        <div class="room-img">
            <img src="' . htmlspecialchars($imagePath) . '" 
                 alt="' . htmlspecialchars($room['TypeName']) . '" 
                 style="width:250px; height:100%; object-fit:cover;">
        </div>

        <div class="room-info">
            <h3>' . htmlspecialchars($room['TypeName']) . '</h3>
            <p>₱' . number_format($room['BasePrice']) . ' / night</p>
            <p>Max Guests: ' . htmlspecialchars($room['MaxCapacity']) . '</p>
            <button onclick="location.href=\'rooms.php\'">View Rooms</button>
        </div>

    </div>';
}
?>
    </section>

    <footer>© Governor Forbes Inn</footer>
    <script>
        (function() {
            var slider = document.querySelector('.room-slider');
            if (!slider) return;
            var slidesEl = slider.querySelector('.slides');
            var slides = Array.prototype.slice.call(slidesEl.children);
            var prev = slider.querySelector('.slider-btn.prev');
            var next = slider.querySelector('.slider-btn.next');
            var dots = Array.prototype.slice.call(slider.querySelectorAll('.slider-dot'));
            var index = 0;
            var timer;

            function go(i) {
                index = (i + slides.length) % slides.length;
                slidesEl.style.transform = 'translateX(-' + (index * 100) + '%)';
                dots.forEach(function(d, di) { d.classList.toggle('active', di === index); });
            }

            function start() {
                stop();
                timer = setInterval(function() { go(index + 1); }, 5000);
            }

            function stop() {
                if (timer) clearInterval(timer);
            }

            prev.addEventListener('click', function() { go(index - 1); start(); });
            next.addEventListener('click', function() { go(index + 1); start(); });
            slider.addEventListener('mouseenter', stop);
            slider.addEventListener('mouseleave', start);

            go(0);
            start();
        })();
    </script>

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