<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pos extends CI_Controller {

      public function __construct(){

          parent::__construct();
          $this->load->helper('form');
          $this->load->helper('url');
          $this->load->model('Product_model');
          $this->load->helper('html');
          $this->load->library('form_validation');
          $this->load->library('pagination');
          include APPPATH . 'third_party/excel/excel_reader.php';
          include APPPATH . 'third_party/excel/SpreadsheetReader.php';
          include APPPATH . 'third_party/excel/SpreadsheetReader_XLSX.php';
          include APPPATH . 'third_party/excel/SpreadsheetReader_XLS.php';
          include APPPATH . 'third_party/excel/SpreadsheetReader_CSV.php';
          include APPPATH . 'third_party/excel/SpreadsheetReader_ODS.php';

      }

      function updateSql(){
         $this->load->view('updateSql');
     }

      function order(){

         if($this->session->userdata('logged_in')){

              if(isset($_REQUEST['btnGenerate'])){

                  $txtFrom=$this->input->post('txtFrom');
                  $txtTo=$this->input->post('txtTo');
                  $data['order']=$this->Product_model->getOrder($txtFrom,$txtTo);
                  $data['from']=$txtFrom;
                  $data['to']=$txtTo;
                  $this->load->view('header');
                  $this->load->view('order',$data);
                  $this->load->view('footer');

             }else{

                  $this->load->view('header');
                  $this->load->view('order');
                  $this->load->view('footer');
             }

          }else{
              redirect('login', 'refresh');
          }
     }

      function updateSession(){

         if(isset($_REQUEST['changToStockId'])){

              $newStockId = $this->input->post('changToStockId');
              $rowid = $this->input->post('oldRowId');
              $done=$this->cart->update(array('rowid'=>$rowid,'qty'=> 0 ));

              //if item exist
              $flag = True;
               $dataTmp = $this->cart->contents();

               foreach($dataTmp as $item){

                   if($item['id']==$newStockId){

                       $flag = false;
                       break;
                   }
               }

              //item not exist, create new
              if($flag){

                  $product_row = $this->Product_model->productRowByID($newStockId);

                  $insert= array(
                                   'types'=>1,
                                   'id'=> $product_row->PRODUCTID ,
                                   'name' => 'product_name',
                                   'stockid'=> $product_row->STOCKID,
                                   'price' => $product_row->POSPrice,
                                   'qty' => '1',
                                   'stock_Qty' => $product_row->current_stock,
                                   'options' => array(
                                                'product_BRAND' => $product_row->BRAND,
                                                'product_DESCRIP1' => $product_row->DESCRIP1,
                                                'product_BarCode' => $product_row->BarCode,
                                                ),
                               );

                   $done2=$this->cart->insert($insert);

               }
               if($done && $done2){
                   echo 'Your item is succesfully updated.';
               }
         }
      }

      function stock(){

         if($this->session->userdata('logged_in')&&($this->session->userdata('username')=="admin"||$this->session->userdata('username')=="manager")){

              if(isset($_REQUEST['btnsubmit'])){

                  $stockid=$this->input->post('selItem');
                  $query=$this->Product_model->productByStockID($stockid);
                  $data['productnumrow']=$query->num_rows();
                  $data['productquery']= $query;
                  $data['result']= 'result';

                  $this->load->view('header');
                  $this->load->view('stock',$data);
                  $this->load->view('footer');

             }else{

                  $per_page = 15;
                  $start_row= $this->uri->segment(3);

                  if($start_row==''){
                      $start_row=0;
                  }

                  $products=$this->Product_model->getAllProduct();
                  $product_numrow=$products->num_rows();

                  $data['product_query']=$this->Product_model->getAllProductByLimit($start_row,$per_page);

                  $config['base_url'] = site_url("pos/stock");
                  $config['total_rows'] = $product_numrow;
                  $config['per_page'] = $per_page;
                  $config['uri_segment'] = 3;
                  $config['full_tag_open'] = '<div class="pagination">';
                  $config['full_tag_close'] = '</div>';
                  $config['next_link'] = 'Next &gt;';
                  $config['prev_link'] = '&lt; Previous ';
                  $config['cur_tag_open'] = '<span class="current">';
                  $config['cur_tag_close'] = '</span>';

                  $this->pagination->initialize($config);
                  $data['pagination']=$this->pagination->create_links();
                  $data['startrow'] = $start_row;
                  $data['numrow']=$product_numrow;

                  $this->load->view('header');
                  $this->load->view('stock',$data);
                  $this->load->view('footer');
             }
          }else{
              redirect('login', 'refresh');
          }
     }

     function updatestk(){

       if($this->session->userdata('logged_in')){

        if(isset($_REQUEST['btnsubmit'])){

          $data['msg']=$this->input->post($_FILES["file"]["type"]);

          $this->load->view('header');
          $this->load->view('updatestk',$data);
          $this->load->view('footer');
        }
        else{

          $this->load->view('header');
          $this->load->view('updatestk');
          $this->load->view('footer');
        }
      }else{
          redirect('login', 'refresh');
      }
    }

    function newitem(){

      if($this->session->userdata('logged_in')){

        if(isset($_REQUEST['btnnew'])){

          $barcode=trim($this->input->post('txtbrcd'));
          $stockid=trim($this->input->post('txtstkid'));
          $brand=trim($this->input->post('txtbrand'));
          $desc=trim($this->input->post('txtdesc'));
          $cost=trim($this->input->post('txtcost'));
          $price=trim($this->input->post('txtprice'));
          $cat=$this->input->post('selcat');
          $qty=trim($this->input->post('txtqty'));
          $donum=trim($this->input->post('txtdo'));

          $mdate=trim($this->input->post('frmdate'));;
          if($mdate!="1970/01/01" && $mdate!="") $mdate="(".date("Y/m/d", strtotime($mdate)).")";
          else if($mdate=="1970/01/01") $mdate="";

          $donum=$donum.$mdate;

          $query=$this->Product_model->productByStockID($stockid);

          if($query->num_rows() > 0)
            $data['output']="<p align='center' style='color: red;'>Failed. StockID already used.</p>";
          else{
            $desc=str_replace("'","",$desc);
            $barcode=str_replace("'","",$barcode);
            $brand=str_replace("'","",$brand);
            $item_arr  = array (
                        'STOCKID'=>$stockid,
                        'BRAND'=>$brand,
                        'DESCRIP1'=>$desc,
                        'COST'=>$cost,
                        'POSPrice'=>$price,
                        'BarCode'=>$barcode,
                        'current_stock'=>$qty,
                        'Category'=>strtoupper($cat)
                    );
            $this->db->insert('cck_products',$item_arr);
            $in_id=$this->db->insert_id();

            date_default_timezone_set('Asia/Singapore');
            $stock_arr  = array (
                        'product_id'=>$in_id,
                        'stock_in'=>$qty,
                        'date'=> date("Y-m-d H:i:s"),
                        'balance'=>$qty,
                        'donumber'=>$donum
                    );
            $this->db->insert('cck_stock',$stock_arr);

            if($in_id > 0) $data['output']="<p align='center' style='color: green;'><b>Succesfully Added.</b></p>";
            else $data['output']="<p align='center' style='color: red;'>Failed.</p>";

            if($qty>0){
              $age_arr=array(
                'PRODUCT_ID'=>$in_id,
                'STOCK_ID'=>$stockid,
                'IN_DATE'=> date("Y-m-d H:i:s"),
                'DONUMBER'=>$donum,
                'QUANTITY'=>$qty
              );
              $this->db->insert('cck_age',$age_arr);
            }
          }

           $this->load->view('header');
           $this->load->view('newitem',$data);
           $this->load->view('footer');
         }
         else{

           $this->load->view('header');
           $this->load->view('newitem');
           $this->load->view('footer');
         }
       }else{
           redirect('login', 'refresh');
       }
   }

   function modifyitem(){

     if(($this->session->userdata('logged_in'))&&($this->session->userdata('username')=="admin"||$this->session->userdata('username')=="manager")){

        if(isset($_REQUEST['btnmodify'])){

          $currentstk=trim($this->input->post('itemqty'));
          $newQty=trim($this->input->post('txtnewqty'));
          $newQtyDe=trim($this->input->post('txtnewqtyde'));
          if($newQty==''&&$newQtyDe!='')
            $newQty=-1*$newQtyDe;
          $barcode=trim($this->input->post('txtbrcd'));
          $productid=$this->input->post('txtpdtid');
          $stockid=$this->input->post('txtstkid');

          $brand=trim($this->input->post('txtbrand'));
          $desc=trim($this->input->post('txtdesc'));
          $cost=trim($this->input->post('txtcost'));
          $price=trim($this->input->post('txtprice'));
          $cat=$this->input->post('selcat');
          $status=trim($this->input->post('txtstatus'));

          $doadj=$this->input->post('seldonumadj');
          $donum="";
          $remark="";
          $mdate=trim($this->input->post('frmdate'));;
          if($mdate!="1970/01/01" && $mdate!="") $mdate="(".date("Y/m/d", strtotime($mdate)).")";
          else if($mdate=="1970/01/01") $mdate="";

          if($doadj=='d')
            $donum=trim($this->input->post('txtdo')).$mdate;
          else{
            $remark=trim($this->input->post('txtrmk')).$mdate;

            if($remark=='Type remark here' || $remark=="") $remark="";
            else $remark=": ".$remark;

            if($this->input->post('seladj')=='q')
              $remark="Quantity mismatch".$remark;
            else $remark="Other".$remark;
          }

          date_default_timezone_set('Asia/Singapore');
          $query=$this->Product_model->updateProduct($stockid,$brand,$desc,$cost,$price,$barcode,$cat,$status,$newQty);
          $currentstk=$currentstk+$newQty;

          if($newQty>0){

            $stock_arr  = array (
                        'product_id'=>$productid,
                        'stock_in'=>$newQty,
                        'date'=> date("Y-m-d H:i:s"),
                        'remark'=>$remark,
                        'donumber'=>$donum,
                        'balance'=>$currentstk
                    );
            $this->db->insert('cck_stock',$stock_arr);

            $age_arr=array(
              'PRODUCT_ID'=>$productid,
              'STOCK_ID'=>$stockid,
              'IN_DATE'=> date("Y-m-d H:i:s"),
              'DONUMBER'=>$donum,
              'QUANTITY'=>$newQty
            );
            $this->db->insert('cck_age',$age_arr);
          }
          else if($newQty<0){

            $stock_arr  = array (
                        'product_id'=>$productid,
                        'stock_out'=>$newQtyDe,
                        'remark'=>$remark,
                        'date'=> date("Y-m-d H:i:s"),
                        'donumber'=>$donum,
                        'balance'=>$currentstk
                    );
            $this->db->insert('cck_stock',$stock_arr);

            $age_query=$this->Product_model->getItemsByAging($stockid);

            foreach($age_query as $itemAge){

                if($itemAge->QUANTITY<$newQtyDe){
                  $this->Product_model->updateItemInAge($itemAge->ID,0);
                  $newQtyDe=$newQtyDe-$itemAge->QUANTITY;
                }
                else{
                  $this->Product_model->updateItemInAge($itemAge->ID,$itemAge->QUANTITY-$newQtyDe);
                  break;
                }
            }
          }

          if($query)
            $data['output']="<p align='center' style='color: green;'>Succesfully Modified.</p>";
          else $data['output']="<p align='center' style='color: red;'>Unable to modify.</p>";

          $this->load->view('header');
          $this->load->view('modifyitem',$data);
          $this->load->view('footer');
        }
        else if(isset($_REQUEST['btnsubmit'])){

          $stockid=$this->input->post('selItem');
          $query=$this->Product_model->productByStockID($stockid);
          $prow=$query->row();

          if($query->num_rows() > 0){

            $data['barcode']=$prow->BarCode;
            $data['productid']=$prow->PRODUCTID;
            $data['stockid']=$prow->STOCKID;
            $data['brand']=$prow->BRAND;
            $data['desc']=$prow->DESCRIP1;
            $data['cost']=$prow->COST;
            $data['price']=$prow->POSPrice;
            $data['currentstock']=$prow->current_stock;
            $data['category']=$prow->Category;
            $data['active']=$prow->active;

            $this->load->view('header');
            $this->load->view('modifyitem',$data);
            $this->load->view('footer');
          }
          else{

            $this->load->view('header');
            $this->load->view('modifyitem');
            $this->load->view('footer');
          }
        }
        else{

          $this->load->view('header');
          $this->load->view('modifyitem');
          $this->load->view('footer');
        }

     }else{
         redirect('login', 'refresh');
     }
  }

  function stockreport(){

    if(($this->session->userdata('logged_in'))&&($this->session->userdata('username')=="admin"||$this->session->userdata('username')=="manager")){

      if(isset($_REQUEST['btnsrch'])){

        $productID=$this->input->post('selItem');

        $partsofst = explode('/',$this->input->post('frmdate'));
        $strStart = $partsofst[2] . '-' . $partsofst[0] . '-' . $partsofst[1];
        $start = date("Y-m-d", strtotime($strStart));

        $partsofend = explode('/',$this->input->post('todate'));
        $strEnd = $partsofend[2] . '-' . $partsofend[0] . '-' . $partsofend[1];
        $end = date("Y-m-d", strtotime($strEnd));

        if($productID!='-1'){

          $ItemsArray=$this->Product_model->getStockByDates($productID,$start,$end);
          $product=$this->Product_model->productRowByID($productID);

          $output="<h3><b>ITEM:</b> ".$product->DESCRIP1."</h3>";
          $output=$output."<h3><b>From:</b> ".$start." <b>To:</b> ".$end."</h3>";
          $output=$output."<table cellpadding='5px' cellspacing='5px' border='1' style='width:625px; margin-top:5px; border:1px solid gray; border-collapse:collapse;'>";
          $output=$output."<tr style='background:  #d3d3d3;'><th width='100px'>Date</th><th width='75px'>In</th><th width='100px'>DO Number</th><th width='75px'>Out</th><th width='200px'>Remarks</th><th width='75px'>Balance</th></tr>";

          foreach($ItemsArray as $Item){
              if($Item->stock_in!=0||$Item->stock_out!=0)
                $output=$output."<tr><td align='center'>".date("d-M-Y", strtotime($Item->date))."</td><td align='center'>".$Item->stock_in."</td><td>".$Item->donumber."</td><td align='center'>".$Item->stock_out."</td><td>".$Item->remark."</td><td align='center'>".$Item->balance."</td></tr>";
          }
          $output=$output."</table>";
          $data['output']=$output;
        }
        else{

          $brcode=$this->input->post('selBarcode');
          $output=$brcode;
          $productsArr=$this->Product_model->getProductsbyBarCode($brcode);

          $endNext=date('Y-m-d', strtotime($end . '+1 day'));

          $output="<h3><b>BARCODE: </b>".$brcode."</h3>";
          $output=$output."<table cellpadding='5px' cellspacing='5px' border='1' style='margin-top:5px; border:1px solid gray; border-collapse:collapse;'>";
          $output=$output."<tr style='background:  #d3d3d3;'><th width='100px' rowspan='2'>Date</th>";
          $strtemp="<tr style='background:  #d3d3d3;'>";
          $strfrstrw="<tr><td>".$start." (bal)</td>";
          $strlstrw="<tr><td>".$end." (bal)</td>";

          foreach($productsArr as $Product){

            $output=$output."<th colspan='3'>".$Product->STOCKID."</th>";
            $strtemp=$strtemp."<td width='20px' align='center' >In</td><td  width='20px' align='center'  >Out</td><td  width='20px' align='center'  >Bal</td>";

            $productsArrFrstBal=$this->Product_model->getProductsWithBalace($start, $Product->PRODUCTID);
            $productsArrLstBal=$this->Product_model->getProductsWithBalace($endNext, $Product->PRODUCTID);

            if(count($productsArrFrstBal)>0){
              foreach($productsArrFrstBal as $ProductBal){
                $strfrstrw=$strfrstrw."<td></td><td></td><td align='right'  >".$ProductBal->balance."</td>";
              }
            }
            else $strfrstrw=$strfrstrw."<td></td><td></td><td align='right'>0</td>";

            if(count($productsArrLstBal)>0){
              foreach($productsArrLstBal as $ProductlstBal){
                $strlstrw=$strlstrw."<td></td><td></td><td align='right'  >".$ProductlstBal->balance."</td>";
              }
            }
            else $strlstrw=$strlstrw."<td></td><td></td><td align='right'>0</td>";
          }
          $output=$output."</tr>";
          $output=$output.$strtemp."</tr>";
          $output=$output.$strfrstrw."</tr>";

          for($date=$start;$date<=$end;$date=date('Y-m-d', strtotime($date . ' +1 day'))){

            $dateValid=FALSE;
            $strRow="";
            $rowindex=0;

            foreach($productsArr as $Product){

              $productID=$Product->PRODUCTID;
              $StockArray=$this->Product_model->getStockByTheDate($productID,$date);
              $stkintbl="<table border='0' cellpadding='0' cellspacing='0' >";
              $stkouttbl="<table border='0' cellpadding='0' cellspacing='0' >";
              $stkbaltbl="<table border='0' cellpadding='0' cellspacing='0' >";

              $StockInCount=0;
							$StockOutCount=0;
							$Balance=0;
              $rowindex++;

              foreach($StockArray as $StockItem){

                if($StockItem->stock_in>0){

                  $dateValid=TRUE;
									$StockInCount=$StockInCount+$StockItem->stock_in;

									if($StockOutCount>0){

										$stkintbl=$stkintbl."<tr><td align='right'>".$rowindex."--0</td></tr>";
	                  $stkouttbl=$stkouttbl."<tr><td align='right'>".$StockOutCount."</td></tr>";
	                  $stkbaltbl=$stkbaltbl."<tr><td align='right'>".$Balance."</td></tr>";
										$StockOutCount=0;

									}
									$Balance=$StockItem->balance;

                }
								else if($StockItem->stock_out>0){

                  $dateValid=TRUE;
									$StockOutCount=$StockOutCount+$StockItem->stock_out;

									if($StockInCount>0){

										$stkintbl=$stkintbl."<tr><td align='right'>".$StockInCount."</td></tr>";
	                  $stkouttbl=$stkouttbl."<tr><td align='right'>".$rowindex."--0</td></tr>";
	                  $stkbaltbl=$stkbaltbl."<tr><td align='right'>".$Balance."</td></tr>";
										$StockInCount=0;

									}
									$Balance=$StockItem->balance;

                }
              }
							if($StockOutCount>0){

								$stkintbl=$stkintbl."<tr><td align='right'>0</td></tr>";
								$stkouttbl=$stkouttbl."<tr><td align='right'>".$StockOutCount."</td></tr>";
								$stkbaltbl=$stkbaltbl."<tr><td align='right'>".$Balance."</td></tr>";
							}
							else if($StockInCount>0){

								$stkintbl=$stkintbl."<tr><td align='right'>".$StockInCount."</td></tr>";
								$stkouttbl=$stkouttbl."<tr><td align='right'>0</td></tr>";
								$stkbaltbl=$stkbaltbl."<tr><td align='right'>".$Balance."</td></tr>";
							}

              /*
              else if($date==$start || $date==$end){

                $stkintbl=$stkintbl."<tr><td align='right'>0</td></tr>";
								$stkouttbl=$stkouttbl."<tr><td align='right'>0</td></tr>";
								$stkbaltbl=$stkbaltbl."<tr><td align='right'>".$Balance."</td></tr>";
              }
              */
             $strRow=$strRow."<td align='right'>".$stkintbl."</table></td><td align='right'>".$stkouttbl."</table></td><td align='right'>".$stkbaltbl."</table></td>";
           }
            if($dateValid) $output=$output."<tr><td align='left'>".$date."</td>".$strRow."</tr>";
            /* if($dateValid || $date==$start || $date==$end ) $output=$output."<tr><td align='left'>".$date."</td>".$strRow."</tr>"; */
          }
          $output=$output.$strlstrw."</table>";
        }
        $data['output']=$output;
        $this->load->view('header');
        $this->load->view('stockreport',$data);
        $this->load->view('footer');
     }
     else{
       $this->load->view('header');
       $this->load->view('stockreport');
       $this->load->view('footer');
     }

    }else{
        redirect('login', 'refresh');
    }
 }

 function aging(){

   if(($this->session->userdata('logged_in'))&&($this->session->userdata('username')=="admin"||$this->session->userdata('username')=="manager")){

     if(isset($_REQUEST['btnsrch'])){

       $productID=$this->input->post('selItem');

       $AgingArray=$this->Product_model->getItemsByAgingForRpt($productID);

       $product=$this->Product_model->productRowByID($productID);

       $output="<h3><b>ITEM:</b> ".$product->DESCRIP1."</h3>";
       $output=$output."<table cellpadding='5px' cellspacing='5px' border='1' style='width:625px; margin-top:5px; border:1px solid gray; border-collapse:collapse;'>";
       $output=$output."<tr style='background:  #d3d3d3;'><th width='100px'>In Date</th><th width='100px'>DO Number</th><th width='75px'>Quantity</th></tr>";

       foreach($AgingArray as $AgeItem){
             $output=$output."<tr><td align='center'>".date("d-M-Y", strtotime($AgeItem->IN_DATE))."</td><td align='center'>".$AgeItem->DONUMBER."</td><td align='center'>".$AgeItem->QUANTITY."</td></tr>";
       }
       $output=$output."</table>";
       $data['output']=$output;

       $this->load->view('header');
       $this->load->view('aging',$data);
       $this->load->view('footer');
    }
    else{
      $this->load->view('header');
      $this->load->view('aging');
      $this->load->view('footer');
    }

   }else{
       redirect('login', 'refresh');
   }
}

  function stockReportSelBarCode(){

     if(isset($_REQUEST['Barcode'])){

       $barcode=$this->input->post('Barcode');
       $ItemsArray=$this->Product_model->getProductsbyBarCode($barcode);
       $str="<select name='selItem' class='selItem' style='width:350px; height:34px; padding:5px;'>";
       $str=$str."<option value=''>-------------------- Select Item --------------------</option>";

       foreach($ItemsArray as $item){

           $str=$str."<option value='".$item->PRODUCTID."'>".$item->DESCRIP1."</option>";
       }
       $str=$str."</select>";
       echo $str;
     }
  }

  function stockReportSelBarCodeSRpt(){

     if(isset($_REQUEST['Barcode'])){

       $barcode=$this->input->post('Barcode');
       $ItemsArray=$this->Product_model->getProductsbyBarCode($barcode);
       $str="<select name='selItem' class='selItem' style='width:350px; height:34px; padding:5px;'>";
       $str=$str."<option value=''>-------------------- Select Item --------------------</option>";
       $str=$str."<option value='-1'> All Items </option>";
       foreach($ItemsArray as $item){

           $str=$str."<option value='".$item->PRODUCTID."'>".$item->DESCRIP1."</option>";
       }
       $str=$str."</select>";
       echo $str;
     }
  }

  function loadItemsSelBarCode(){

     if(isset($_REQUEST['Barcode'])){

       $barcode=$this->input->post('Barcode');
       $ItemsArray=$this->Product_model->getProductsbyBarCode($barcode);
       $str="<select name='selItem' class='selItem' style='width:350px; height:34px; padding:5px;'>";
       $str=$str."<option value=''>-- Select Item --</option>";

       foreach($ItemsArray as $item){

           $str=$str."<option value='".$item->STOCKID."'>".$item->DESCRIP1."</option>";
       }
       $str=$str."</select>";
       echo $str;
     }
  }

    function updateitem(){

      if($this->session->userdata('logged_in')){

       if(isset($_REQUEST['btnadd'])){

         $currentstk=trim($this->input->post('itemqty'));
         $newQty=$this->input->post('txtnewqty');
         $barcode=$this->input->post('txtbrcd');
         $productid=$this->input->post('txtpdtid');
         $stockid=$this->input->post('txtstkid');

         $doadj=$this->input->post('seldonumadj');
         $donum="";
         $remark="";
         $mdate=trim($this->input->post('frmdate'));;
         if($mdate!="1970/01/01" && $mdate!="") $mdate="(".date("Y/m/d", strtotime($mdate)).")";
         else if($mdate=="1970/01/01") $mdate="";

         if($doadj=='d')
           $donum=trim($this->input->post('txtdo')).$mdate;
         else{
           $remark=trim($this->input->post('txtrmk')).$mdate;

           if($remark=='Type remark here' || $remark=="") $remark="";
           else $remark=": ".$remark;

           if($this->input->post('seladj')=='q')
             $remark="Quantity mismatch".$remark;
           else $remark="Other".$remark;
         }

         date_default_timezone_set('Asia/Singapore');
         $query=$this->Product_model->updateStockAmount($stockid,$newQty);

         $stock_arr  = array (
                     'product_id'=>$productid,
                     'stock_in'=>$newQty,
                     'date'=> date("Y-m-d H:i:s"),
                     'remark'=>$remark,
                     'donumber'=>$donum,
                     'balance'=>$currentstk+$newQty
                 );
         $this->db->insert('cck_stock',$stock_arr);

         $age_arr=array(
           'PRODUCT_ID'=>$productid,
           'STOCK_ID'=>$stockid,
           'IN_DATE'=> date("Y-m-d H:i:s"),
           'DONUMBER'=>$donum,
           'QUANTITY'=>$newQty
         );
         $this->db->insert('cck_age',$age_arr);

         if($query)
          $data['output']="<p align='center' style='color: green;'>".$newQty." items succesfully added.</p>";
         else
          $data['output']="<p align='center' style='color: red;'> Failed.</p>";

         $this->load->view('header');
         $this->load->view('updateitem',$data);
         $this->load->view('footer');
       }
       elseif(isset($_REQUEST['btnde'])){

         $currentstk=trim($this->input->post('itemqty'));
         $newQty=$this->input->post('txtnewqtyde');
         $barcode=$this->input->post('txtbrcd');
         $productid=$this->input->post('txtpdtid');
         $stockid=$this->input->post('txtstkid');

         $doadj=$this->input->post('seldonumadjde');
         $donum="";
         $remark="";
         $mdate=trim($this->input->post('frmdatede'));;
         if($mdate!="1970/01/01" && $mdate!="") $mdate="(".date("Y/m/d", strtotime($mdate)).")";
         else if($mdate=="1970/01/01") $mdate="";

         if($doadj=='d')
           $donum=$this->input->post('txtdode').$mdate;
         else{
           $remark=$this->input->post('txtrmkde').$mdate;

           if($remark=='Type remark here' || $remark=="") $remark="";
           else $remark=": ".$remark;

           if($this->input->post('seladjde')=='q')
             $remark="Quantity mismatch".$remark;
           else $remark="Other".$remark;
         }

         date_default_timezone_set('Asia/Singapore');
         $query=$this->Product_model->updateStockAmount($stockid,-1*$newQty);

         $stock_arr  = array (
                     'product_id'=>$productid,
                     'stock_out'=>$newQty,
                     'date'=> date("Y-m-d H:i:s"),
                     'remark'=>$remark,
                     'donumber'=>$donum,
                     'balance'=>$currentstk-$newQty
                 );
         $this->db->insert('cck_stock',$stock_arr);

         if($query)
          $data['output']="<p align='center' style='color: green;'>".$newQty." items succesfully deducted.</p>";
         else
          $data['output']="<p align='center' style='color: red;'> Failed.</p>";

          $age_query=$this->Product_model->getItemsByAging($stockid);

          foreach($age_query as $itemAge){

              if($itemAge->QUANTITY<$newQty){
                $this->Product_model->updateItemInAge($itemAge->ID,0);
                $newQty=$newQty-$itemAge->QUANTITY;
              }
              else{
                $this->Product_model->updateItemInAge($itemAge->ID,$itemAge->QUANTITY-$newQty);
                break;
              }
          }

         $this->load->view('header');
         $this->load->view('updateitem',$data);
         $this->load->view('footer');
       }
       elseif(isset($_REQUEST['btnsubmit'])){

         $stockid=$this->input->post('selItem');
         $query=$this->Product_model->productByStockID($stockid);
         $prow=$query->row();

         if($query->num_rows() > 0){

           $data['barcode']=$prow->BarCode;
           $data['productid']=$prow->PRODUCTID;
           $data['stockid']=$prow->STOCKID;
           $data['brand']=$prow->BRAND;
           $data['desc']=$prow->DESCRIP1;
           $data['cost']=$prow->COST;
           $data['price']=$prow->POSPrice;
           $data['currentstock']=$prow->current_stock;
           $data['category']=$prow->Category;
           $data['active']=$prow->active;

           $this->load->view('header');
           $this->load->view('updateitem',$data);
           $this->load->view('footer');
         }
         else{
           $this->load->view('header');
           $this->load->view('updateitem');
           $this->load->view('footer');
         }
       }
       else{

         $this->load->view('header');
         $this->load->view('updateitem');
         $this->load->view('footer');
       }
     }else{
         redirect('login', 'refresh');
     }
   }

  	public function index(){

        if($this->session->userdata('logged_in')){

            $this->load->view('header');
            $this->load->view('index');
            $this->load->view('footer');

        }else{
            redirect('login', 'refresh');
        }
  	}

    function destroy(){

        $this->cart->destroy();

        if ($this->session->userdata('numA4') !== FALSE)
          $this->session->unset_userdata('numA4');

        echo 'success';
    }

    function destroyCart(){

        $this->cart->destroy();

        if ($this->session->userdata('numA4') !== FALSE)
          $this->session->unset_userdata('numA4');

        if ($this->session->userdata('sock') !== FALSE)
            $this->session->unset_userdata('sock');

        echo 'success';
    }

    function remove($rowid){

        foreach($this->cart->contents() as $items){
          if($items['stockid']=='SOCK' && $items['rowid']==$rowid){
            if ($this->session->userdata('sock') !== FALSE)
                $this->session->unset_userdata('sock');
          }
        }

        $this->cart->update(array(
           'rowid'=>$rowid,
            'qty'=> 0
        ));

        redirect('pos');
    }

    function checkout(){

        $paymenttype=$this->input->post('paymenttype');
        $voucher=trim($this->input->post('voucher'));
        $adjustment=trim($this->input->post('adjustment'));
        $remark = trim($this->input->post('remark'));
        $invoice="";
        // $firstItemStockID="";
        // $firstItemCat="";

        if($paymenttype=="invoice"){
          $invoice=$voucher;
          $voucher="";
        }

        // foreach($this->cart->contents() as $items){
        //   $firstItemStockID=$items['stockid'];
        //   break;
        // }
        //
        // $firstItemCatArr=$this->Product_model->getCatByStockID($firstItemStockID);
        //
        // foreach($firstItemCatArr as $item){
        //   $firstItemCat=$item->Category;
        // }

        date_default_timezone_set('Asia/Singapore');
        $order = array(
            'order_date'=> date("Y-m-d H:i:s"),
            'voucher'=> $voucher,
            'invoice'=> $invoice,
            'adjustment'=> $adjustment,
            'remark'=>$remark
        );
        $insertorder=$this->Product_model->insertorder($order);

        if($insertorder==true){

            $this->cart->destroy();
            echo "success";
       }
    }

    function changeqtybytextbox(){

        $rowid=$this->input->post('rowid');
        $newqty = trim($this->input->post('newqty'));

        $done=$this->cart->update(array('rowid'=>$rowid,'qty'=>$newqty));

        if($done){
            echo 'changeqty';
        }
    }

//Paper Price for one paper and a ream
    function paperTypeUpdate(){

        $rowid=$this->input->post('rowID');

        if ($this->input->post('type') == 'pc'){

          $this->cart->updateptype(array('rowid'=>$rowid,'paperPcRm'=>'pc'));
          $this->cart->updatepColor(array('rowid'=>$rowid,'paperColor'=>'bw'));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'1s'));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.02));//0.02 is the pc price
        }
        else{

          $this->cart->updateptype(array('rowid'=>$rowid,'paperPcRm'=>'rm'));
          $this->cart->updatepColor(array('rowid'=>$rowid,'paperColor'=>' '));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>' '));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>5));//5 is the ream price
        }

        redirect('pos');
    }

//Paper Price for B&W and 4C
    function paperBWCUpdate(){

        $rowid=$this->input->post('rowID');

        if ($this->input->post('bwc') == 'bw'){

          $this->cart->updatepColor(array('rowid'=>$rowid,'paperColor'=>'bw'));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'1s'));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.02));//0.02 is the 1s BW pc price
        }
        else{

          $this->cart->updatepColor(array('rowid'=>$rowid,'paperColor'=>'4c'));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'1s'));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.30));//0.30 is the 1s 4c pc price
        }

        redirect('pos');
    }

  //Paper Price for 1S and 2S
    function paperSideUpdate(){

        $rowid=$this->input->post('rowID');
        $bwc = $this->input->post('bwc');

        if ($this->input->post('side') == '1s'){

          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'1s'));

          if ($bwc == '4c')
            $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.30));//0.30 is the 1s 4c pc price
          else $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.02));//0.02 is the 1s bw pc price
        }
        else{

          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'2s'));

          if ($bwc == '4c')
            $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.60));//0.60 is the 2s 4c pc price
          else $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.04));//0.04 is the 2s bw pc price
        }

        redirect('pos');
    }

    function paperTypeUpdateA3(){

        $rowid=$this->input->post('rowID');

        if ($this->input->post('type') == 'pc'){

          $this->cart->updateptype(array('rowid'=>$rowid,'paperPcRm'=>'pc'));
          $this->cart->updatepColor(array('rowid'=>$rowid,'paperColor'=>'bw'));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'1s'));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.06));//0.06 is the pc price
        }
        else{

          $this->cart->updateptype(array('rowid'=>$rowid,'paperPcRm'=>'rm'));
          $this->cart->updatepColor(array('rowid'=>$rowid,'paperColor'=>' '));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>' '));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>8.90));//8.90 is the ream price
        }

        redirect('pos');
    }

//A3 Paper Price for B&W and 4C
    function paperBWCUpdateA3(){

        $rowid=$this->input->post('rowID');

        if ($this->input->post('bwc') == 'bw'){

          $this->cart->updatepColor(array('rowid'=>$rowid,'paperColor'=>'bw'));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'1s'));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.06));//0.06 is the 1s BW A3 price
        }
        else{

          $this->cart->updatepColor(array('rowid'=>$rowid,'paperColor'=>'4c'));
          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'1s'));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.60));//0.60 is the 1s 4c A3 price
        }

        redirect('pos');
    }

  //A3 Paper Price for 1S and 2S
    function paperSideUpdateA3(){

        $rowid=$this->input->post('rowID');
        $bwc = $this->input->post('bwc');

        if ($side = $this->input->post('side') == '1s'){

          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'1s'));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));

          if ($bwc == '4c')
            $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.60));//0.60 is the 1s 4c A3 price
          else $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.06));//0.06 is the 1s bw A3 price
        }
        else{

          $this->cart->updatepSide(array('rowid'=>$rowid,'paperSide'=>'2s'));
          $this->cart->update(array('rowid'=>$rowid,'qty'=>1));

          if ($bwc == '4c')
            $this->cart->updatep(array('rowid'=>$rowid,'price'=>1.20));//1.20 is the 2s 4c A3 price
          else $this->cart->updatep(array('rowid'=>$rowid,'price'=>0.12));//0.12 is the 2s bw A3 price
        }

        redirect('pos');
    }

    function sockUpdate(){

        $rowid=$this->input->post('rowID');
        $sock = $this->input->post('sock');

        if ($sock == '1'){

          $this->session->set_userdata('sock','1');
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>2.90));//1 pair price
        }
        else if ($sock == '2'){

          $this->session->set_userdata('sock','2');
          $this->cart->updatep(array('rowid'=>$rowid,'price'=>5.00));//2 pair price
        }

        $this->cart->update(array('rowid'=>$rowid,'qty'=>1));

        redirect('pos');
    }

    function increaseitem($rowid){

        $dataTmp = $this->cart->contents();

        if($dataTmp[$rowid]['qty']<$dataTmp[$rowid]['stock_Qty'])
          $done=$this->cart->update(array('rowid'=>$rowid,'qty'=>$dataTmp[$rowid]['qty'] + 1));
        else
          redirect('pos');

        if($done)
            redirect('pos');
    }

    function decreaseitem($rowid){

        $dataTmp = $this->cart->contents();
        $done=$this->cart->update(array('rowid'=>$rowid,'qty'=>$dataTmp[$rowid]['qty'] - 1));

        if($done){
            redirect('pos');
        }
    }

    function getProduct(){

      $barcodeid=trim($this->input->post('barcodeid'));

      if($barcodeid!=''){

        if($barcodeid=="P+B" || $barcodeid=="p+B" || $barcodeid=="P+b" || $barcodeid=="p+b"){

          $this->getProductWBrCd("PAPERA4");
          $this->getProductWBrCd("BINDA4");
          $this->getProductWBrCd("COLPAPER");
          $this->getProductWBrCd("FASTENER");
          $this->getProductWBrCd("QFILEA4");
        }
        else $this->getProductWBrCd($barcodeid);
      }
    }

    function getProductWBrCd($barcodeid){

        $flag = True;
        $dataTmp = $this->cart->contents();

        $productQuery=$this->Product_model->productList($barcodeid);
        $productRow=$productQuery->row();
        $num = $productQuery->num_rows();

        if($num==1){
          foreach($dataTmp as $item){

              if($item['id']==$productRow->PRODUCTID && ($productRow->BarCode!='PAPERA3' && $productRow->BarCode!='PAPERA4')){

                  $qty = $item['qty'];

                  if($productRow->current_stock>$item['qty'])
                    $qty++;

                  $this->cart->update(array('rowid'=>$item['rowid'],'qty'=>$qty));
                  $flag = false;
                  break;
              }
          }
        }

        if($flag){

            $product_query=$this->Product_model->productList($barcodeid);
            $count=$product_query->num_rows();

            if($count==0){
                echo 'No Items Found.';
            }else{

                $product_row=$product_query->result();
                $k=0;
                foreach($product_row as $item){

                    if($item->current_stock>0) break;
                    $k++;
                }

                $result= $product_row[$k];
                $qty=0;
                if($result->current_stock>0)$qty=1;
                $paperA3A4="";
                $paperPcRm="";
                $paperColor="";
                $paperSide="";

                if($num>1){
                   $tmpid=0;
                 }
                else{
                    if($productRow->BarCode=='PAPERA4'){

                      if($this->session->userdata('numA4') !== FALSE){
                        $tmpid=$result->PRODUCTID."A4-".(1+$this->session->userdata('numA4'));
                        $this->session->set_userdata('numA4',1+$this->session->userdata('numA4'));
                      }
                      else{

                        $this->session->set_userdata('numA4',0);
                        $tmpid=$result->PRODUCTID."A4-0";
                      }
                      $paperPcRm="pc";
                      $paperColor="bw";
                      $paperSide="1s";
                    }
                    else if($productRow->BarCode=='PAPERA3'){

                      if($this->session->userdata('numA3') !== FALSE){
                        $tmpid=$result->PRODUCTID."A3-".(1+$this->session->userdata('numA3'));
                        $this->session->set_userdata('numA3',1+$this->session->userdata('numA3'));
                      }
                      else{

                        $this->session->set_userdata('numA3',0);
                        $tmpid=$result->PRODUCTID."A3-0";
                      }
                      $paperPcRm="pc";
                      $paperColor="bw";
                      $paperSide="1s";
                    }
                    else $tmpid=$result->PRODUCTID;
                 }

                $insert= array(
                    'types'=>$count,
                    'id'=> $tmpid,
                    'name' => 'product_name',
                    'stockid'=> $result->STOCKID,
                    'price' => $result->POSPrice,
                    'qty' => $qty,
                    'stock_Qty' => $result->current_stock,
                    'paperPcRm'=>$paperPcRm,
                    'paperColor'=>$paperColor,
                    'paperSide'=>$paperSide,
                    'options' => array(
                                 'product_BRAND' => $result->BRAND,
                                 'product_DESCRIP1' => $result->DESCRIP1,
                                 'product_BarCode' => $result->BarCode,
                                 ),
                );

                $this->cart->insert($insert);
                echo 'success';
          }
      }
    }

    function logout(){

        $this->session->unset_userdata('logged_in');
        $this->session->unset_userdata('paperType');
        if ($this->session->userdata('sock') !== FALSE)
            $this->session->unset_userdata('sock');
        redirect('login', 'refresh');
    }

    function exportCsv(){

        $query = $this->db->get('cck_products');
        $this->load->helper('xls');
        query_to_xls($query, TRUE, 'Current_stock');

    }

    function exportCsvAging(){

        $query = $this->Product_model->getProductsWithAging();
        $this->load->helper('xls');
        query_to_xls_age($query, TRUE, 'Stock_with_InDates');

    }

    function exportOrder($to,$from){

        $query=$this->Product_model->getOrder($from,$to);
        $this->load->helper('csv');
        query_to_csv($query, TRUE, 'order');
    }
}
