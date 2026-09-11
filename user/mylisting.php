\<?php

include "../config/database.php";
include "../includes/auth.php";

$user_id = $_SESSION["user_id"];

// Get products belonging to the logged-in user
$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Listings - NSBM Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <?php include "../includes/header.php"; ?>


    <main class="my-listings-page">

        <div class="my-listings-container">


            <!-- =========================================
                 PAGE HEADER
            ========================================== -->

            <div class="my-listings-header">

                <div>

                    <span class="listings-label">
                        SELLER DASHBOARD
                    </span>

                    <h1>My Listings</h1>

                    <p>
                        Manage the products you have listed
                        on the NSBM Marketplace.
                    </p>

                </div>


                <a
                    href="add-product.php"
                    class="add-listing-btn"
                >
                    + Add New Product
                </a>

            </div>


            <!-- =========================================
                 PRODUCT LISTINGS
            ========================================== -->

            <?php

            if ($result->num_rows > 0) {

            ?>

                <div class="my-listings-grid">

                    <?php

                    while (
                        $product =
                        $result->fetch_assoc()
                    ) {

                    ?>

                        <div class="listing-card">


                            <!-- PRODUCT IMAGE -->

                            <div class="listing-image">

                                <?php

                                if (
                                    !empty($product["image"]) &&
                                    $product["image"] != "null"
                                ) {

                                ?>

                                    <img
                                        src="../assets/images/products/<?php
                                            echo htmlspecialchars(
                                                $product["image"]
                                            );
                                        ?>"
                                        alt="<?php
                                            echo htmlspecialchars(
                                                $product["title"]
                                            );
                                        ?>"
                                    >

                                <?php

                                } else {

                                ?>

                                    <div class="listing-no-image">
                                        No Image
                                    </div>

                                <?php } ?>

                            </div>


                            <!-- PRODUCT INFORMATION -->

                            <div class="listing-content">


                                <div class="listing-top">

                                    <h2>
                                        <?php
                                        echo htmlspecialchars(
                                            $product["title"]
                                        );
                                        ?>
                                    </h2>


                                    <span
                                        class="listing-status status-<?php
                                            echo htmlspecialchars(
                                                strtolower(
                                                    $product["status"]
                                                )
                                            );
                                        ?>"
                                    >
                                        <?php
                                        echo htmlspecialchars(
                                            ucfirst(
                                                $product["status"]
                                            )
                                        );
                                        ?>
                                    </span>

                                </div>


                                <p class="listing-category">

                                    <?php
                                    echo htmlspecialchars(
                                        $product["category_name"]
                                    );
                                    ?>

                                </p>


                                <p class="listing-price">

                                    Rs.
                                    <?php
                                    echo number_format(
                                        $product["price"],
                                        2
                                    );
                                    ?>

                                </p>


                                <div class="listing-details">

                                    <span>
                                        <strong>Quantity:</strong>
                                        <?php
                                        echo $product["quantity"];
                                        ?>
                                    </span>

                                    <span>
                                        <strong>Location:</strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $product["location"]
                                        );
                                        ?>
                                    </span>

                                </div>


                                <!-- ACTIONS -->

                                <div class="listing-actions">

                                    <a
                                        href="edit-product.php?id=<?php
                                            echo $product["id"];
                                        ?>"
                                        class="listing-edit-btn"
                                    >
                                        Edit
                                    </a>


                                    <a
                                        href="delete-product.php?id=<?php
                                            echo $product["id"];
                                        ?>"
                                        class="listing-delete-btn"
                                        onclick="return confirm(
                                            'Are you sure you want to delete this product?'
                                        );"
                                    >
                                        Delete
                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php

                    }

                    ?>

                </div>

            <?php

            } else {

            ?>

                <!-- =====================================
                     EMPTY STATE
                ====================================== -->

                <div class="empty-listings">

                    <div class="empty-listings-icon">
                        📦
                    </div>

                    <h2>No Products Listed Yet</h2>

                    <p>
                        You haven't added any products to the
                        marketplace yet.
                    </p>

                    <a
                        href="add-product.php"
                        class="add-listing-btn"
                    >
                        Add Your First Product
                    </a>

                </div>

            <?php } ?>


            <!-- =========================================
                 MARKETPLACE LINK
            ========================================== -->

            <div class="marketplace-link">

                <a href="../products.php">
                    ← View Marketplace
                </a>

            </div>

        </div>

    </main>


    <?php include "../includes/footer.php"; ?>

</body>

</html>