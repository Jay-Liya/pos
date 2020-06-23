<!DOCTYPE html>
<html>
<head>
<style>
body {
	font-family: Arial;
	width: 94%;
}

.outer-container {
	background: #F0F0F0;
	border: #e0dfdf 1px solid;
	padding: 40px 20px;
	border-radius: 2px;
}

.btn-submit {
	background: #333;
	border: #1d1d1d 1px solid;
    border-radius: 2px;
	color: #f0f0f0;
	cursor: pointer;
    padding: 5px 20px;
    font-size:0.9em;
}

.tutorial-table {
    margin-top: 40px;
    font-size: 0.8em;
	border-collapse: collapse;
	width: 100%;
}

.tutorial-table th {
    background: #f0f0f0;
    border-bottom: 1px solid #dddddd;
	padding: 8px;
	text-align: left;
}

.tutorial-table td {
    background: #FFF;
	border-bottom: 1px solid #dddddd;
	padding: 8px;
	text-align: left;
}

#response {
    padding: 10px;
    margin-top: 10px;
    border-radius: 2px;
    display:none;
}

.success {
    background: #c7efd9;
    border: #bbe2cd 1px solid;
}

.error {
    background: #fbcfcf;
    border: #f3c6c7 1px solid;
}

div#response.display-block {
    display: block;
}
</style>
<script>
	function myFunction() {
		if($('#txtdo').val()==""){
			alert("Please type the DO number");
			event.preventDefault();
			return false;
		 }
	}
</script>
</head>

<body>
    <form action="" method="post"
        name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
        <table cellpadding="5" cellspacing="5" border="0" style="width:100%; border:1px solid #D0D0D0; background:  #d3d3d3;">
          <tr>
              <td colspan="3" align="center"><h3>Upload Items By Excel File<h3></td>
          </tr>
          <tr>
              <td style="width:30%;">
                <input type="file" name="file" id="file" accept=".xls,.xlsx">
              </td>
							<td width="50px" align="center">
									<?php echo anchor( site_url('pos/updatestk'), img(array('src'=>'assets/images/refresh.png','style'=>'width:40px; height:40px;')),array('title'=>'Refresh','class'=>'btnStock')) ; ?>
							</td>
              <td align="right">
                  <table style="width:90%;">
                      <tr>
                        <?php if(($this->session->userdata('logged_in'))&&($this->session->userdata('username')=="admin"||$this->session->userdata('username')=="manager")){?>
													<td width="50px" align="center">
						                  <?php echo anchor( site_url('pos/aging'), img(array('src'=>'assets/images/aging.png','style'=>'width:40px; height:40px;')),array('title'=>'Stock Aging Report','class'=>'btnStock')) ; ?>
						              </td>
													<td width="50px" align="center">
						                  <?php echo anchor( site_url('pos/stockreport'), img(array('src'=>'assets/images/stockreport.png','style'=>'width:40px; height:40px;')),array('title'=>'Closing Stock Report','class'=>'btnStock')) ; ?>
						              </td>
													<td width="50px" align="center">
                              <?php echo anchor( site_url('pos/stock'), img(array('src'=>'assets/images/stock.png','style'=>'width:40px; height:40px;')),array('title'=>'Current stock','class'=>'btnStock')) ; ?>
                          </td>
                          <td width="50px" align="center">
                              <?php echo anchor( site_url('pos/modifyitem'), img(array('src'=>'assets/images/modify.png','style'=>'width:40px; height:40px;')),array('title'=>'Modify Item','class'=>'btnStock')) ; ?>
                          </td>
                        <?php }?>
                        <td width="50px" align="center">
                            <?php echo anchor( site_url('pos/updateitem'), img(array('src'=>'assets/images/item_update.png','style'=>'width:40px; height:40px;')),array('title'=>'Add Item Quantity','class'=>'btnStock')) ; ?>
                        </td>
												<td width="50px" align="center">
						                <?php echo anchor( site_url('pos/newitem'), img(array('src'=>'assets/images/newitem.png','style'=>'width:40px; height:40px;')),array('title'=>'Add New Item','class'=>'btnnewitem')) ; ?>
						            </td>
                        <td width="50px" align="center">
                            <?php echo anchor( site_url('pos'), img(array('src'=>'assets/images/home.png','style'=>'width:40px; height:40px;')),array('title'=>'Selling','class'=>'btnStock')) ; ?>
                        </td>
                        <td width="50px" align="right">
                            <?php echo anchor( site_url('pos/logout'), img(array('src'=>'assets/images/logout.png','style'=>'width:40px; height:40px;')),array('title'=>'Log Out Now','class'=>'btnLogout')) ; ?>
                        </td>
                      </tr>
                  </table>
              </td>
          </tr>
					<tr>
						<td style="background:#D0D0D0;"><b>DO Number: </b><input id="txtdo" name="txtdo" style="width:150px;" >
							<?php date_default_timezone_set('Asia/Singapore');
										$attributes = 'id="frmdate" style="width:70px;" placeholder="'.date("m/d/Y").'"';
			              echo form_input('frmdate', set_value('frmdate'), $attributes); ?>
						</td>
						<td align="left">
							<button type="submit" id="submit" name="import" class="btn-submit" onclick="myFunction()" >Add</button>
						</td>
						<td  align="center"></td>
          </tr>
      </table>
    </form>

    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>

<?php
if (isset($_POST["import"]))
{
  $allowedFileType = array('application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

  if(in_array($_FILES["file"]["type"],$allowedFileType)){

        $targetPath = 'uploads/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

        $Reader = new SpreadsheetReader($targetPath);

        $sheetCount = count($Reader->sheets());

        echo '</br><table cellpadding="5" border="1">';
        echo '<tr bgcolor="#DCDCDC">
                    <td>No</td>
                    <td>Brand</td>
                    <td>StockID</td>
                    <td>Description</td>
                    <td>Cost</td>
                    <td>Price</td>
                    <td>Barcode</td>
                    <td>Qty</td>
                    <td>Category</td>
                    <td>Status</td>
                </tr>';

        $n=0;
        $add_num = 0;
        $update_num = 0 ;

        for($i=0;$i<$sheetCount;$i++)
        {
            $Reader->ChangeSheet($i);

            foreach ($Reader as $Row)
            {
                $n++;

                if($n>1&&(isset($Row[1])&&$Row[1]!="")) {

                  $stockid=trim($Row[1]);
                  $qty = trim($Row[6]);

                  $query=$this->Product_model->productByStockID($stockid);
                  $prow=$query->row();
                  date_default_timezone_set('Asia/Singapore');
									$do=trim($_POST["txtdo"]);
									if(isset($_POST["frmdate"]) && $_POST["frmdate"]!="1970/01/01" && $_POST["frmdate"]!="") $do=$do."(".date("Y/m/d", strtotime($_POST["frmdate"])).")";

                  if($query->num_rows()> 0){
                      $update_num++;
                      $this->Product_model->updateStockAmount($stockid,$qty);

                      $stock_arr  = array (
                                  'product_id'=>$prow->PRODUCTID,
                                  'stock_in'=>$qty,
                                  'date'=> date("Y-m-d H:i:s"),
																	'donumber'=>$do,
						                      'balance'=>$qty+$prow->current_stock
                              );
                      $this->db->insert('cck_stock',$stock_arr);

											$age_arr=array(
												'PRODUCT_ID'=>$prow->PRODUCTID,
												'STOCK_ID'=>$stockid,
												'IN_DATE'=> date("Y-m-d H:i:s"),
												'DONUMBER'=>$do,
												'QUANTITY'=>$qty
											);
											$this->db->insert('cck_age',$age_arr);

                      echo '<tr><td>'.($n-1).'</td><td>'.$Row[0].'</td><td>'.$Row[1].'</td><td>'.$Row[2].'</td><td>'.$Row[3].'</td><td>'.$Row[4].'</td><td>'.$Row[5].'</td><td>'.$Row[6].'</td><td>'.$Row[7].'</td><td>Updated</td></tr>';
                  }else{
                      $add_num++;
                      $pro_arr  = array (
                                  'STOCKID'=>$stockid,
																	'BRAND'=>str_replace("'","",trim($Row[0])),
                                  'DESCRIP1'=>str_replace("'","",trim($Row[2])),
                                  'COST'=>str_replace("'","",trim($Row[3])),
                                  'POSPrice'=>str_replace("'","",trim($Row[4])),
                                  'BarCode'=>str_replace("'","",trim($Row[5])),
                                  'current_stock'=>str_replace("'","",trim($Row[6])),
                                  'Category'=>strtoupper(str_replace("'","",trim($Row[7])))
                              );
                      $this->db->insert('cck_products',$pro_arr);
                      $in_id=$this->db->insert_id();

                      $stock_arr  = array (
                                  'product_id'=>$in_id,
                                  'stock_in'=>$qty,
                                  'date'=> date("Y-m-d H:i:s"),
																	'donumber'=>$do,
						                      'balance'=>$qty
                              );
                      $this->db->insert('cck_stock',$stock_arr);

											$age_arr=array(
												'PRODUCT_ID'=>$in_id,
												'STOCK_ID'=>$stockid,
												'IN_DATE'=> date("Y-m-d H:i:s"),
												'DONUMBER'=>$do,
												'QUANTITY'=>$qty
											);
											$this->db->insert('cck_age',$age_arr);

                      echo '<tr><td>'.($n-1).'</td><td>'.$Row[0].'</td><td>'.$Row[1].'</td><td>'.$Row[2].'</td><td>'.$Row[3].'</td><td>'.$Row[4].'</td><td>'.$Row[5].'</td><td>'.$Row[6].'</td><td>'.$Row[7].'</td><td>Added</td></tr>';
                  }
                }
            }
          }
          echo '</table></br>';
          echo $add_num.' new item types added.<br/>';
          echo $update_num.' existing item types updated.<br/>';
  }
  else
  {
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
  }
}
?>
<script src="<?php echo base_url('assets/js/jquery-1.10.2.js'); ?>"></script>
<!--load jquery ui js file-->
<script src="<?php echo base_url('assets/jquery-ui-1.12.0/jquery-ui.js'); ?>"></script>
<script type="text/javascript">
$(function() {
		$("#frmdate").datepicker();
});
</script>
</body>
</html>
