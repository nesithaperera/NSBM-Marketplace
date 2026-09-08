<?php

include "config/database.php";

// Get search and filter values
$search = "";
$category_id = "";
$min_price = "";
$max_price = "";
$sort = "newest";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

if (isset($_GET["category"])) {
    $category_id = $_GET["category"];
}

if (isset($_GET["min_price"])) {
    $min_price = $_GET["min_price"];
}

if (isset($_GET["max_price"])) {
    $max_price = $_GET["max_price"];
}

if (isset($_GET["sort"])) {
    $sort = $_GET["sort"];
}


// Decide the sorting method
if ($sort == "oldest") {

    $order_by = "p.created_at ASC";

} elseif ($sort == "price_low") {

    $order_by = "p.price ASC";

} elseif ($sort == "price_high") {

    $order_by = "p.price DESC";

} else {

    $order_by = "p.created_at DESC";

}


// Start SQL query
$sql = "SELECT p.*, c.name AS category_name, u.name AS seller_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN users u ON p.user_id = u.id
        WHERE p.status = 'approved'";


// Search filter
if ($search != "") {
    $sql .= " AND (p.title LIKE ? OR p.description LIKE ?)";
}


// Category filter
if ($category_id != "") {
    $sql .= " AND p.category_id = ?";
}


// Minimum price filter
if ($min_price != "") {
    $sql .= " AND p.price >= ?";
}


// Maximum price filter
if ($max_price != "") {
    $sql .= " AND p.price <= ?";
}


$sql .= " ORDER BY " . $order_by;


$stmt = $conn->prepare($sql);


// Bind values
$types = "";
$values = [];

if ($search != "") {

    $search_value = "%" . $search . "%";

    $types .= "ss";
    $values[] = $search_value;
    $values[] = $search_value;
}

if ($category_id != "") {

    $types .= "i";
    $values[] = $category_id;
}

if ($min_price != "") {

    $types .= "d";
    $values[] = $min_price;
}

if ($max_price != "") {

    $types .= "d";
    $values[] = $max_price;
}


// Bind parameters if there are filters
if ($types != "") {

    $stmt->bind_param($types, ...$values);
}


$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

    <title>NSBM Marketplace</title>

</head>

<body>

    <h1>NSBM Marketplace</h1>

    <h2>Find Products</h2>


    <!-- Search and Filter Form -->

    <form method="GET">

        <label>Search:</label>

        <input
            type="text"
            name="search"
            value="<?php echo htmlspecialchars($search); ?>"
            placeholder="Search products..."
        >

        <br><br>


        <label>Category:</label>

        <select name="category">

            <option value="">All Categories</option>

            <?php

            $category_sql = "SELECT id, name
                             FROM categories
                             WHERE status = 'active'
                             ORDER BY name ASC";

            $category_result = $conn->query($category_sql);

            while ($category = $category_result->fetch_assoc()) {

            ?>

                <option
                    value="<?php echo $category["id"]; ?>"
                    <?php
                    if ($category_id == $category["id"]) {
                        echo "selected";
                    }
                    ?>
                >

                    <?php echo htmlspecialchars($category["name"]); ?>

                </option>

            <?php } ?>

        </select>

        <br><br>


        <label>Minimum Price:</label>

        <input
            type="number"
            name="min_price"
            step="0.01"
            min="0"
            value="<?php echo htmlspecialchars($min_price); ?>"
        >

        <br><br>


        <label>Maximum Price:</label>

        <input
            type="number"
            name="max_price"
            step="0.01"
            min="0"
            value="<?php echo htmlspecialchars($max_price); ?>"
        >

        <br><br>


        <label>Sort By:</label>

        <select name="sort">

            <option
                value="newest"
                <?php if ($sort == "newest") echo "selected"; ?>
            >
                Newest
            </option>

            <option
                value="oldest"
                <?php if ($sort == "oldest") echo "selected"; ?>
            >
                Oldest
            </option>

            <option
                value="price_low"
                <?php if ($sort == "price_low") echo "selected"; ?>
            >
                Price: Low to High
            </option>

            <option
                value="price_high"
                <?php if ($sort == "price_high") echo "selected"; ?>
            >
                Price: High to Low
            </option>

        </select>

        <br><br>


        <button type="submit">Search</button>

        <a href="products.php">Clear Filters</a>

    </form>


    <hr>


    <h2>Available Products</h2>


    <?php

    if ($result->num_rows > 0) {

        while ($product = $result->fetch_assoc()) {

    ?>

            <div>

                <h3>
                    <?php echo htmlspecialchars($product["title"]); ?>
                </h3>

                <p>
                    <strong>Category:</strong>
                    <?php echo htmlspecialchars($product["category_name"]); ?>
                </p>

                <p>
                    <strong>Price:</strong>
                    Rs. <?php echo number_format($product["price"], 2); ?>
                </p>

                <p>
                    <strong>Location:</strong>
                    <?php echo htmlspecialchars($product["location"]); ?>
                </p>

                <p>
                    <strong>Available:</strong>
                    <?php echo $product["quantity"]; ?>
                </p>

                <p>
                    <strong>Seller:</strong>
                    <?php echo htmlspecialchars($product["seller_name"]); ?>
                </p>

                <a href="productdetails.php?id=<?php echo $product["id"]; ?>">
                    View Details
                </a>

                <hr>

            </div>

    <?php

        }

    } else {

        echo "<p>No products found.</p>";

    }

    ?>

</body>

</html>