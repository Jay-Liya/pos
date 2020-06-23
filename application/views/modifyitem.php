<script src="<?php echo base_url('assets/js/jquery-1.10.2.js'); ?>"></script>
<!--load jquery ui js file-->
<script src="<?php echo base_url('assets/jquery-ui-1.12.0/jquery-ui.js'); ?>"></script>
<script type="text/javascript">
$(function() {
    $("#frmdate").datepicker();
});
</script>
<script>
   $(document).ready(function(){

      $('.btnsubmit').hide();
      $('.seladj').hide();
      $('#txtrmk').hide();

      $("[name='txtnewqty']").keypress(function(e){

        if(e.which == 13){
          return false;
          }
      });

      $("[name='txtnewqtyde']").keypress(function(e){

        if(e.which == 13){
          return false;
          }
      });

      $('.btnmodify').click(function(){

          if($("[name='txtnewqtyde']").val()!='' && $("[name='txtnewqty']").val()!=''){
            alert('You can not do add and deduct same time.' );
            return false;
          }

          if($("[name='txtnewqty']").val()!=''){

            var newQty = parseInt($("[name='txtnewqty']").val());

            if (!(Number.isInteger(newQty) && newQty > 0)) {
                alert('Positive integer is required!');
                return false;
            }
          }

          if($("[name='txtnewqtyde']").val()!=''){

            var newQty = parseInt($("[name='txtnewqtyde']").val());

            if (!(Number.isInteger(newQty) && newQty > 0)) {
                alert('Positive integer is required!');
                return false;
            }
            else if($('#itemqty').val() < newQty) {
                alert('Deducting quantity cannot be exceed '+$('#itemqty').val());
                return false;
            }
          }

          if(!$('#txtprice').val().match(/^\d+(\.\d+)?$/)){
            alert("Price has to be a positve decimal number.");
            return false;
          }

          var answer = confirm('Are you sure you want to modify the item');
          return answer
      });

      $('.selBarcode').change(function(){

          $('.btnsubmit').show();
          $('#tblItemDetails').hide();
          var selectValue = $(this).val();

          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/loadItemsSelBarCode'); ?>",
              data: { Barcode: selectValue}
          }).done(function(msg) {
            $('#displayItemDropInner').html(msg);
          });
      });

      $('#seldonumadj').change(function(){

        if($(this).val()=='a'){

          $('#txtdo').hide();
          $('#txtrmk').show();
          $('#seladj').show();
        }
        else{

          $('#txtdo').show();
          $('#txtrmk').hide();
          $('#seladj').hide();
        }

      });

      $('.btnsubmit').onclick(function(){

          $('#tblItemDetails').show();
      });
    });
</script>

<?php echo form_open('pos/modifyitem'); ?>
  <div>
      <table cellpadding="5" cellspacing="5" border="0" style="width:100%; border:1px solid #D0D0D0; background:  #d3d3d3;">
        <tr>
            <td colspan="10" align="center"><h3>Item Modify</h3></td>
        </tr>
        <tr>
            <td style="width:50%; padding:5px;">
                <select name="selBarcode" class="selBarcode" style="width:300px; height:34px; padding:5px;">
                    <option value="">-- Select Barcode --</option>
                    <?php
                    $itemsArray=$this->Product_model->getOrderedBarcodes();
                    foreach($itemsArray AS $item){ ?>
                                <option value="<?php  echo $item->BarCode; ?>" ><?php  echo $item->BarCode; ?></option>
                    <?php }?>
                </select>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/modifyitem'), img(array('src'=>'assets/images/refresh.png','style'=>'width:40px; height:40px;')),array('title'=>'Refresh','class'=>'btnStock')) ; ?>
            </td>
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
                <?php echo anchor( site_url('pos/updatestk'), img(array('src'=>'assets/images/stock_update.png','style'=>'width:40px; height:40px;')),array('title'=>'Stock update','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/updateitem'), img(array('src'=>'assets/images/item_update.png','style'=>'width:40px; height:40px;')),array('title'=>'Add Item quantity','class'=>'btnStock')) ; ?>
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
      <div id="displayItemDropOuter" >
        <table cellpadding="5" cellspacing="5" border="0" style="width:100%; background:  #d3d3d3;">
          <tr>
            <td >
              <div id='displayItemDropInner' ></div></br>
               <?php echo form_submit(array('name'=>'btnsubmit','class'=>'btnsubmit','value'=>'Search item','style'=>'width:100px; height:30px; padding:1px;')); ?>
            </td>
          </tr>
        </table>
      </div>

<?php echo form_close();
    if(@$barcode){?>
      <table id="tblItemDetails" cellpadding="10" cellspacing="10" border="1" style=" border-collapse: collapse; border:1px solid gray; width:100%;">
          <tr >
              <td style="width:150px;background:#D0D0D0;"><b>BARCODE</b></td>
              <td><input id="txtbrcd" name="txtbrcd" style="width:200px;" value="<?php echo $barcode;?>"  ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>STOCK ID</b></td>
              <td><input id="txtstkid" name="txtstkid" style="border:0;background-color:white;" value="<?php echo $stockid;?>"  readonly>
                  <input  type="hidden" id="txtpdtid" name="txtpdtid" value="<?php echo $productid;?>" >
              </td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>BRAND</b></td>
              <td><input id="txtbrand" name="txtbrand"  value="<?php echo $brand;?>" ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>DESCRIPTION</b></td>
              <td><input id="txtdesc" name="txtdesc" style="width:350px;"  value="<?php echo $desc;?>" ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>COST</b></td>
              <td><input id="txtcost" name="txtcost" style="width:70px;"  value="<?php echo $cost;?>" ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>PRICE</b></td>
              <td><input id="txtprice" name="txtprice" style="width:70px;" value="<?php echo $price;?>" ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>CATEGORY</b></td>
              <td>
                <select name="selcat" class="selcat" style="width:60px; height:30px; padding:2px;">
                    <?php
                          $car_arr=$this->Product_model->getGroupedCat();
                          foreach($car_arr AS $cat){
                            if($cat->Category!=""){
                              if($cat->Category==$category){ ?>
                                <option value="<?php  echo $cat->Category; ?>" selected><?php  echo $cat->Category; ?></option>
                    <?php     }else{ ?>
                              <option value="<?php  echo $cat->Category; ?>" ><?php  echo $cat->Category; ?></option>
                    <?php           }
                              }
                          } ?>
                </select>
              </td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;" ><b>CURRENT STOCK</b></td>
              <td >
                <table>
                  <tr>
                    <td>
                      <?php echo $currentstock;?> +
                      <?php echo form_input(array('name'=>'txtnewqty','style'=>'text-align:center; width:50px; height:25px; margin:0 5px;','maxlength'=>'7'));?>
                    </td>
                    <td rowspan="2">
                      <table>
                        <tr>
                          <td>
                      <select name="seldonumadj" class="seldonumadj" id="seldonumadj" style="margin-bottom:7px;width:100px; height:25px; padding:2px;">
                          <option value="d">DO Number</option>
                          <option value="a">Adjustment</option>
                      </select></br>
                      <?php date_default_timezone_set('Asia/Singapore');
                            $attributes = 'id="frmdate" style="width:70px;" placeholder="'.date("m/d/Y").'"';
                            echo form_input('frmdate', set_value('frmdate'), $attributes); ?>
                          </td><td>
                      <input id="txtdo" name="txtdo" style="width:150px;" placeholder="Type Do Number here" >
                      <select name="seladj" class="seladj" id="seladj" style="width:150px; height:25px; padding:2px;">
                          <option value="q">Quantity mismatch</option>
                          <option value="o">Other</option>
                      </select>
                          </td><td>
                      <input id="txtrmk" name="txtrmk" style="width:200px;" placeholder="Type remark here" >
                          </td>
                        </tr>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <input type="hidden" id="itemqty" name="itemqty" value="<?php echo $currentstock; ?>">
                      <?php echo $currentstock." - ".form_input(array('name'=>'txtnewqtyde','style'=>'text-align:center; width:50px; height:25px; margin:0 5px 0 8px;','maxlength'=>'7'));
                      ?>
                    </td>
                  </tr>
                  <tr >
                      <td></td>
                      <td><input id="txtstatus" name="txtstatus" style="width:30px;" value="<?php echo $active;?>" type="hidden"></td>
                  </tr>
                </table>
              </td>
          </tr>
          <tr >
              <td ></td>
              <td ><?php echo form_submit(array('name'=>'btnmodify','class'=>'btnmodify','value'=>'Modify','style'=>'width:50px; height:30px; padding:3px;'));?></td>
          </tr>
      </table>
<?php  }
        else if(@$output)
          echo $output; ?>
