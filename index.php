<?php

$result = [];

function load()
{
    $url = 'https://raw.githubusercontent.com/Bit-Code-Technologies/mockapi/main/purchase.json';

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
        "Accept: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    global $result;
    $result = json_decode(curl_exec($curl));
    curl_close($curl);
}

foreach ($result as $obj) {
    echo $obj->name;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    load();
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Assignment - BITCode Technologies</title>
</head>

<body>

    <div class="container my-basic">
        <form action="index.php" method="POST">
            <button class="btn w-100">Load</button>
        </form>
    </div>

    <p>&nbsp;</p>
        <div class="container">
            <table class="w-100">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Customer Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $obj) : ?>

                        <tr>
                            <td><?php echo $obj->product_name; ?></td>
                            <td><?php echo $obj->name; ?></td>
                            <td><?php echo $obj->purchase_quantity; ?></td>
                            <td><?php echo $obj->product_price; ?></td>
                            <td><?php echo $obj->product_price * $obj->purchase_quantity; ?></td>
                        </tr>

                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="text-right">Gross Total</td>
                        <td>10</td>
                        <td>889.00</td>
                        <td>4445</td>
                    </tr>
                </tbody>
            </table>

        </div>
</body>

</html>