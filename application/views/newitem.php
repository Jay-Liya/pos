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

      $('#txtstkid').focus();

      $('.btnnew').click(function(){

          if($('#txtstkid').val()==''){
            alert('Please type a STOCK ID.' );
            return false;
          }
          if($('#txtqty').val()!='' && $('#txtdo').val()==''){
            alert('Please type the DO Number when you add new items.' );
            return false;
          }

          if(!$('#txtcost').val().match(/^\d+(\.\d+)?$/)){
            alert("COST has to be a positve decimal number.");
            return false;
          }

          if(!$('#txtprice').val().match(/^\d+(\.\d+)?$/)){
            alert("Price has to be a positve decimal number.");
            return false;
          }

          var answer = confirm('Are you sure you want to add this new item');
          return answer
      });

    });

</script>

<?php echo form_open('pos/newitem'); ?>
  <div>
      <table cellpadding="5" cellspacing="5" border="0" style="width:100%; border:1px solid #D0D0D0; background:  #d3d3d3;">
        <tr>
            <td colspan="9" align="center"><h3>Adding New Item</h3></td>
        </tr>
        <tr>
            <td width="40%" align="center"></td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/newitem'), img(array('src'=>'assets/images/refresh.png','style'=>'width:40px; height:40px;')),array('title'=>'Refresh','class'=>'btnStock')) ; ?>
            </td>
            <?php if(($this->session->userdata('logged_in'))&&($this->session->userdata('username')=="admin"||$this->session->userdata('username')=="manager")){?>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/aging'), img(array('src'=>'assets/images/aging.png','style'=>'width:40px; height:40px;')),array('title'=>'Stock Aging Report','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/stockreport'), img(array('src'=>'assets/images/stockreport.png','style'=>'width:40px; height:40px;')),array('title'=>'Closing Stock Report','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/modifyitem'), img(array('src'=>'assets/images/modify.png','style'=>'width:40px; height:40px;')),array('title'=>'Modify Item','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/stock'), img(array('src'=>'assets/images/stock.png','style'=>'width:40px; height:40px;')),array('title'=>'Current stock','class'=>'btnStock')) ; ?>
            </td>
          <?php } else{?>
            <td width="50px" align="center"></td>
            <td width="50px" align="center"></td>
            <td width="50px" align="center"></td>
            <td width="50px" align="center"></td>
            <?php } ?>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/updatestk'), img(array('src'=>'assets/images/stock_update.png','style'=>'width:40px; height:40px;')),array('title'=>'Stock update','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos/updateitem'), img(array('src'=>'assets/images/item_update.png','style'=>'width:40px; height:40px;')),array('title'=>'Add Item quantity','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="center">
                <?php echo anchor( site_url('pos'), img(array('src'=>'assets/images/home.png','style'=>'width:40px; height:40px;')),array('title'=>'Selling','class'=>'btnStock')) ; ?>
            </td>
            <td width="50px" align="right">
                <?php echo anchor( site_url('pos/logout'), img(array('src'=>'assets/images/logout.png','style'=>'width:40px; height:40px;')),array('title'=>'Log Out Now','class'=>'btnLogout')) ; ?>
            </td>
        </tr>
      </table>

      <table cellpadding="10" cellspacing="10" border="1" style=" border-collapse: collapse; border:1px solid gray; width:100%;">
          <tr >
              <td style="background:#D0D0D0;"><b>STOCK ID</b></td>
              <td><input id="txtstkid" name="txtstkid" style="height:20px;width:200px;"   ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>BRAND</b></td>
              <td><input id="txtbrand" name="txtbrand" style="height:20px;width:250px;"  ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>DESCRIPTION</b></td>
              <td><input id="txtdesc" name="txtdesc" style="height:20px;width:350px;"   ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>COST</b></td>
              <td><input id="txtcost" name="txtcost" style="height:20px;width:70px;"   ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>PRICE</b></td>
              <td><input id="txtprice" name="txtprice" style="height:20px;width:70px;"  ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>BARCODE</b></td>
              <td><input id="txtbrcd" name="txtbrcd" style="height:20px;width:200px;"  ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>CATEGORY</b></td>
              <td>
                <select name="selcat" class="selcat" style="width:150px; height:30px; padding:2px;">
                    <option value="">Select Category</option>
                    <?php $cat_arr=$this->Product_model->getGroupedCat();
                          foreach($cat_arr AS $cat){
                            if($cat->Category!=""){
                    ?>
                              <option value="<?php  echo $cat->Category; ?>" ><?php  echo $cat->Category; ?></option>
                    <?php   }
                          }
                    ?>
                </select>
              </td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;" ><b>NUMBER OF ITEMS</b></td>
              <td>
                <input id="txtqty" name="txtqty" style="height:20px;width:60px;" >
              </td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;" ><b>DO NUMBER</b></td>
              <td>
                <input id="txtdo" name="txtdo" style="height:20px;width:200px;" >
                <?php date_default_timezone_set('Asia/Singapore');
                      $attributes = 'id="frmdate" style="width:70px;" placeholder="'.date("m/d/Y").'"';
                      echo form_input('frmdate', set_value('frmdate'), $attributes); ?>
              </td>
          </tr>
          <tr >
              <td ></td>
              <td ><?php echo form_submit(array('name'=>'btnnew','class'=>'btnnew','value'=>'Add','style'=>'width:50px; height:30px; padding:3px;'));?></td>
          </tr>
      </table>
    </div>
      <?php
            echo form_close();
            if(@$output) {
              echo $output;
              header("refresh: 2");
            }
      ?>
