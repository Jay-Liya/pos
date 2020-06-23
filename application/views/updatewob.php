<script>
   $(document).ready(function(){

      $("[name='txtdesc']").focus();

      $(document).ready(function(){

          $("[name='btnsubmit']").click(function(){

              var barcode = $("[name='txtbrcode']").val();
              if(barcode==''){
                  alert('Barcode is required!');
                  return false;
              }
          });

      });

      $('.refresh').click(function(){
          location.reload();
      });

    });
</script>

<?php echo form_open('pos/updatewob'); ?>
  <div>
      <table cellpadding="5" cellspacing="5" border="0" style="width:100%; border:1px solid #D0D0D0; background:  #d3d3d3;">
        <tr>
            <td colspan="4" align="center"><b>Add items without barcode</b></td>
        </tr>
        <tr>
          <td style="width:10%;">
             <p>Description</p>
          </td>
            <td style="width:40%;">
               <?php echo form_input(array('name'=>'txtdesc','class'=>'txtbarcode')); ?>
            </td>
            <td style="width:25%;">
               <?php echo form_submit(array('name'=>'btnsubmit','class'=>'btnsubmit','value'=>'Search item','style'=>'width:80px; height:34px; padding:3px;')); ?>
            </td>
            <td><a href="#" class="refresh"><?php echo img(array('src'=>'assets/images/refresh.png','style'=>'width:30px; height:30px;','title'=>'Click to refresh.')); ?></a></td>
        </tr>
      </table>

<?php echo form_close();
    if(@$barcode){?>
      <table cellpadding="10" cellspacing="10" border="1" style=" border-collapse: collapse; border:1px solid gray; width:100%;">
          <tr >
              <td style="background:#D0D0D0;"><b>BARCODE</b></td>
              <td><input id="txtbrcd" name="txtbrcd" style="border:0;background-color:white;" value="<?php echo $barcode;?>"  readonly ></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>PRODUCT ID</b></td>
              <td><input id="txtpdtid" name="txtpdtid" style="border:0;background-color:white;" value="<?php echo $productid;?>" readonly ></td>

          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>STOCK ID</b></td>
              <td><input id="txtstkid" name="txtstkid" style="border:0;background-color:white;" value="<?php echo $stockid;?>"  readonly></td>
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
              <td style="background:#D0D0D0;"><b>POS PRICE</b></td>
              <td><?php echo $posprice;?></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>STAFF PRICE</b></td>
              <td><?php echo $staffprice;?></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>ACTIVE STATUS</b></td>
              <td><?php echo $active;?></td>
          </tr>
          <tr >
              <td style="background:#D0D0D0;"><b>CURRENT STOCK</b></td>
              <td><?php echo $currentstock;?> +
                <?php echo form_input(array('name'=>'txtnewqty','style'=>'width:50px; height:25px; margin:5px;'));
                echo form_submit(array('name'=>'btnadd','class'=>'btnsubmit','value'=>'Add','style'=>'width:50px; height:30px; padding:3px;'));?></td>
          </tr>
      </table>
<?php  }
        else if(@$output)
          echo $output; ?>
