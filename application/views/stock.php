<script>
    $(document).ready(function(){

        $('.btnsubmit').click(function(){

            if($('.selItem').val()==''){
                alert('Please select Item.');
                return false;
            }
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

    });
</script>
<!--search form -->
<?php echo form_open('pos/stock',array('class'=>'frmstock'));?>
<table cellpadding="5" cellspacing="5" border="0" style="width:100%; border:1px solid #D0D0D0; background:  #d3d3d3;">
    <tr>
        <td colspan="11" align="center"><h3>Current Stock</h3></td>
    </tr>
    <tr>
        <td style="width:300px;"><?php //echo form_input(array('name'=>'txtbarcode','class'=>'txtbarcode','autocomplete'=>'off','style'=>'width:200px; height:24px; padding:3px;')); ?>
          <select name="selBarcode" class="selBarcode" style="width:300px; height:34px; padding:5px;">
              <option value="">-- Select Barcode --</option>
              <?php
              $itemsArray=$this->Product_model->getOrderedBarcodes();
              foreach($itemsArray AS $item){ ?>
                          <option value="<?php  echo $item->BarCode; ?>" ><?php  echo $item->BarCode; ?></option>
              <?php }?>
          </select>
        </td>
        <td width="100px" align="right">
            <?php echo anchor( site_url('pos/stock'), img(array('src'=>'assets/images/refresh.png','style'=>'width:40px; height:40px;')),array('title'=>'Refresh','class'=>'btnStock')) ; ?>
        </td>
        <td align="center"><?php echo anchor(site_url('pos/exportCsv'),img(array('src'=>'assets/images/csv.png','style'=>'width:40px; height:40px;','title'=>'Download Excel file'))) ?></td>
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
            <?php echo anchor( site_url('pos/updatestk'), img(array('src'=>'assets/images/stock_update.png','style'=>'width:40px; height:40px;')),array('title'=>'Stock update','class'=>'btnStock')) ; ?>
        </td>
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
    <tr colspan="10"><td><div id='displayItemDropInner' ></div></br><?php echo form_submit(array('name'=>'btnsubmit','class'=>'btnsubmit','value'=>'Submit','style'=>'width:80px; height:34px; padding:3px;')); ?></br></td></tr>
</table>
<?php echo form_close() ?>

<!--search result -->

<?php
    if(@$result){
?>
        <table border="1" cellpadding="5" cellspacing="5" style="width:100%; border-collapse:collapse; border:1px solid #D0D0D0; margin-top:10px;">
        <?php
        if(@$productnumrow==0){
        ?>
            <tr>
                <td>Sorry no product found for your search.</td>
            </tr>
        <?php
        }else{
        ?>
            <tr style="background: #D0D0D0;">
                <td>Brand</td>
                <td>Stock ID</td>
                <td>Description</td>
                <td>Cost</td>
                <td>Price</td>
                <td>Barcode ID</td>
                <td>Quantity</td>
                <td>Cat</td>
            </tr>
            <?php
                foreach(@$productquery->result() as $rows){
            ?>
                <tr>
                    <td><?php echo $rows->BRAND; ?></td>
                    <td><?php echo $rows->STOCKID; ?></td>
                    <td><?php echo $rows->DESCRIP1; ?></td>
                    <td><?php echo $rows->COST; ?></td>
                    <td><?php echo $rows->POSPrice; ?></td>
                    <td><?php echo $rows->BarCode; ?></td>
                    <td><?php echo $rows->current_stock; ?></td>
                    <td><?php echo $rows->Category; ?></td>
                </tr>
            <?php } ?>
      <?php } ?>
    </table>
<!--default product list-->
<?php
}else{
?>
    <table border="1" cellpadding="5" cellspacing="5" style="width:100%; border-collapse:collapse; border:1px solid #D0D0D0; margin-top:10px;">
        <tr style="background: #D0D0D0;">
            <td>Brand</td>
            <td>Stock ID</td>
            <td>Description</td>
            <td >Cost</td>
            <td >Price</td>
            <td>Barcode ID</td>
            <td >Quantity</td>
            <td >Cat</td>
        </tr>
        <?php
            foreach(@$product_query->result() as $rows){
        ?>
            <tr>
                <td><?php echo $rows->BRAND; ?></td>
                <td><?php echo $rows->STOCKID; ?></td>
                <td><?php echo $rows->DESCRIP1; ?></td>
                <td align="right"><?php echo $rows->COST; ?></td>
                <td align="right"><?php echo $rows->POSPrice; ?></td>
                <td><?php echo $rows->BarCode; ?></td>
                <td align="right"><?php echo $rows->current_stock; ?></td>
                <td><?php echo $rows->Category; ?></td>
            </tr>
        <?php } ?>
            <tr style="height:50px;">
                <td colspan="8">
                    <table>
                        <tr>
                            <td><?php echo @$pagination; ?> </td>
                            <td>Total number of stock : <?php echo @$numrow; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
    </table>
<?php
}
?>
