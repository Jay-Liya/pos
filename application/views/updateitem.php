<script src="<?php echo base_url('assets/js/jquery-1.10.2.js'); ?>"></script>
<!--load jquery ui js file-->
<script src="<?php echo base_url('assets/jquery-ui-1.12.0/jquery-ui.js'); ?>"></script>

<script type="text/javascript">

    $(function() {
        $("#frmdate").datepicker();
        $("#frmdatede").datepicker();
    });

   $(document).ready(function(){

      $('.selBarcode').hide();
      $('.txtbarcode').hide();
      $('.btnsubmit').hide();
      $('.seladj').hide();
      $('#txtrmk').hide();
      $('.seladjde').hide();
      $('#txtrmkde').hide();

      $(".txtbarcode").keypress(function(e){

        if(e.which == 13){
          return false;
          }
      });

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

      $('.btnadd').click(function(){

          if($("[name='txtnewqty']").val()==' '){
            alert('Enter the quantity.' );
            return false;
          }

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

          var answer = confirm('Are you sure you want to add items?');
          return answer
      });


      $('.btnde').click(function(){

          if($("[name='txtnewqtyde']").val()==' '){
            alert('Enter the quantity.' );
            return false;
          }

          if($("[name='txtnewqtyde']").val()!='' && $("[name='txtnewqty']").val()!=''){
            alert('You can not do add and deduct same time.' );
            return false;
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

          var answer = confirm('Are you sure you want to deduct items?');
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
            $('.selItem').show();
            $('#displayItemDropInner').html(msg);
          });
      });

      $('input[type=radio][name=brcodetype]').change(function() {

          $('#tblItemDetails').hide();
          $('.btnsubmit').hide();
          $('.selItem').hide();

          if (this.value == 'scan') {
              $('.selBarcode').hide();
              $('.txtbarcode').show();
              $('.txtbarcode').val('');
              $('.txtbarcode').focus();
          }
          else if (this.value == 'drop') {
              $('.selBarcode').show();
              $('.txtbarcode').hide();
          }
      });

      $('.txtbarcode').keyup(function () {
      //$('.txtbarcode').keyup(function () {
        $('.btnsubmit').show();

         $.ajax({
               type: "POST",
               url: "<?php echo site_url('pos/loadItemsSelBarCode'); ?>",
               data: { Barcode: $(this).val()}
         }).done(function(msg) {
              $('.selItem').show();
              $('#displayItemDropInner').html(msg);
              //$('.txtbarcode').val('');
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

      $('#seldonumadjde').change(function(){

        if($(this).val()=='a'){

          $('#txtdode').hide();
          $('#txtrmkde').show();
          $('#seladjde').show();
        }
        else{

          $('#txtdode').show();
          $('#txtrmkde').hide();
          $('#seladjde').hide();
        }

      });

      $('.selItem').change(function(){

          $('#tblItemDetails').hide();
      });

      $('.btnsubmit').onclick(function(){

          $('#tblItemDetails').show();
      });



    });

</script>

<?php echo form_open('pos/updateitem');  ?>
  <div>
      <table cellpadding="5" cellspacing="5" border="0" style="width:100%; background:  #d3d3d3;">
        <tr>
            <td colspan="10" align="center"><h3>Add Item Quantity</h3></td>
        </tr>
        <tr>
            <td colspan="2" style="width:40%; padding:5px;">
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/updateitem'), img(array('src'=>'assets/images/refresh.png','style'=>'width:40px; height:40px;')),array('title'=>'Refresh','class'=>'btnStock')) ; ?>
            </td>
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
            <?php } else { ?>
              <td width="50px" align="center">&nbsp;</td>
              <td width="50px" align="center">&nbsp;</td>
              <td width="50px" align="center">&nbsp;</td>
              <td width="50px" align="center">&nbsp;</td>
            <?php } ?>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/updatestk'), img(array('src'=>'assets/images/stock_update.png','style'=>'width:40px; height:40px;')),array('title'=>'Stock update','class'=>'btnStock')) ; ?>
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
        <tr>
            <td align="left">
              <input type="radio" name="brcodetype" value="scan" >Scanned Barcode</br>
              <input type="radio" name="brcodetype" value="drop">Barcodes Drop Down
            </td>
            <td align="left">
              <select name="selBarcode" class="selBarcode" style="width:300px; height:34px; padding:5px;">
                  <option value="">-- Select Barcode --</option>
                  <?php
                  $itemsArray=$this->Product_model->getOrderedBarcodes();
                  foreach($itemsArray AS $item){ ?>
                              <option value="<?php  echo $item->BarCode; ?>" ><?php  echo $item->BarCode; ?></option>
                  <?php }?>
              </select>
              <input name="txtbarcode" class="txtbarcode" autocomplete="off"  >
            </td>
            <td colspan="8" align="left"></td>
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
<?php
    echo form_close();

    if(@$barcode){?>
      <table id="tblItemDetails" cellpadding="10" cellspacing="10" border="1" style=" border-collapse: collapse; border:1px solid gray; width:100%;">
          <tr >
              <td style="width:150px;background:#D0D0D0;"><b>BARCODE</b></td>
              <td><input id="txtbrcd" name="txtbrcd" style="border:0;background-color:white;" value="<?php echo $barcode;?>"  readonly ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>STOCK ID</b></td>
              <td><input id="txtstkid" name="txtstkid" style="border:0;background-color:white;" value="<?php echo $stockid;?>"  readonly>
                <input  type="hidden" id="txtpdtid" name="txtpdtid" value="<?php echo $productid;?>" >
              </td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>BRAND</b></td>
              <td ><?php echo $brand;?></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>DESCRIPTION</b></td>
              <td><?php echo $desc;?></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>PRICE</b></td>
              <td><?php echo $price;?></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>CATEGORY</b></td>
              <td><?php echo $category;?></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;" rowspan="2"><b>CURRENT STOCK</b></td>
              <td>
                <table>
                  <tr>
                    <td>
                      <?php echo $currentstock." + "; ?>
                      <?php echo form_input(array('name'=>'txtnewqty','style'=>'text-align:center; width:50px; height:25px; margin:0 5px;','maxlength'=>'7'));?>
                      </td>
                      <td>
                        <?php
                          echo form_submit(array('name'=>'btnadd','class'=>'btnadd','value'=>'Add','style'=>'width:50px; height:30px; padding:3px;margin-right:10px;'));
                        //  echo "Remark: ".form_input(array('name'=>'txtremarkAdd','style'=>'text-align:left; width:300px; height:25px; margin:0 5px;','maxlength'=>'40'));
                        ?>
                    </td>
                    <td>
                <select name="seldonumadj" class="seldonumadj" id="seldonumadj"  style="width:100px; height:25px; padding:2px;">
                    <option value="d">DO Number</option>
                    <option value="a">Adjustment</option>
                </select></br>
                <?php date_default_timezone_set('Asia/Singapore');
                      $attributes = 'id="frmdate" style="width:70px;margin-top:5px;" placeholder="'.date("m/d/Y").'"';
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
          <tr >
              <td>
                <table>
                  <tr>
                    <td>
                      <input type="hidden" id="itemqty" name="itemqty" value="<?php echo $currentstock; ?>">
                      <?php echo $currentstock." - "; ?>
                      <?php echo form_input(array('name'=>'txtnewqtyde','style'=>'text-align:center; width:50px; height:25px; margin:0 5px 0 8px;','maxlength'=>'7'));?>
                      </td><td>
                      <?php
                        echo form_submit(array('name'=>'btnde','class'=>'btnde','value'=>'Deduct','style'=>'width:50px; height:30px; padding:1px;margin-right:10px;'));
                      ?>
                    </td>
                    <td>
                <select name="seldonumadjde" class="seldonumadjde" id="seldonumadjde"  style="width:100px; height:25px; padding:2px;">
                    <option value="d">DO Number</option>
                    <option value="a">Adjustment</option>
                </select></br>
                <?php date_default_timezone_set('Asia/Singapore');
                      $attributes = 'id="frmdatede" style="width:70px;margin-top:5px;" placeholder="'.date("m/d/Y").'"';
                      echo form_input('frmdatede', set_value('frmdatede'), $attributes); ?>
                    </td><td>
                <input id="txtdode" name="txtdode" style="width:150px;" placeholder="Type Do Number here" >
                <select name="seladjde" class="seladjde" id="seladjde" style="width:150px; height:25px; padding:2px;">
                    <option value="q">Quantity mismatch</option>
                    <option value="o">Other</option>
                </select>
                    </td><td>
                <input id="txtrmkde" name="txtrmkde" style="width:200px;" placeholder="Type remark here" >
                    </td>
                  </tr>
                </table>
              </td>
          </tr>
      </table>
<?php  }
        else if(@$output)
          echo $output; ?>
