<?php


require_once('./vendor/autoload.php');
use App\Model\Database;

$result = [];
$gross_total = 0;
$gross_price = 0;
$total_quantity = 0;


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
    $db = new Database($result);

    // get report's data
    $result = $db->get_report_datas();

    global $total_quantity;
    global $gross_price;
    global $gross_total; 

    foreach($result as $obj) {
        $total_quantity += $obj['quantity'];
        $gross_price += $obj['product_price'];
        $gross_total += $obj['total'];
    }
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
                        <td><?php echo $obj['product_name']; ?></td>
                        <td><?php echo $obj['name']; ?></td>
                        <td><?php echo $obj['quantity']; ?></td>
                        <td><?php echo $obj['product_price']; ?></td>
                        <td><?php echo $obj['total']; ?></td>
                    </tr>

                <?php endforeach; ?>
                <tr>
                    <td colspan="2" class="text-right">Gross Total</td>
                    <td><?php echo $total_quantity; ?></td>
                    <td><?php echo $gross_price; ?></td>
                    <td><?php echo $gross_total; ?></td>
                </tr>
            </tbody>
        </table>

    </div>
</body>

</html>