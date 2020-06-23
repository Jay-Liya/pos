<?php

class Product_model extends CI_Model{

    /*
    | -------------------------------------------------------------------
    | Get Product Detail
    | -------------------------------------------------------------------
    */
    function checkLogin($username,$txtpassword){

        $result= $this->db->query("SELECT * FROM cck_user WHERE user_name='$username' AND user_password='$txtpassword' ");
        $row = $result->row();
        $count = $result->num_rows();

        if($count==1){
            return true;
        }else{
            return false;
        }
    }

    function getOrder($txtFrom,$txtTo){

        $query = $this->db->query("SELECT cck_order.order_id,cck_products.STOCKID,cck_order_item.order_qty
                                     FROM (`cck_order`)
                                     JOIN `cck_order_item`
                                       ON `cck_order`.`order_id` = `cck_order_item`.`order_id`
                                     JOIN `cck_products`
                                       ON `cck_order_item`.`product_id` = `cck_products`.`PRODUCTID`
                                    WHERE `cck_order`.`order_date`
                                  BETWEEN '$txtFrom'
                                      AND '$txtTo'
                                 ");

        return $query;
    }

    function getAllProductByLimit($start_row,$per_page){

        $query = $this->db->query("SELECT * FROM cck_products ORDER BY PRODUCTID ASC LIMIT $start_row,$per_page");
        return $query;
    }

    function getAllProduct(){

        $query = $this->db->query("SELECT * FROM cck_products");
        return $query;
    }

    function productList($options){

        $query = $this->db->query("SELECT * FROM cck_products WHERE BarCode='$options' ");
        return $query;
    }

    function getProduct($barcode){

        $query = $this->db->query("SELECT * FROM cck_products WHERE BarCode='$barcode' ");
        return $query;
    }

    function insertorder($order){

        $this->db->insert('cck_order',$order);
        $order_id= $this->db->insert_id();

        foreach($this->cart->contents() as $items){


            if(isset($items['paperPcRm']) && $items['paperPcRm']=='rm'){

              $order_item= array(
                  'order_id'=>$order_id,
                  'product_id'=> $items['id'],
                  'order_qty' => ($items['qty'] * 500),
                  'printing' => 'rm'
              );
              $quantity = $items['qty'] * 500;
            }
            else if(isset($items['paperPcRm']) && $items['paperPcRm']=='pc'){

              if($items['options']['product_BarCode']=='PAPERA4'){

                $order_item= array(
                    'order_id'=>$order_id,
                    'product_id'=> $items['id'],
                    'order_qty' => $items['qty'],
                    'printing' => $items['paperColor']."A4".$items['paperSide']
                );
                $quantity = $items['qty'];
              }
              else if($items['options']['product_BarCode']=='PAPERA3'){

                $order_item= array(
                    'order_id'=>$order_id,
                    'product_id'=> $items['id'],
                    'order_qty' => $items['qty'],
                    'printing' => $items['paperColor']."A3".$items['paperSide']
                );
                $quantity = $items['qty'];
              }
            }
            else if($items['stockid']=='SOCK' && $this->session->userdata('sock')!==False){

              $order_item= array(
                  'order_id'=>$order_id,
                  'product_id'=> $items['id'],
                  'order_qty' => $items['qty'],
                  'printing' => $this->session->userdata('sock')." pair"
              );
              $quantity = $this->session->userdata('sock')*$items['qty'];
            }
            else{

              $order_item= array(
                  'order_id'=>$order_id,
                  'product_id'=> $items['id'],
                  'order_qty' => $items['qty']
              );
              $quantity = $items['qty'];
            }

            $this->db->insert('cck_order_item',$order_item);
            $productid= $items['id'];

            if(strpos($items['options']['product_BarCode'], 'BINDA')===false)
              $this->db->query("UPDATE cck_products SET current_stock = current_stock - '$quantity' WHERE PRODUCTID='$productid'");

            //BindA36mm....BindA351mm and BindA46mm......BindA451mm
            //Plas6,CoverA4 or CoverA3, FCoverA4 or FCoverA3

            $productstockid=$items['stockid'];

            if(strpos($productstockid, 'BindA') !== false && strpos($productstockid, 'mm') !== false){

              $plas="Plas";
              $cover="CoverA4";
              $fcover="FCoverA4";

              if(strlen($productstockid)==9){
                $plas=$plas.substr($productstockid, -3, 1);
                if(substr($productstockid, -4, 1)=="3"){
                  $cover="CoverA3";
                  $fcover="FCoverA3";
                }
              }
              else{
                $plas=$plas.substr($productstockid, -4, 2);
                if(substr($productstockid, -5, 1)=="3"){
                  $cover="CoverA3";
                  $fcover="FCoverA3";
                }
              }

              $query=$this->db->query("SELECT PRODUCTID, current_stock FROM cck_products WHERE STOCKID='$plas' OR STOCKID='$cover' OR STOCKID='$fcover'");
              $productIdArr = $query->result();
              $product_numrow = $query->num_rows();

              if($product_numrow>0){
                foreach($productIdArr AS $productRow){
                  $this->db->query("UPDATE cck_products SET current_stock = current_stock - '$quantity' WHERE PRODUCTID='$productRow->PRODUCTID'");
                  $stockhistorytmp = array(
                      'product_id'=>$productRow->PRODUCTID,
                      'stock_out'=>$quantity,
                      'date'=>date("Y-m-d H:i:s"),
                      'balance'=>$productRow->current_stock-$quantity
                  );
                  $this->db->insert('cck_stock',$stockhistorytmp);

                  $age_query=$this->Product_model->getItemsByAgingFromPro($productRow->PRODUCTID);

                  foreach($age_query as $itemAge){

                      if($itemAge->QUANTITY<$quantity){
                        $this->Product_model->updateItemInAge($itemAge->ID,0);
                        $quantity=$quantity-$itemAge->QUANTITY;
                      }
                      else{
                        $this->Product_model->updateItemInAge($itemAge->ID,$itemAge->QUANTITY-$quantity);
                        break;
                      }
                  }
                }
              }
            }

            if(strpos($items['options']['product_BarCode'], 'BINDA')===false){

              $queryforstk=$this->db->query("SELECT current_stock FROM cck_products WHERE PRODUCTID='$productid'");
              $productforstk = $queryforstk->row();
              //save in stock history

              $stockhistory = array(
                  'product_id'=>$productid,
                  'stock_out'=>$quantity,
                  'date'=>date("Y-m-d H:i:s"),
                  'balance'=>$productforstk->current_stock
              );

              $this->db->insert('cck_stock',$stockhistory);

              $age_query=$this->Product_model->getItemsByAgingFromPro($productid);

              foreach($age_query as $itemAge){

                  if($itemAge->QUANTITY<$quantity){
                    $this->Product_model->updateItemInAge($itemAge->ID,0);
                    $quantity=$quantity-$itemAge->QUANTITY;
                  }
                  else{
                    $this->Product_model->updateItemInAge($itemAge->ID,$itemAge->QUANTITY-$quantity);
                    break;
                  }
              }
           }
        }
        return true;
    }

    function getReceiptNum(){

      $this->db->select('MAX(order_id) AS "Max"');
      $this->db->from('cck_order');
      $maxquery = $this->db->get();
      $maxrow = $maxquery->result();
      $max_numrow = $maxquery->num_rows();
      $receiptnum=0;

      if($max_numrow>0)
        foreach($maxrow AS $maxnum)
          $receiptnum=1+$maxnum->Max;

      $charlength=strlen($receiptnum);
      $receiptnumStr=(string)$receiptnum;

      if($charlength<6)
        for($k=0;$k<(6-$charlength);$k++)
          $receiptnumStr="0".$receiptnumStr;
      return $receiptnumStr;
    }

    function getOrderedProducts(){

      $this->db->select('*');
      $this->db->from('cck_order');
      $this->db->join('cck_order_item', 'cck_order.order_id = cck_order_item.order_id');
      $this->db->join('cck_products', 'cck_products.PRODUCTID = cck_order_item.product_id');
      $this->db->where('DATE(cck_order.order_date)', date("Y-m-d"));
      $orderquery = $this->db->get();
      return $orderquery->result();
    }

    function getProductsbyBarCode($barcode){

      $product_query=$this->db->query("SELECT * FROM cck_products WHERE BarCode='$barcode'");
      return $product_query->result();
    }

    function roundingFunc($num){

      $num=round($this->cart->total(),2);
      $numRnded=round($num,1);

      $differ=round($numRnded-$num,2);
      if($differ==0.05) $numRnded=$numRnded-0.05;
      else if($differ<-0.02) $numRnded=$numRnded+0.05;
      return $numRnded;

    //  if($differ>0)
  /*    $returnVal=0.00;

      if($num>$numRnded) $returnVal=$numRnded+0.05;
      else{

        $str="".$numRnded-$num;
        if(strcmp($str,"0.05")==0) $returnVal=$num;
        else $returnVal=$numRnded;
      }

      return $numRnded; */
    }

    function productRowByID($productID){

      $product_query= $this->db->query("SELECT * FROM cck_products WHERE PRODUCTID='$productID'");
      return $product_query->row();
    }

    function productByStockID($stockid){

      return $this->db->query("SELECT * FROM cck_products WHERE STOCKID='$stockid'");
    }

    function updateStockAmount($stockid,$qty){

      return $this->db->query("UPDATE cck_products SET current_stock=current_stock+'$qty' WHERE STOCKID='$stockid'");
    }

    function getStockByDates($productID,$start,$end){

      $query=$this->db->query("SELECT * FROM cck_stock WHERE (product_id='".$productID."' or product_id Like '".$productID."A%') and DATE_FORMAT(`date`, '%Y-%m-%d') between '$start' and '$end' order by date");
      return $query->result();
    }

    function getStockByTheDate($productID,$date){

      $query=$this->db->query("SELECT * FROM cck_stock WHERE (product_id='".$productID."' or product_id Like '".$productID."A%') and DATE_FORMAT(`date`, '%Y-%m-%d') = '$date'");
      return $query->result();
    }

    function updateProduct($stockid,$brand,$desc,$cost,$price,$barcode,$cat,$status,$newQty){

      $desc=str_replace("'","",$desc);
      $barcode=str_replace("'","",$barcode);
      $brand=str_replace("'","",$brand);

      return $this->db->query("UPDATE cck_products SET BRAND='$brand', DESCRIP1='$desc', COST='$cost', POSPrice='$price', BarCode='$barcode', Category='$cat', active='$status', current_stock=current_stock+'$newQty' WHERE STOCKID='$stockid'");
    }

    function getItemsByAging($stockid){

      $query=$this->db->query("SELECT * FROM cck_age WHERE STOCK_ID='$stockid' and QUANTITY>0 order by IN_DATE");
      return $query->result();
    }

    function updateItemInAge($ID,$Qty){

      return $this->db->query("UPDATE cck_age SET QUANTITY='$Qty' WHERE ID='$ID'");
    }

    function getItemsByAgingFromPro($productID){

      $query=$this->db->query("SELECT * FROM cck_age WHERE PRODUCT_ID='$productID' and QUANTITY>0 order by IN_DATE");
      return $query->result();
    }

    function getItemsByAgingForRpt($productID){

      $query=$this->db->query("SELECT * FROM cck_age WHERE PRODUCT_ID='$productID' order by IN_DATE");
      return $query->result();
    }

    function getOrderedBarcodes(){

      $query=$this->db->query("SELECT BarCode FROM cck_products GROUP BY BarCode ORDER BY BarCode");
      return $query->result();
    }

    function getGroupedCat(){

      $query=$this->db->query("SELECT Category FROM cck_products group by Category");
      return $query->result();
    }

    function getProductsWithAging(){

      $query=$this->db->query("SELECT cck_products.STOCKID, cck_products.BRAND, cck_products.DESCRIP1, cck_products.COST, cck_products.POSPrice, cck_products.BarCode, cck_products.current_stock, cck_products.Category, date(cck_age.IN_DATE), cck_age.QUANTITY FROM cck_products, cck_age where cck_products.PRODUCTID=cck_age.PRODUCT_ID order by cck_products.PRODUCTID
");
      return $query;
    }

    function getProductsWithBalace($date, $productID){

      $query=$this->db->query("SELECT balance FROM cck_stock WHERE date<'$date' and product_id='$productID' order by date desc limit 1");
      return $query->result();
    }



    // function getProductsWithBalace($date, $barcode){
    //
    //   $query=$this->db->query("SELECT t.product_id, t.date, t.balance, cck_products.STOCKID from cck_stock t inner join ( select product_id, max(date) as MaxDate from cck_stock group by product_id ) tm on (t.product_id = tm.product_id and t.date = tm.MaxDate) and t.date<='$date' join cck_products on t.product_id=cck_products.PRODUCTID and cck_products.BarCode='$barcode' order by PRODUCTID");
    //   return $query->result();
    // }
}

?>
