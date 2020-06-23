<?php

/*
if (($handle = fopen(base_url("order.csv"), "r")) !== FALSE)
    {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            //echo $data[2]."<br/>";
            //$this->db->query("Select count(*) FROM products WHERE STOCKID=");
            $this->db->query("UPDATE products SET current_stock=current_stock+'$data[2]' WHERE STOCKID ='".$data[1]."'");
        }
    fclose($handle);
    }


if (($handle = fopen(base_url("addproduct.csv"), "r")) !== FALSE)
    {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            //echo $data[1]."<br/>";
            //$this->db->query("Select count(*) FROM products WHERE STOCKID=");
            //$this->db->query("UPDATE products SET current_stock=current_stock+'$data[1]' WHERE BarCode ='".$data[0]."'");
        }
    fclose($handle);
    }
*/

/*
|----------------------------------------------------
|adding stock to the database
|----------------------------------------------------
*/

/*
if (($handle = fopen(base_url("JCS_Final_Stock_List.csv"), "r")) !== FALSE)
    {
        echo '<table>';
        echo '
                <tr>
                    <td>No</td>
                    <td>Productid</td>
                    <td>Stock ID</td>
                    <td>Barcode</td>
                    <td>Qty</td>
                </tr>
            ';
        $i=0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
        $i++;
            $stockid=$data[0];
            $barcode=$data[4];
            $product=$this->db->query("Select * FROM products WHERE BarCode='$barcode' AND STOCKID='$stockid'");
            $numrow=$product->num_rows();
            if($numrow==0){
                $qty = $data[5];
                if($data[5]==null || $data[5]==""){
                    $qty=0;
                }
                $pro_arr  = array (
                            'STOCKID'=>$data[0],
                            'BRAND'=>$data[1],
                            'DESCRIP1'=>$data[2],
                            'POSPrice'=>$data[3],
                            'BarCode'=>$data[4],
                            'current_stock'=>$qty
                        );
                $this->db->insert('products',$pro_arr);
                $in_id=$this->db->insert_id();
                echo '<tr>
                        <td>'.$i.'</td>
                        <td>'.$in_id.'</td>
                        <td>'.$data[0].'</td>
                        <td>'.$data[4].'</td>
                        <td>'.$qty.'</td>
                    </tr>
                ';
            }

        }
        echo '</table>';
    fclose($handle);
    }
*/

/*
|----------------------------------------------------
|Deduct quantity form product
|----------------------------------------------------
*/

/*
if (($handle = fopen(base_url("order_item_abv_734.csv"), "r")) !== FALSE)
    {

        $affectedrow = null;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            $query=$this->db->query("SELECT * FROM products WHERE PRODUCTID='$data[2]'");
            $numrow=$query->num_rows();
            if($numrow == 0){
                echo 'These Product Can not be found <br/>';
                echo $data[2].'<br/>';
            }else{
                $this->db->query("UPDATE products SET current_stock=current_stock-'$data[3]' WHERE PRODUCTID='$data[2]'");
                $affectedrow = $affectedrow+$this->db->affected_rows();
            }
        }
        echo 'effected row='.$affectedrow;
    fclose($handle);
    }
*/

/*
|----------------------------------------------------
|add product into the system
|----------------------------------------------------
*/


if (($handle = fopen(base_url("stationery_replenish_31_Mar_2014.csv"), "r")) !== FALSE)
    {
        echo '<table border="1">';
        echo '
                <tr>
                    <td>No</td>
                    <td>Name</td>
                    <td>Description</td>
                    <td>BarCode</td>
                    <td>Status  </td>
                </tr>
            ';
        $i=0;
        $add_num = 0;
        $update_num = 0 ;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            $i++;
            $stockid=$data[1];
            $barcode=$data[4];
            $qty = $data[5];

            //echo $stockid .'~'; echo $barcode; echo $qty; exit;
            $query=$this->db->query("SELECT * FROM products WHERE BarCode='$barcode' AND STOCKID='$stockid'");
            $numrow=$query->num_rows();
            $prow=$query->row();
            if($numrow > 0){
                $update_num++;
                $this->db->query("UPDATE products SET current_stock=current_stock+'$qty' WHERE BarCode='$barcode' AND STOCKID='$stockid'");

                $stock_arr  = array (
                            'product_id'=>$prow->PRODUCTID,
                            'stock_in'=>$data[5],
                            'date'=> date("Y-m-d H:i:s")
                        );
                $this->db->insert('stock',$stock_arr);

                echo '<tr><td>'.$i.'</td><td>'.$data[0].'</td><td>'.$data[2].'</td><td>'.$data[4].'</td><td>Updated</td></tr>';
            }else{
                $add_num++;
                $pro_arr  = array (
                            'STOCKID'=>$data[1],
                            'BRAND'=>$data[0],
                            'DESCRIP1'=>$data[2],
                            'POSPrice'=>$data[3],
                            'BarCode'=>$data[4],
                            'current_stock'=>$data[5]
                        );
                $this->db->insert('products',$pro_arr);
                $in_id=$this->db->insert_id();

                $stock_arr  = array (
                            'product_id'=>$in_id,
                            'stock_in'=>$data[5],
                            'date'=> date("Y-m-d H:i:s")
                        );
                $this->db->insert('stock',$stock_arr);                

                echo '<tr><td>'.$i.'</td><td>'.$data[0].'</td><td>'.$data[2].'</td><td>'.$data[4].'</td><td>Added</td></tr>';
            }
        }
        echo '</table>';
        echo 'added => '.$add_num.' row(s).<br/>';
        echo 'updated => '.$update_num.' row(s).<br/>';

    fclose($handle);
    }


/*
|----------------------------------------------------
|add order item
|----------------------------------------------------
*/
//if (($handle = fopen(base_url("order_item_abv_734.csv"), "r")) !== FALSE)
//    {
//
//        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
//        {
//            $oi_arr  = array (
//                            'order_item_id'=>$data[0],
//                            'order_id'=>$data[1],
//                            'product_id'=>$data[2],
//                            'order_qty'=>$data[3]
//                        );
//           $this->db->insert('order_item',$oi_arr);
//
//        }
//    fclose($handle);
//    }



/*
|----------------------------------------------------
|adding stock to the database
|----------------------------------------------------
*/


//if (($handle = fopen(base_url(".csv"), "r")) !== FALSE)
//    {
//        echo '<table>';
//        echo '
//                <tr>
//                    <td>No</td>
//                    <td>Productid</td>
//                    <td>Stock ID</td>
//                    <td>Barcode</td>
//                    <td>Qty</td>
//                </tr>
//            ';
//        $i=0;
//        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
//        {
//        $i++;
//            $stockid=$data[0];
//            $barcode=$data[4];
//            $product=$this->db->query("Select * FROM products WHERE BarCode='$barcode' AND STOCKID='$stockid'");
//            $numrow=$product->num_rows();
//            if($numrow==0){
//                $qty = $data[5];
//                if($data[5]==null || $data[5]==""){
//                    $qty=0;
//                }
//                $pro_arr  = array (
//                            'STOCKID'=>$data[0],
//                            'BRAND'=>$data[1],
//                            'DESCRIP1'=>$data[2],
//                            'POSPrice'=>$data[3],
//                            'BarCode'=>$data[4],
//                            'current_stock'=>$qty
//                        );
//                $this->db->insert('products',$pro_arr);
//                $in_id=$this->db->insert_id();
//                echo '<tr>
//                        <td>'.$i.'</td>
//                        <td>'.$in_id.'</td>
//                        <td>'.$data[0].'</td>
//                        <td>'.$data[4].'</td>
//                        <td>'.$qty.'</td>
//                    </tr>
//                ';
//            }
//
//        }
//        echo '</table>';
//    fclose($handle);
//    }

?>
