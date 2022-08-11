<?php

/**
 * Created by VsCode
 * User: Shuva Biswas
 * Date: 10/08/2022
 * Time: 2:17 PM
 */

namespace App\Model;

use PDO, PDOException;

class Database
{
    protected $dbconnection;

    public function __construct($json_result)
    {
        try {
            $this->dbconnection = new PDO("mysql:host=localhost;dbname=assignment", "root", "");
            $this->dbconnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->prepare_query($json_result);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getDbInstance()
    {
        return $this->dbconnection;
    }

    public function prepare_query($result)
    {
        // inserting users data into mysql database
        $users_query = "INSERT INTO `users` (`name`, `user_phone`) VALUES (:name,:user_phone)";
        $data_array = array();
        $count = array();
        foreach ($result as $obj) {
            $user = $this->retrieve("SELECT COUNT(user_phone) as user FROM `users` WHERE user_phone= '" . $obj->user_phone . "'")['user'];

            if ($user == 0) {
                if (!in_array($obj->user_phone, $count)) {
                    array_push($data_array, array('name' => $obj->name, 'user_phone' => $obj->user_phone));
                    array_push($count, $obj->user_phone);
                }
            }
        }

        $this->delete_Update_Store($users_query, $data_array);


        // inserting products data into mysql database

        $products_query = "INSERT INTO `products` (`product_name`, `product_code`, `product_price`) VALUES (:product_name, :product_code, :product_price)";
        $count = array();
        $products_array = array();
        foreach ($result as $obj) {
            $product = $this->retrieve("SELECT COUNT(product_code) as product FROM `products` WHERE product_code= '" . $obj->product_code . "'")['product'];
            if ($product == 0) {
                if (!in_array($obj->product_code, $count)) {
                    array_push($products_array, array('product_name' => $obj->product_name, 'product_code' => $obj->product_code, 'product_price' => $obj->product_price));
                    array_push($count, $obj->product_code);
                }
            }
        }

        $this->delete_Update_Store($products_query, $products_array);


        // inserting orders data into mysql database

        $orders_query = "INSERT INTO `orders` (`user_id`, `order_no`, `created_at`) VALUES (:user_id, :order_no, :created_at)";
        $count = array();
        $orders_array = array();
        foreach ($result as $obj) {
            $order = $this->retrieve("SELECT COUNT(order_no) as 'order' FROM `orders` WHERE order_no= '" . $obj->order_no . "'")['order'];
            if ($order == 0) {
                $user_id = $this->retrieve("SELECT id FROM users WHERE user_phone = '" . $obj->user_phone . "'")['id'];
                if (!in_array($obj->order_no, $count)) {
                    array_push($orders_array, array('user_id' => $user_id, 'order_no' => $obj->order_no, 'created_at' => $obj->created_at));
                    array_push($count, $obj->order_no);
                }
            }
        }

        $this->delete_Update_Store($orders_query, $orders_array);


        // inserting order_items data into mysql database
        $order_items_query = "INSERT INTO `order_items` (`order_no`, `product_id`, `purchase_quantity`) VALUES (:order_no, :product_id, :purchase_quantity)";
        $count = array();
        $order_items_array = array();

        foreach ($result as $obj) {
            $product_id = $this->retrieve("SELECT id FROM `products` WHERE product_code = '" . $obj->product_code . "'")['id'];
            $order = $this->retrieve("SELECT COUNT(order_no) as 'order' FROM `order_items` WHERE order_no='" . $obj->order_no . "' and product_id=" . $product_id)['order'];

            if ($order == 0 and $product_id) {
                array_push($order_items_array, array('order_no' => $obj->order_no, 'product_id' => $product_id, 'purchase_quantity' => $obj->purchase_quantity));
            }
        }

        $this->delete_Update_Store($order_items_query, $order_items_array);
    }

    public function retrieve($query)
    {
        $statement = $this->dbconnection->prepare($query);
        $statement->execute();
        $data = $statement->fetch();
        return $data;
    }
    
    public function retrieve_all($query)
    {
        $statement = $this->dbconnection->prepare($query);
        $statement->execute();
        $data = $statement->fetchAll();
        return $data;
    }

    public function delete_Update_Store($query, $data_array)
    {
        $statement = $this->dbconnection->prepare($query);
        foreach ($data_array as $obj) {
            $statement->execute($obj);
        }
        return true;
    }

    public function get_report_datas()
    {
        $query = "SELECT name, SUM(purchase_quantity) as 'quantity', product_price, product_name, SUM(purchase_quantity)*product_price as 'total' 
        FROM users LEFT JOIN orders ON users.id = orders.user_id 
        LEFT JOIN order_items on order_items.order_no = orders.order_no 
        LEFT JOIN products on products.id = order_items.product_id 
        GROUP BY users.id 
        ORDER BY quantity 
        DESC";

        return $this->retrieve_all($query);
    }
}
