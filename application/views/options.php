<script>
    $(document).ready(function(){
        $('.btnCancel').click(function(){
            location.redirect="<?php echo site_url('pos'); ?>";
        });

        $('.btnSubmit').click(function(){
            var radio= $('input:radio[name=rdoStockID]:checked').is(':checked');
            if(radio!=true){
                   alert('Please select at least one to submit.');
                   return false;
            }else {
                   return true;
            }
        });
    });
</script>
<?php echo form_open('pos/options') ?>
<p align="center">This barcode ID "<?php echo @$barcodeid; ?>" has <?php echo @$no_of_item; ?> options. Please select one.</p>
<table cellpadding="15" cellspacing="5" border="1" style=" border:1px solid gray; border-collapse: collapse; width:800px;" align="center">
    <tr style="background-color: #D0D0D0;">
        <td style="width:30px;" align="center"><b>#</b></td>
        <td><b>Description</b></td>
        <td style="width:50px;" align="center"><b>Brand</b></td>
        <td style="width:60px;" align="center"><b>Quantity<br/>(In Stock)</b></td>
    </tr>
     <?php foreach(@$product_row as $items){ ?>
    <tr>
        <td align="center">
            <input type="radio" name="rdoStockID" class="rdoStockID" value="<?php echo $items->ID ?>" />
        </td>
        <td><?php echo $items->DESCRIP1 ?></td>
        <td align="center"><?php echo $items->BRAND ?></td>
        <td align="center"><?php echo $items->current_stock ?></td>
    </tr>
    <?php } ?>
    <tr>
        <td colspan="4" align="right">
            <input type="button" name="btnCancel" class="btnCancel" value="Cancel" style="width:150px; height:44px;"/>
            <input type="Submit" name="btnSubmit" class="btnSubmit" value="Submit" style="width:150px; height:44px;"/>
        </td>
    </tr>
</table>
<?php echo form_close() ?>
