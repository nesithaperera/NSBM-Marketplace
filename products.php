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
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products - NSBM Marketplace</title>

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

<?php include "includes/header.php"; ?>


<main class="products-page">

    <!-- Page Header -->

    <section class="products-header">

        <h1>Browse Products</h1>

        <p>
            Find products available in the NSBM Marketplace.
        </p>

    </section>


    <!-- Search and Filters -->

    <section class="filter-section">

        <form method="GET" class="filter-form">

            <div class="filter-group">

                <label for="search">
                    Search
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search products..."
                >

            </div>


            <div class="filter-group">

                <label for="category">
                    Category
                </label>

                <select name="category" id="category">

                    <option value="">
                        All Categories
                    </option>

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

            </div>


            <div class="filter-group">

                <label for="min_price">
                    Min Price
                </label>

                <input
                    type="number"
                    id="min_price"
                    name="min_price"
                    step="0.01"
                    min="0"
                    value="<?php echo htmlspecialchars($min_price); ?>"
                    placeholder="0.00"
                >

            </div>


            <div class="filter-group">

                <label for="max_price">
                    Max Price
                </label>

                <input
                    type="number"
                    id="max_price"
                    name="max_price"
                    step="0.01"
                    min="0"
                    value="<?php echo htmlspecialchars($max_price); ?>"
                    placeholder="0.00"
                >

            </div>


            <div class="filter-group">

                <label for="sort">
                    Sort By
                </label>

                <select name="sort" id="sort">

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

            </div>


            <div class="filter-buttons">

                <button type="submit">
                    Search
                </button>

                <a href="products.php">
                    Clear
                </a>

            </div>

        </form>

    </section>


    <!-- Products -->

    <section class="products-section">

        <div class="products-section-header">

            <h2>Available Products</h2>

            <?php if ($result->num_rows > 0): ?>

                <span>
                    <?php echo $result->num_rows; ?> product(s) found
                </span>

            <?php endif; ?>

        </div>


        <?php if ($result->num_rows > 0): ?>

            <div class="products-grid">

                <?php while ($product = $result->fetch_assoc()): ?>

                    <div class="product-card">


                        <!-- Product Image -->

                        <div class="product-image">

                            <?php if (!empty($product["image"])): ?>

                                <img
                                    src="assets/images/products/<?php echo htmlspecialchars($product["image"]); ?>"
                                    alt="<?php echo htmlspecialchars($product["title"]); ?>"
                                >

                            <?php else: ?>

                                <div class="no-image">
                                    No Image
                                </div>

                            <?php endif; ?>

                        </div>


                        <!-- Product Information -->

                        <div class="product-info">

                            <h3>
                                <?php echo htmlspecialchars($product["title"]); ?>
                            </h3>


                            <p class="product-category">
                                <?php echo htmlspecialchars($product["category_name"]); ?>
                            </p>


                            <p class="product-price">
                                Rs. <?php echo number_format($product["price"], 2); ?>
                            </p>


                            <p>
                                <strong>Location:</strong>
                                <?php echo htmlspecialchars($product["location"] ?? "N/A"); ?>
                            </p>


                            <p>
                                <strong>Available:</strong>
                                <?php echo (int)$product["quantity"]; ?>
                            </p>


                            <p>
                                <strong>Seller:</strong>
                                <?php echo htmlspecialchars($product["seller_name"]); ?>
                            </p>


                            <a
                                href="product-details.php?id=<?php echo (int)$product["id"]; ?>"
                                class="view-product-button"
                            >
                                View Details
                            </a>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>


        <?php else: ?>

            <div class="no-products">

                <h3>No Products Found</h3>

                <p>
                    Try changing your search or filter options.
                </p>

                <a href="products.php">
                    Clear Filters
                </a>

            </div>

        <?php endif; ?>

    </section>

</main>


<?php include "includes/footer.php"; ?>

</body>

</html>