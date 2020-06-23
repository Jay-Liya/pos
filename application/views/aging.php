<script>
   $(document).ready(function(){
     $('.btnsrch').hide();
      $(".btnsrch").click(function(){

          if($(".selBarcode").val()==''){
              alert('Please select the Barcode.');
              return false;
          }

          if($(".selItem").val()==''){
              alert('Please select the Item.');
              return false;
          }

          $('#printbtn').show();
      });

      $('.selBarcode').change(function(){

          $('#displayRecords').html('');
          $('#printbtn').hide();
          $('.btnsrch').hide();


          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/stockReportSelBarCode'); ?>",
              data: { Barcode: $(this).val() }
          }).done(function(msg) {
            $('#displayItemDrop').html(msg);
            $('.btnsrch').show();
          });
      });

      $("#printbtn").click(function () {

        var printContents = $("#displayRecords").html();
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
      });

    });

</script>

<?php echo form_open('pos/aging');  ?>
  <div>
      <table cellpadding="5" cellspacing="5" border="0" style="width:100%; background:  #d3d3d3;">
        <tr>
            <td colspan="11" align="center"><h3>Stock Aging Report</h3></td>
        </tr>
        <tr>
            <td style="width:400px; padding:5px;">
                <select name="selBarcode" class="selBarcode" style="width:300px; height:34px; padding:5px;">
                    <option value="">-- Select Barcode --</option>
                    <?php
                    $itemsArray=$this->Product_model->getOrderedBarcodes();
                    foreach($itemsArray AS $item){ ?>
                                <option value="<?php  echo $item->BarCode; ?>" ><?php  echo $item->BarCode; ?></option>
                    <?php }?>
                </select>
            </td>
            <td width="50px" align="left">
                <?php echo anchor( site_url('pos/aging'), img(array('src'=>'assets/images/refresh.png','style'=>'width:35px; height:35px;')),array('title'=>'Refresh','class'=>'btnStock')) ; ?>
            </td>
            <td align="center"><?php echo anchor(site_url('pos/exportCsvAging'),img(array('src'=>'assets/images/csv.png','style'=>'width:40px; height:40px;','title'=>'Download Excel file'))) ?></td>
            <?php if(($this->session->userdata('logged_in'))&&($this->session->userdata('username')=="admin"||$this->session->userdata('username')=="manager")){?>
              <td width="50px" align="center">
                  <?php echo anchor( site_url('pos/stock'), img(array('src'=>'assets/images/stock.png','style'=>'width:35px; height:35px;')),array('title'=>'Current stock','class'=>'btnStock')) ; ?>
              </td>
              <td width="50px" align="center">
                  <?php echo anchor( site_url('pos/stockreport'), img(array('src'=>'assets/images/stockreport.png','style'=>'width:35px; height:35px;')),array('title'=>'Closing Stock Report','class'=>'btnStock')) ; ?>
              </td>
              <td width="50px" align="center">
                  <?php echo anchor( site_url('pos/modifyitem'), img(array('src'=>'assets/images/modify.png','style'=>'width:35px; height:35px;')),array('title'=>'Modify Item','class'=>'btnStock')) ; ?>
              </td>
            <?php } else { ?>
              <td width="50px" align="center">&nbsp;</td>
              <td width="50px" align="center">&nbsp;</td>
              <td width="50px" align="center">&nbsp;</td>
            <?php } ?>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/updatestk'), img(array('src'=>'assets/images/stock_update.png','style'=>'width:35px; height:35px;')),array('title'=>'Stock update','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/updateitem'), img(array('src'=>'assets/images/item_update.png','style'=>'width:35px; height:35px;')),array('title'=>'Add Item quantity','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/newitem'), img(array('src'=>'assets/images/newitem.png','style'=>'width:35px; height:35px;')),array('title'=>'Add New Item','class'=>'btnnewitem')) ; ?>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos'), img(array('src'=>'assets/images/home.png','style'=>'width:35px; height:35px;')),array('title'=>'Selling','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="right">
                <?php echo anchor( site_url('pos/logout'), img(array('src'=>'assets/images/logout.png','style'=>'width:35px; height:35px;')),array('title'=>'Log Out Now','class'=>'btnLogout')) ; ?>
            </td>
        </tr>
      </table>

      <table cellpadding="5" cellspacing="5" border="0" style="width:100%; background:  #d3d3d3;">
        <tr>
          <td width="400px"><div id='displayItemDrop' ></div></td>
          <td width="100px"><?php echo form_submit(array('name'=>'btnsrch','class'=>'btnsrch','value'=>'Search','style'=>'width:75px; height:30px; padding:3px;align:left;'));?></td>
          <td ></td >
        </tr>
      </table>

<?php echo form_close(); ?>
    <?php if(@$output){ ?>
    </br><center><input type="button" id="printbtn" value="Print Report" style="width:100px;" /><center>
        <div id='displayRecords' align='center'><?php echo $output; ?></div>
    <?php }  ?>
