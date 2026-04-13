<?php
session_start();
include "config.php";

// Remove item
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
}

// Clear cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
}

// Buy all
if (isset($_GET['buy'])) {
    unset($_SESSION['cart']);
    echo "<script>alert('✅ Order placed successfully!');</script>";
}

// Buy single product
if (isset($_GET['buy_single'])) {
    echo "<script>alert('✅ Order placed successfully!');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Your Cart</title>

<style>
body { font-family: Arial; text-align: center; background: #f5f5f5; }

.container {
    width: 70%;
    margin: auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
}

table {
    width: 100%;
}

th { background: black; color: white; }

button {
    padding: 5px 10px;
    border: none;
    cursor: pointer;
}

.remove { background: red; color: white; }
.buy { background: green; color: white; }
.clear { background: black; color: white; }
</style>

</head>
<body>

<div class="container">
<h1>🛍️ Your Cart</h1>

<?php
if (!empty($_SESSION['cart'])) {

    $total = 0;

    echo "<table>";
    echo "<tr><th>Item</th><th>Price</th><th>Action</th></tr>";

    foreach ($_SESSION['cart'] as $key => $id) {

        $res = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
        $row = mysqli_fetch_assoc($res);

        echo "<tr>";
        echo "<td>".$row['name']."</td>";
        echo "<td>₹".$row['price']."</td>";
        echo "<td>
                <a href='cart.php?remove=$key'>
                <button class='remove'>Remove</button></a>
              </td>";
        echo "</tr>";

        $total += $row['price'];
    }

    echo "</table>";

    echo "<h2>Total: ₹$total</h2>";

    echo "<a href='cart.php?buy=true'><button class='buy'>Buy All</button></a><br><br>";
    echo "<a href='cart.php?clear=true'><button class='clear'>Clear Cart</button></a>";

} else {
    echo "<p>Cart is empty 😢</p>";
}
?>

<br><br>
<a href="products.php">← Continue Shopping</a>

</div>

</body>
</html>