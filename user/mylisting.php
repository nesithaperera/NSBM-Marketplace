<?php

include "../config/database.php";
include "../includes/auth.php";

$user_id = $_SESSION["user_id"];


/* ======================================================
   GET USER'S PRODUCTS
====================================================== */

$sql = "SELECT
            p.id,
            p.title,
            p.description,
            p.price,
            p.quantity,
            p.image,
            p.location,
            p.status,
            p.created_at,
            c.name AS category_name
        FROM products p
        LEFT JOIN categories c
            ON p.category_id = c.id
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

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

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
                        SELLER AREA
                    </span>

                    <h1>My Listings</h1>

                    <p>
                        Manage the products you have listed
                        on NSBM Marketplace.
                    </p>

                </div>


                <a
                    href="add-product.php"
                    class="add-listing-btn"
                >
                    + Add Product
                </a>

            </div>


            <!-- =========================================
                 PRODUCT LIST
            ========================================== -->

            <?php if ($result->num_rows > 0) { ?>

                <div class="listings-grid">

                    <?php while ($product = $result->fetch_assoc()) { ?>


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


                            <!-- PRODUCT CONTENT -->

                            <div class="listing-content">


                                <div class="listing-top">

                                    <span class="listing-category">

                                        <?php
                                        echo htmlspecialchars(
                                            $product["category_name"]
                                            ?? "Uncategorized"
                                        );
                                        ?>

                                    </span>


                                    <span
                                        class="listing-status status-<?php
                                            echo htmlspecialchars(
                                                $product["status"]
                                            );
                                        ?>"
                                    >

                                        <?php
                                        echo ucfirst(
                                            htmlspecialchars(
                                                $product["status"]
                                            )
                                        );
                                        ?>

                                    </span>

                                </div>


                                <h2>

                                    <?php
                                    echo htmlspecialchars(
                                        $product["title"]
                                    );
                                    ?>

                                </h2>


                                <p class="listing-description">

                                    <?php

                                    $description =
                                        $product["description"];

                                    if (
                                        strlen($description) > 100
                                    ) {

                                        echo htmlspecialchars(
                                            substr(
                                                $description,
                                                0,
                                                100
                                            )
                                        ) . "...";

                                    } else {

                                        echo htmlspecialchars(
                                            $description
                                        );

                                    }

                                    ?>

                                </p>


                                <!-- PRICE -->

                                <div class="listing-price">

                                    LKR
                                    <?php
                                    echo number_format(
                                        $product["price"],
                                        2
                                    );
                                    ?>

                                </div>


                                <!-- DETAILS -->

                                <div class="listing-details">

                                    <span>
                                        <strong>Quantity:</strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $product["quantity"]
                                        );
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


                    <?php } ?>

                </div>


            <?php } else { ?>


                <!-- =====================================
                     EMPTY STATE
                ====================================== -->

                <div class="empty-listings">

                    <div class="empty-listings-icon">
                        📦
                    </div>

                    <h2>No Products Listed Yet</h2>

                    <p>
                        You have not added any products to the
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

        </div>

    </main>


    <?php include "../includes/footer.php"; ?>


</body>

</html>

<?php

$stmt->close();

?>