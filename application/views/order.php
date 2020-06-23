<script>
    $(document).ready(function(){
        $('.button').button();
        
        $('.txtFrom').datepicker({maxDate: '0',dateFormat: 'yy-mm-dd'});
        $('.txtTo').datepicker({maxDate: '0',dateFormat: 'yy-mm-dd'});
        
        $('.button').click(function(){
            var from = $('.txtFrom').val();
            var to = $('.txtTo').val();
            if(from=="" || to==""){
                alert('Please select from date and to date.');
                return false;
            }else{
                return true;
            }
        });
    });
</script>
<div style="width:100%; height:100px; border:1px solid  #DDD; background: #EEE;">
    <table cellpadding="5" cellspacing="5" border="0" style="margin-top:5px;">
        <tr>
            <td colspan="5"><b>Order Report</b></td>
        </tr>
        <?php echo form_open('pos/order'); ?>
        <tr>
            <td>From</td>
            <td><?php echo form_input(array('name'=>'txtFrom','class'=>'txtFrom','style'=>'width:200px; height:26px;','autocomplete'=>'off')); ?></td>
            <td>To</td>
            <td><?php echo form_input(array('name'=>'txtTo','class'=>'txtTo','style'=>'width:200px; height:26px;','autocomplete'=>'off')); ?></td>
            <td><?php echo form_submit(array('name'=>'btnGenerate','class'=>'button','value'=>'Generate')); ?></td>
        </tr>
        <?php echo form_close(); ?>
    </table>
    <?php 
    if(@$order){
        if($order->num_rows()>0){
    ?>
    <table style="width:100%; margin-top:25px; border:1px solid  #DDD; " border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td colspan="4">
                <?php echo img(array('src'=>'assets/images/download.png','style'=>'width:28px; height:28px; float:left;')); ?>
                <a href="<?php echo site_url('pos/exportOrder'.'/'.$to.'/'.$from); ?>" style="float:left;" class="downloadcsv" >Download as csv</a>
            </td>
        </tr>
        <tr style="background: #EEE;">
            <td><b>No.</b></td>
            <td><b>Order Number</b></td>
            <td><b>Stock ID</b></td>
            <td><b>Quantity</b></td>
        </tr>
        <?php 
            $i=0;
            foreach($order->result() AS $o_row):
            $i++;
        ?>
        <tr>
            <td style="border-top:1px solid #DDD;"><?php echo $i.'.'; ?></td>
            <td style="border-top:1px solid #DDD;"><?php echo $o_row->order_id; ?></td>
            <td style="border-top:1px solid #DDD;"><?php echo $o_row->STOCKID; ?></td>
            <td style="border-top:1px solid #DDD;"><?php echo $o_row->order_qty; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php 
        }else{
    ?>
    
    <table style="width:100%; margin-top:25px; border:1px solid  #DDD; " border="0" cellpadding="5" cellspacing="0">
        <tr>
            <td>Sorry, no record found for your search !</td>
        </tr>
    </table>
    
    <?php
        } 
    } 
    ?>
</div>
