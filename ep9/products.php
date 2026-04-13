<?php
session_start();
include "config.php";

// Add to cart
if (isset($_GET['add'])) {
    $id = $_GET['add'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = $id;

    header("Location: cart.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Electrical | Products</title>

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background: linear-gradient(120deg, #fdfbfb, #ebedee);
}

/* Header */
.header {
    background: linear-gradient(to right, #000, #434343);
    color: white;
    padding: 25px;
    text-align: center;
    font-size: 26px;
    letter-spacing: 2px;
    text-transform: uppercase;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}

/* Grid */
.container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    padding: 40px;
}

/* Card */
.card {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    padding: 20px;
    text-align: center;
    border-radius: 20px;
    transition: 0.4s;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}

/* Glow border */
.card::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 20px;
    padding: 2px;
    background: linear-gradient(45deg, #ff00cc, #3333ff, #00ffcc);
    -webkit-mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
    -webkit-mask-composite: destination-out;
    opacity: 0;
    transition: 0.4s;
}

.card:hover::before {
    opacity: 1;
}

/* Hover effect */
.card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

/* Image */
.card img {
    width: 170px;
    height: 210px;
    object-fit: cover;
    border-radius: 12px;
    transition: 0.4s;
}

.card:hover img {
    transform: scale(1.1) rotate(1deg);
}

/* Title */
.card h3 {
    margin: 12px 0 5px;
    font-size: 18px;
    color: #333;
}

/* Info */
.info {
    font-size: 13px;
    color: #666;
}

/* Price */
.price {
    color: #ff0055;
    font-weight: bold;
    font-size: 20px;
    margin: 10px 0;
}

/* Buttons */
.btn {
    background: linear-gradient(to right, #000, #434343);
    color: white;
    padding: 10px 15px;
    border: none;
    margin: 6px;
    cursor: pointer;
    border-radius: 25px;
    transition: 0.3s;
    font-weight: bold;
    position: relative;
    overflow: hidden;
}

/* Button hover */
.btn:hover {
    background: linear-gradient(to right, #ff0055, #ff7a00);
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* Ripple effect */
.btn::after {
    content: "";
    position: absolute;
    width: 0;
    height: 0;
    background: rgba(255,255,255,0.4);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    top: 50%;
    left: 50%;
    transition: 0.5s;
}

.btn:active::after {
    width: 200px;
    height: 200px;
}

/* Badge (NEW) */
.card::after {
    content: "NEW";
    position: absolute;
    top: 15px;
    left: 15px;
    background: red;
    color: white;
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 5px;
}

/* Cart link */
.cart-link {
    text-align: center;
    display: block;
    margin: 30px;
    font-size: 20px;
    text-decoration: none;
    color: #333;
    font-weight: bold;
    transition: 0.3s;
}

.cart-link:hover {
    color: #ff0055;
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.6s ease;
}
</style>

</head>
<body>

<div class="header">
Electrical products
</div>

<div class="container">

<?php
$result = mysqli_query($conn, "SELECT * FROM products");

while ($row = mysqli_fetch_assoc($result)) {
?>
    <div class="card">
        <img src="<?php echo $row['image']; ?>">
        <h3><?php echo $row['name']; ?></h3>
        <p><?php echo $row['material']; ?></p>
        <p class="price">₹<?php echo $row['price']; ?></p>

        <a href="products.php?add=<?php echo $row['id']; ?>">
            <button class="btn">Add to Cart</button>
        </a>

        <a href="cart.php?buy_single=<?php echo $row['id']; ?>">
            <button class="btn">Buy Now</button>
        </a>
    </div>
<?php
}
?>

</div>

<a href="cart.php" class="cart-link">🛒 View Cart</a>

</body>
</html>