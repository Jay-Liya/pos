<script>
   $(document).ready(function(){

     var scrlDiv = document.getElementById("scrolldiv");
     if (typeof(scrlDiv) != 'undefined' && scrlDiv != null)
      scrlDiv.scrollTop = scrlDiv.scrollHeight;

     if(document.getElementById('lbluser').innerHTML == 'admin'||document.getElementById('lbluser').innerHTML == 'manager')
       $('.btnStock').show();
     else
        $('.btnStock').hide();

      $('.txtbarcode').focus();

      $("img[title='Edit']").click(function(){

          $(".txtreceived").removeAttr('readonly');
          $(".txtreceived").css("background-color", "white");
          $(".txtreceived").focus();

      });

      $('.selpaymenttype').change(function(){

        if($(this).val()!="0"){

           $(".txtvoucher").removeAttr('readonly');
           $(".txtvoucher").css("background-color", "white");
           $(".txtvoucher").focus();
        }
        else{

          $(".txtvoucher").attr('readonly','readonly');
          $(".txtvoucher").css("background-color", "bisque");
          $(".txtreceived").val($('.txttotalprice').text());
          $('.txtvoucher').val('');
          $('.txtadjustment').val('');
          $('.txtremark').val('');
        }

      });

      $('.txtqty').keypress(function(e){

          if(e.which == 13){

            if(parseInt($(this).val())<=0)
              alert("Enter a positive number for quantity.");
            else{
              var stkCount=$("#stkQty"+$(this).attr("id")).val();

              if($('.selSock').val()!=null){

                var selectedstr = $('.selSock').val();
                var sock = parseInt(selectedstr.substring(0, 1));
                var rqstQty=parseInt($(this).val());
                rqstQty=rqstQty*sock;

                if(parseInt(stkCount)<rqstQty){

                  stkCount=Math.floor(stkCount/sock);
                  alert("Only "+stkCount+" items available");
                  $(this).val(stkCount);
                }
              }
              else{
                if(stkCount>0 && parseInt(stkCount)<parseInt($(this).val())){

                  alert("Only "+stkCount+" items available");
                  $(this).val(stkCount);
                }
              }

               $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('pos/changeqtybytextbox'); ?>",
                    data: { rowid: $(this).attr("id"), newqty:$(this).val()}

               }).done(function(msg){

                    if(msg=='changeqty'){
                        location.reload();
                        $('.txtbarcode').val('');
                    }
                    location.reload();
                    $('.txtbarcode').val('');
                });
              }
          }
      });

    $('.txtbarcode').change(function () {
    //$('.txtbarcode').keyup(function () {

         $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/getProduct'); ?>",
              data: { barcodeid: $(this).val()}

         }).done(function(msg) {

              if(msg=='success'){

                  location.reload();
                  $('.txtbarcode').val('');

              }else if(msg=='nodata'){

                  alert('No item found.');
                  location.reload();
                  $('.txtbarcode').val('');

              }else{

                  location.reload();
                  $('.txtbarcode').val('');
              }
          });
      });


      $('.txtreceived').keypress(function (e) {

      //    $('.txtchange').text('');
          $('.txtremark').text('');

          if (e.which == 13) {

             var txtreceived = $('.txtreceived').val();
             var RE = /^-{0,1}\d*\.{0,1}\d+$/;

             if(txtreceived!=''){

               if(!RE.test(txtreceived))
                   alert('Received is only allow numeric value');

               var total = $('.txttotalprice').text();
               var adjustment =txtreceived - total;

               if($('.txtvoucher').val()!='') adjustment =  $('.txtvoucher').val()*1.0 + adjustment*1.0;

               $('.txtadjustment').val(adjustment.toFixed(2));

            //   $('.txtchange').text(0.00);
             }
          }
      });

      $('.txtvoucher').keypress(function (e) {

      //    $('.txtchange').text('');
          $('.txtreceived').val('');
          $('.txtadjustment').val('');

          if (e.which == 13) {

             var txtvoucher = $('.txtvoucher').val();
             var RE = /^-{0,1}\d*\.{0,1}\d+$/;

             if(txtvoucher!=''){

               if(!RE.test(txtvoucher))
                   alert('Voucher value is only allow numeric value');

               var total = $('.txttotalprice').text();
               var receive =total-txtvoucher;

               var receiveRnded=receive.toFixed(1);
               if(receiveRnded==0.0 && total!=txtvoucher)receiveRnded=0.05;

               $('.txtreceived').val(receiveRnded);

             }
          }
      });
/*
      $('.txtreceived').focus(function(){

          $('.txtchange').text('');

      });

      $('.txtreceived').click(function(){

          $('.txtchange').text('');

      });
*/
      $('.checkout').click(function(){

          var $ss = $('.selsimilar');

          for (i = 0; i < $ss.length; i++) {
              if ($ss.eq(i).val() == "") {
                  alert("Please select an item in the drop box or remove the dropbox.");
                  return false;
              }
          }

          functionAlert();

      });

      $('#viewreceipt').click(function(){

          var $ss = $('.selsimilar');

          for (i = 0; i < $ss.length; i++) {
              if ($ss.eq(i).val() == "") {
                  alert("Please select an item in the drop box or remove the dropbox.");
                  return false;
              }
          }

          var check = confirm("Are you sure you want to check out?");

          if (check == true) {

              $.ajax({
                type: "POST",
                url: "<?php echo site_url('pos/checkout'); ?>",
                data: { paymenttype: $('.selpaymenttype').val(), voucher: $('.txtvoucher').val(), adjustment: $('.txtadjustment').val(), remark: $('.txtremark').val()}
              }).done(function( msg ) {

                  strforcommentDiv="";

                  if($('.txtvoucher').val()!=""){

                    if($('.selpaymenttype').val()=="voucher") strforcommentDiv="</br><i>$ "+$('.txtvoucher').val()+" voucher accepted.</i>";
                    else if($('.selpaymenttype').val()=="invoice") strforcommentDiv="</br><i>$ "+$('.txtvoucher').val()+" invoice sales (Times Printers) accepted.</i>";

                    if($('.txtadjustment').val()!="" && $('.txtadjustment').val()!="0"){

                        if($('.txtremark').val()!="")
                          strforcommentDiv=strforcommentDiv+"</br><i>Total price adjusted by, $"+$('.txtadjustment').val()+" ("+$('.txtremark').val()+")</i>";
                        else
                          strforcommentDiv=strforcommentDiv+"</br><i>Total price adjusted by, $"+$('.txtadjustment').val()+"</i>";
                    }
                    $('#adjustmentdiv').show();

                  }else if($('.txtadjustment').val()!="" && $('.txtadjustment').val()!="0"){

                      if($('.txtremark').val()!="")
                        strforcommentDiv="</br><i>Total price adjusted by, $"+$('.txtadjustment').val()+" ("+$('.txtremark').val()+")</i>";
                      else
                        strforcommentDiv="</br><i>Total price adjusted by, $"+$('.txtadjustment').val()+"</i>";

                      $('#adjustmentdiv').show();

                  }else
                    $('#adjustmentdiv').hide();

                  document.getElementById('adjustmentdiv').innerHTML=strforcommentDiv;

                  if(msg=='success'){
                    $( "#dialog-form-receipt" ).dialog( "open" );
                  }
              });
          }
          else {
              return false;
          }

      });

      $('.emptycart').click(function(){

          var check = confirm("Are you sure you want to empty cart?");

          if (check == true) {
              $.ajax({
                url: "<?php echo site_url('pos/destroyCart'); ?>",
                cache: false
              }).done(function( msg ) {
                  if(msg=='success'){
                      location.reload();
                  }
              });
          }
          else {
              return false;
          }
      });

      $('.removeitem').click(function(){
          var answer = confirm('Are you sure you want to delete item');
          return answer
      });

      $('.selsimilar').change(function(){

          var selectValue = $(this).val();

          var changToStockId = selectValue.split("~")[0];
          var oldRowId = selectValue.substr(selectValue.indexOf("~") + 1);

          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/updateSession'); ?>",
              data: { changToStockId: changToStockId, oldRowId:oldRowId}
          }).done(function() {
                location.reload();
          });
      });

      $('.selpaperType').change(function(){

          var selectedstr = $(this).val();
          var paperType = selectedstr.substring(0, 2);
          var rowID = selectedstr.substring(2);

          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/paperTypeUpdate'); ?>",
              data: { rowID: rowID, type:paperType }
          }).done(function() {
                location.reload();
          });
      });

      $('.selpaperBWC').change(function(){

          var selectedstr = $(this).val();
          var paperBWC = selectedstr.substring(0, 2);
          var rowID = selectedstr.substring(2);

          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/paperBWCUpdate'); ?>",
              data: { rowID: rowID, bwc:paperBWC}
          }).done(function() {
                location.reload();
          });
      });

      $('.selpaperSide').change(function(){

          var selectedstr = $(this).val();
          var paperSide = selectedstr.substring(0, 2);
          var rowID = selectedstr.substring(2);

          var selectedstrBWC = $('#'+rowID+'selpaperBWC').val();
          var paperBWC = selectedstrBWC.substring(0, 2);

          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/paperSideUpdate'); ?>",
              data: { rowID: rowID, bwc:paperBWC, side:paperSide}
          }).done(function() {
                location.reload();
          });
      });

      $('.selpaperTypeA3').change(function(){

          var selectedstr = $(this).val();
          var paperType = selectedstr.substring(0, 2);
          var rowID = selectedstr.substring(2);

          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/paperTypeUpdateA3'); ?>",
              data: { rowID: rowID, type:paperType }
          }).done(function() {
                location.reload();
          });
      });

      $('.selpaperBWCA3').change(function(){

          var selectedstr = $(this).val();
          var paperBWC = selectedstr.substring(0, 2);
          var rowID = selectedstr.substring(4);

          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/paperBWCUpdateA3'); ?>",
              data: { rowID: rowID, bwc:paperBWC}
          }).done(function() {
                location.reload();
          });
      });

      $('.selpaperSideA3').change(function(){

          var selectedstr = $(this).val();
          var paperSide = selectedstr.substring(0, 2);
          var rowID = selectedstr.substring(4);

          var selectedstrBWC = $('#'+rowID+'selpaperBWCA3').val();
          var paperBWC = selectedstrBWC.substring(0, 2);

          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/paperSideUpdateA3'); ?>",
              data: { rowID: rowID, bwc:paperBWC, side:paperSide}
          }).done(function() {
                location.reload();
          });
      });

      $('.selSock').change(function(){

          var selectedstr = $(this).val();
          var sock = selectedstr.substring(0, 1);
          var rowID = selectedstr.substring(1);
          var stkCount=$("#stkQty"+rowID).val();

          if(parseInt(sock)>parseInt(stkCount)){
            alert("Only "+stkCount+" socks available");
            if(stkCount>=2){
              $(this).val(2+rowID);
              sock=2;
            }
            else{
              $(this).val(1+rowID);
              sock=1;
            }
            $('.txtqty').val(1);

          }

          $.ajax({
              type: "POST",
              url: "<?php echo site_url('pos/sockUpdate'); ?>",
              data: { rowID: rowID, sock:sock}
          }).done(function() {
                location.reload();
          });
      });

      $('.btnDailyReport').click(function(){
          $( "#dialog-form" ).dialog( "open" );
      });

      $( "#dialog-form-receipt" ).dialog({

    			autoOpen: false,
    			height:500,
    			width: 650,
    			modal: true,
    			buttons: {
    				"Print": function() {
    					$('#dialog-form-receipt').print();
              location.reload();
              return false;
    				},
    				Cancel: function() {
              $.ajax({
                url: "<?php echo site_url('pos/destroyCart'); ?>",
                cache: false
              }).done(function( msg ) {
                  if(msg=='success'){
                      location.reload();
                  }
              });
              location.reload();
    					//$( this ).dialog( "close" );
    				}
    			},
    			close: function() {
            location.reload();
    				//$( this ).dialog( "close" );
    			}
    		});

      $( "#dialog-form" ).dialog({

    			autoOpen: false,
    			height: 600,
    			width: 600,
    			modal: true,
    			buttons: {
    				"Print": function() {
    					$('#dialog-form').print();
              return false;
    				},
    				Cancel: function() {
    					$( this ).dialog( "close" );
    				}
    			},
    			close: function() {
    				$( this ).dialog( "close" );
    			}
    		});

  });

  function functionAlert(msg, yesFn, noFn) {

      var confirmBox = $("#confirm");

      confirmBox.find(".message").text(msg);
      confirmBox.find(".yes").unbind().click(function()
      {
          $.ajax({
            type: "POST",
            url: "<?php echo site_url('pos/checkout'); ?>",
            data: { paymenttype: $('.selpaymenttype').val(), voucher: $('.txtvoucher').val(), adjustment: $('.txtadjustment').val(), remark: $('.txtremark').val()}
          }).done(function( msg ) {

              strforcommentDiv="";

              if($('.txtvoucher').val()!=""){

                if($('.selpaymenttype').val()=="voucher") strforcommentDiv="</br><i>$ "+$('.txtvoucher').val()+" voucher accepted.</i>";
                else if($('.selpaymenttype').val()=="invoice") strforcommentDiv="</br><i>$ "+$('.txtvoucher').val()+" invoice sales (Times Printers) accepted.</i>";

                if($('.txtadjustment').val()!="" && $('.txtadjustment').val()!="0"){

                    if($('.txtremark').val()!="")
                      strforcommentDiv=strforcommentDiv+"</br><i>Total price adjusted by, $"+$('.txtadjustment').val()+" ("+$('.txtremark').val()+")</i>";
                    else
                      strforcommentDiv=strforcommentDiv+"</br><i>Total price adjusted by, $"+$('.txtadjustment').val()+"</i>";
                }
                $('#adjustmentdiv').show();

              }else if($('.txtadjustment').val()!="" && $('.txtadjustment').val()!="0"){

                  if($('.txtremark').val()!="")
                    strforcommentDiv="</br><i>Total price adjusted by, $"+$('.txtadjustment').val()+" ("+$('.txtremark').val()+")</i>";
                  else
                    strforcommentDiv="</br><i>Total price adjusted by, $"+$('.txtadjustment').val()+"</i>";

                  $('#adjustmentdiv').show();

              }else
                $('#adjustmentdiv').hide();

              document.getElementById('adjustmentdiv').innerHTML=strforcommentDiv;

              if(msg=='success'){
                //  $( "#dialog-form-receipt" ).dialog( "open" );
                location.reload();
              }
          });
          confirmBox.hide();
      });
      confirmBox.find(".no").unbind().click(function()
      {
          confirmBox.hide();
          return false;
      });
      confirmBox.find(".yes").click(yesFn);
      confirmBox.find(".no").click(noFn);
      confirmBox.show();
  }

</script>

<?php date_default_timezone_set('Asia/Singapore'); ?>
<div id="dialog-form-receipt" title="Receipt"  style="display:none;" >
  <?php echo img(array('src'=>'assets/images/cck_logo.png','style'=>'width:300px; height:80px;display: block;  margin-left: auto;  margin-right: auto;')); ?>
  <div style="color: #B93E8E;text-align:center;">1 CHOA CHU KANG GROVE, BLK 1 #03-04, SINGAPORE 688236</br>
  TEL: 6892 6379 FAX: 6892 9124</div>
  <!--<h5 style="text-align:left;color: #B93E8E;">Cashier: <?php //echo $this->session->userdata('username'); ?></h5>-->
  <table cellpadding="10" cellspacing="10" border="0" style=" border-collapse: collapse; width:100%;">
      <tr style="color: #B93E8E;" width="50%">
          <td width="50%"><b>GST Reg No : M2-0008412-8</b></td>
          <td><b>Receipt No : W - </b><?php echo $this->Product_model->getReceiptNum(); ?></td>
      </tr>
      <tr style="color: #B93E8E;">
          <td>Class  : </td>
          <td>Date Order : <?php echo date("Y-m-d"); ?></td>
      </tr>
      <tr style="color: #B93E8E;">
          <td>Name : </td>
          <td>Date Require : </td>
      </tr>
  </table>
  <table cellpadding="10" cellspacing="10" border="1" style=" border-collapse: collapse; border:1px solid #B93E8E; width:100%;">
      <tr style="color: #B93E8E;">
          <td>Description</td>
          <td>Quantity</td>
          <td>S.P.</td>
          <td>Amount</td>
      </tr>
      <?php foreach($this->cart->contents() as $items){
              if($items['types']==1){
        ?>
        <tr>
            <td align="left">
              <?php if($items['stockid']=='SOCK' && $this->session->userdata('sock')!==False){

                      if($this->session->userdata('sock')=='1')
                        echo $items['options']['product_DESCRIP1']." (1 pair)";
                      else if($this->session->userdata('sock')=='2')
                        echo $items['options']['product_DESCRIP1']." (2 pair)";

                    }
                    else if(isset($items['paperPcRm'])){

                      if($items['paperPcRm']=='pc')
                        echo $items['options']['product_DESCRIP1']." ".$items['paperPcRm']." ".$items['paperColor']." ".$items['paperSide'];
                      else if($items['paperPcRm']=='rm')
                          echo $items['options']['product_DESCRIP1']." ".$items['paperPcRm'];

                    }else echo $items['options']['product_DESCRIP1'];
               ?>
            </td>
            <td align="center"><?php echo $items['qty']; ?></td>
            <td align="right"><?php echo '$ '.(float)$items['price']; ?></td>
            <td align="right"><?php echo '$ '.$items['price']*$items['qty']; ?></td>
        </tr>
      <?php   }
            }
      ?>
      <tr>
          <td colspan="3" align="right" style="height:50px; padding:5px;color: #B93E8E;"><b>Total Amount </b>(Price inclusive of GST) </td>
          <td style="padding:5px;" align="right"><b>$ <label ><?php echo number_format($this->cart->total(),2); ?></label></b></td>
      </tr>
  </table>
  <div id="adjustmentdiv"></div>
  <h6 style="text-align:left;color: #B93E8E;" >Times Printers Pte Ltd</h6>
</div>
<div id="dialog-form" title="Daily Report" style="display:none;">
	<?php
            $orderrow = $this->Product_model->getOrderedProducts();

            if(count($orderrow)==0){
                echo "No record found for today !";
            }else{
        ?>
        <table cellpadding="10" cellspacing="10" border="1" style=" border-collapse: collapse; border:1px solid gray; width:100%;">
            <tr style="background:#D0D0D0;">
                <td width="30px"><b>NO</b></td>
                <td><b>Description</b></td>
                <td><b>Price</b></td>
                <td><b>Qty</b></td>
                <td><b>Sub Total</b></td>
            </tr>
            <?php

            $alltotal = 0;
            $adjustments = 0.0;
            $vouchers = 0.0;
            $invoices = 0.0;
            $tmorderid="";
            $AllItems=array();
            $Total=array();
            $Adjust=array();

            foreach($orderrow AS $items_order){

              $cat=$items_order->Category;
              $size=0;

              if($cat!='P'){

                $repeatIndex=-1;
                $strPrinting=$items_order->printing;
                $strDesc=$items_order->DESCRIP1;
                $strBarcode=$items_order->BarCode;
                $intQty=$items_order->order_qty;

                if(!empty($AllItems[$cat]))
                  $size=count($AllItems[$cat]);

                for($j=0;$j<$size;$j++){

                  if($strPrinting=='2 pair' && $AllItems[$cat][$j]['Desc']==$strDesc." 2 pair") $repeatIndex=$j;
                  else if($strPrinting!='2 pair' && $AllItems[$cat][$j]['Desc']==$strDesc) $repeatIndex=$j;
                }

                if($repeatIndex>-1){

                  if(strpos($strPrinting, '2 pair') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(5*$intQty); //5 is the 2 pair price
                    $Total[$cat]=$Total[$cat]+(5*$intQty); //5 is the 2 pair price
                  }
                  else{

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$items_order->order_qty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(($items_order->order_qty)*trim(preg_replace('/([^0-9\.])/i', '', $items_order->POSPrice)));
                    $Total[$cat]=$Total[$cat]+(($items_order->order_qty)*trim(preg_replace('/([^0-9\.])/i', '', $items_order->POSPrice)));
                  }

                  $repeatIndex=-1;
                }
                else{

                  if(strpos($strPrinting, '2 pair') !== false){

                    $AllItems[$cat][$size]['Desc']=$strDesc." 2 pair";
                    $AllItems[$cat][$size]['Price']=5; //5 is the 2 pair price
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else{

                    $AllItems[$cat][$size]['Desc']=$strDesc;
                    $AllItems[$cat][$size]['Price']=$items_order->POSPrice;
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }

                  $AllItems[$cat][$size]['Category']=$items_order->Category;
                  $AllItems[$cat][$size]['Subtotal']=($AllItems[$cat][$size]['Qty'])*trim(preg_replace('/([^0-9\.])/i', '', $AllItems[$cat][$size]['Price']));
                  if(empty($Total[$cat])) $Total[$cat]=$AllItems[$cat][$size]['Subtotal'];
                  else $Total[$cat]=$Total[$cat]+$AllItems[$cat][$size]['Subtotal'];

                  // $AllItems[$cat][$size]['Desc']=$items_order->DESCRIP1;
                  // $AllItems[$cat][$size]['Price']=$items_order->POSPrice;
                  // $AllItems[$cat][$size]['Qty']=$items_order->order_qty;
                  //
                  // $AllItems[$cat][$size]['Category']=$items_order->Category;
                  // $AllItems[$cat][$size]['Subtotal']=($items_order->order_qty)*trim(preg_replace('/([^0-9\.])/i', '', $items_order->POSPrice));
                  // if(empty($Total[$cat])) $Total[$cat]=$AllItems[$cat][$size]['Subtotal'];
                  // else $Total[$cat]=$Total[$cat]+$AllItems[$cat][$size]['Subtotal'];
                }
              }

              else if($cat=='P'){

                $repeatIndex=-1;
                $strPrinting=$items_order->printing;
                $strDesc=$items_order->DESCRIP1;
                $strBarcode=$items_order->BarCode;
                $intQty=$items_order->order_qty;

                if(!empty($AllItems[$cat]))
                  $size=count($AllItems[$cat]);

                for($j=0; $j<$size;$j++){
                  if(strpos($strBarcode, '001') !== false && $AllItems[$cat][$j]['Desc']==$strDesc)
                    $repeatIndex=$j;
                  else if(strpos($strBarcode, 'COLPAPER') !== false && $AllItems[$cat][$j]['Desc']==$strDesc)
                      $repeatIndex=$j;
              //    else if($strPrinting!="rm" && $strPrinting!="bwA41s" && $strPrinting!="bwA42s" && $strPrinting!="4cA41s" && $strPrinting!="4cA42s" && $strPrinting!="bwA31s" && $strPrinting!="bwA32s" && $strPrinting!="4cA31s" && $strPrinting!="4cA32s" && $AllItems[$cat][$j]['Desc']!=$strDesc )
              //      $repeatIndex=$j;
                  else if($strPrinting=="rm" && $strBarcode=="PAPERA4" && ($AllItems[$cat][$j]['Desc']==($strDesc." (ream)")))
                    $repeatIndex=$j;
                  else if($strPrinting=="rm" && $strBarcode=="PAPERA3" && ($AllItems[$cat][$j]['Desc']==($strDesc." (ream)")))
                    $repeatIndex=$j;
                  else if(strpos($strPrinting, 'bwA41s') !== false && $AllItems[$cat][$j]['Desc']==$strDesc." B&W 1S" )
                    $repeatIndex=$j;
                  else if(strpos($strPrinting, 'bwA42s') !== false && $AllItems[$cat][$j]['Desc']==$strDesc." B&W 2S" )
                    $repeatIndex=$j;
                  else if(strpos($strPrinting, '4cA41s') !== false && $AllItems[$cat][$j]['Desc']==$strDesc." 4C 1S" )
                    $repeatIndex=$j;
                  else if(strpos($strPrinting, '4cA42s') !== false && $AllItems[$cat][$j]['Desc']==$strDesc." 4C 2S" )
                    $repeatIndex=$j;
                  else if(strpos($strPrinting, 'bwA31s') !== false && $AllItems[$cat][$j]['Desc']==$strDesc." B&W 1S" )
                      $repeatIndex=$j;
                  else if(strpos($strPrinting, 'bwA32s') !== false && $AllItems[$cat][$j]['Desc']==$strDesc." B&W 2S" )
                    $repeatIndex=$j;
                  else if(strpos($strPrinting, '4cA31s') !== false && $AllItems[$cat][$j]['Desc']==$strDesc." 4C 1S" )
                    $repeatIndex=$j;
                  else if(strpos($strPrinting, '4cA32s') !== false && $AllItems[$cat][$j]['Desc']==$strDesc." 4C 2S" )
                    $repeatIndex=$j;
                }

                if($repeatIndex>-1){

                  if(strpos($strBarcode, '001') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(($intQty)*trim(preg_replace('/([^0-9\.])/i', '', $items_order->POSPrice)));
                    $Total[$cat]=$Total[$cat]+(($intQty)*trim(preg_replace('/([^0-9\.])/i', '', $items_order->POSPrice)));

                  }
                  else if(strpos($strBarcode, 'COLPAPER') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(($intQty)*trim(preg_replace('/([^0-9\.])/i', '', $items_order->POSPrice)));
                    $Total[$cat]=$Total[$cat]+(($intQty)*trim(preg_replace('/([^0-9\.])/i', '', $items_order->POSPrice)));

                  }
                  else if(strpos($strPrinting, 'rm') !== false && $strBarcode=="PAPERA4"){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+($intQty)/500;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(5*($intQty)/500); //5 is the ream price
                    $Total[$cat]=$Total[$cat]+(5*($intQty)/500); //5 is the ream price
                  }
                  else if(strpos($strPrinting, 'rm') !== false && $strBarcode=="PAPERA3"){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+($intQty)/500;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(8.90*($intQty)/500); //8.90 is the ream price
                    $Total[$cat]=$Total[$cat]+(8.90*($intQty)/500); //5 is the ream price
                  }
                  else if(strpos($strPrinting, 'bwA41s') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(0.02*$intQty); //0.02 is B&W 1S price
                    $Total[$cat]=$Total[$cat]+(0.02*$intQty); //0.02 is B&W 1S price

                  }
                  else if(strpos($strPrinting, 'bwA42s') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(0.04*$intQty); //0.04 is B&W 2S price
                    $Total[$cat]=$Total[$cat]+(0.04*$intQty); //0.04 is B&W 2S price

                  }
                  else if(strpos($strPrinting, '4cA41s') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(0.30*$intQty); //0.30 is 4C 1S price
                    $Total[$cat]=$Total[$cat]+(0.30*$intQty); //0.30 is 4C 1S price

                  }
                  else if(strpos($strPrinting, '4cA42s') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(0.60*$intQty); //0.60 is 4C 2S price
                    $Total[$cat]=$Total[$cat]+(0.60*$intQty); //0.60 is 4C 2S price

                  }
                  else if(strpos($strPrinting, 'bwA31s') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(0.06*$intQty); //0.06 is B&W 1S A3 price
                    $Total[$cat]=$Total[$cat]+(0.06*$intQty); //0.06 is B&W 1S A3 price

                  }
                  else if(strpos($strPrinting, 'bwA32s') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(0.12*$intQty); //0.12 is B&W 2S A3 price
                    $Total[$cat]=$Total[$cat]+(0.12*$intQty); //0.12 is B&W 2S A3 price

                  }
                  else if(strpos($strPrinting, '4cA31s') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(0.60*$intQty); //0.60 is 4C 1S A3 price
                    $Total[$cat]=$Total[$cat]+(0.60*$intQty); //0.60 is 4C 1S A3 price

                  }
                  else if(strpos($strPrinting, '4cA32s') !== false){

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(1.20*$intQty); //1.20 is 4C 2S A3 price
                    $Total[$cat]=$Total[$cat]+(1.20*$intQty); //1.20 is 4C 2S A3 price

                  }
                  else{

                    $AllItems[$cat][$repeatIndex]['Qty']=$AllItems[$cat][$repeatIndex]['Qty']+$intQty;
                    $AllItems[$cat][$repeatIndex]['Subtotal']=$AllItems[$cat][$repeatIndex]['Subtotal']+(($intQty)*trim(preg_replace('/([^0-9\.])/i', '', $items_order->POSPrice)));
                    $Total[$cat]=$Total[$cat]+(($intQty)*trim(preg_replace('/([^0-9\.])/i', '', $items_order->POSPrice)));

                  }

                  $repeatIndex=-1;
                }
                else{

                  if(strpos($strBarcode, '001') !== false){
                    $AllItems[$cat][$size]['Desc']=$strDesc;
                    $AllItems[$cat][$size]['Price']=$items_order->POSPrice;
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else if(strpos($strBarcode, 'COLPAPER') !== false){
                    $AllItems[$cat][$size]['Desc']=$strDesc;
                    $AllItems[$cat][$size]['Price']=$items_order->POSPrice;
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else if(strpos($strPrinting, 'rm') !== false && $strBarcode=="PAPERA4"){

                    $AllItems[$cat][$size]['Desc']=$strDesc." (ream)";
                    $AllItems[$cat][$size]['Price']=5; //5 is the ream price
                    $AllItems[$cat][$size]['Qty']=($intQty)/500;
                  }
                  else if(strpos($strPrinting, 'rm') !== false && $strBarcode=="PAPERA3"){

                    $AllItems[$cat][$size]['Desc']=$strDesc." (ream)";
                    $AllItems[$cat][$size]['Price']=8.90; //8.90 is the ream price
                    $AllItems[$cat][$size]['Qty']=($intQty)/500;
                  }
                  else if(strpos($strPrinting, 'bwA41s') !== false){

                    $AllItems[$cat][$j]['Desc']=$strDesc." B&W 1S";
                    $AllItems[$cat][$size]['Price']=0.02; //0.02 is the B&W 1S price
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else if(strpos($strPrinting, 'bwA42s') !== false){

                    $AllItems[$cat][$j]['Desc']=$strDesc." B&W 2S";
                    $AllItems[$cat][$size]['Price']=0.04; //0.04 is the B&W 2S price
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else if(strpos($strPrinting, '4cA41s') !== false){

                    $AllItems[$cat][$j]['Desc']=$strDesc." 4C 1S";
                    $AllItems[$cat][$size]['Price']=0.30; //0.30 is the 4C 1S price
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else if(strpos($strPrinting, '4cA42s') !== false){

                    $AllItems[$cat][$j]['Desc']=$strDesc." 4C 2S";
                    $AllItems[$cat][$size]['Price']=0.60; //0.60 is the 4C 2S price
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else if(strpos($strPrinting, 'bwA31s') !== false){
                    $AllItems[$cat][$j]['Desc']=$strDesc." B&W 1S";
                    $AllItems[$cat][$size]['Price']=0.06; //0.06 is the B&W 1S A3 price
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else if(strpos($strPrinting, 'bwA32s') !== false){

                    $AllItems[$cat][$j]['Desc']=$strDesc." B&W 2S";
                    $AllItems[$cat][$size]['Price']=0.12; //0.12 is the B&W 1S A3 price
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else if(strpos($strPrinting, '4cA31s') !== false){

                    $AllItems[$cat][$j]['Desc']=$strDesc." 4C 1S";
                    $AllItems[$cat][$size]['Price']=0.60; //0.60 is the 4C 1S A3 price
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else if(strpos($strPrinting, '4cA32s') !== false){

                    $AllItems[$cat][$j]['Desc']=$strDesc." 4C 2S";
                    $AllItems[$cat][$size]['Price']=1.20; //1.20 is the 4C 2S A3 price
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }
                  else{
                    $AllItems[$cat][$size]['Desc']=$strDesc;
                    $AllItems[$cat][$size]['Price']=$items_order->POSPrice;
                    $AllItems[$cat][$size]['Qty']=$intQty;
                  }

                  $AllItems[$cat][$size]['Category']=$items_order->Category;
                  $AllItems[$cat][$size]['Subtotal']=($AllItems[$cat][$size]['Qty'])*trim(preg_replace('/([^0-9\.])/i', '', $AllItems[$cat][$size]['Price']));
                  if(empty($Total[$cat])) $Total[$cat]=$AllItems[$cat][$size]['Subtotal'];
                  else $Total[$cat]=$Total[$cat]+$AllItems[$cat][$size]['Subtotal'];
                }
              }

              if($tmorderid!=$items_order->order_id){

                if(empty($Adjust[$cat])) $Adjust[$cat]=$items_order->adjustment;
                else $Adjust[$cat]=$Adjust[$cat]+$items_order->adjustment;

                $adjustments=$adjustments+$items_order->adjustment;
                $vouchers=$vouchers+$items_order->voucher;
                $invoices=$invoices+$items_order->invoice;
                $tmorderid=$items_order->order_id;
              }
            }

          foreach($AllItems AS $key => $val){

            for($i=0; $i<count($val);$i++){?>
              <tr>
                  <td><?php echo ($i+1); ?></td>
                  <td><?php echo $val[$i]['Desc']; ?></td>
                  <td align="right"><?php echo $val[$i]['Price']; ?></td>
                  <td align="center"><?php echo $val[$i]['Qty']; ?></td>
                  <td align="right"><?php echo number_format("".$val[$i]['Subtotal'],2); ?></td>
              </tr>
      <?php }
            if(!empty($Adjust[$key]) && ((float)$Adjust[$key])!=0.00){ ?>
              <tr >
                  <td colspan="4" style="text-align:right;">Adjustment</td>
                  <td style="text-align:right;"><?php echo number_format("".$Adjust[$key],2); ?></td>
              </tr>
              <tr >
                  <td colspan="4" style="text-align:right;">Total Price of<b><?php echo " ".$key; ?></b> items</td>
                  <td style="text-align:right;"><b><?php echo number_format("".$Total[$key]+$Adjust[$key],2); ?></b></td>
              </tr>
      <?php }else{ ?>
            <tr >
                <td colspan="4" style="text-align:right;">Total Price of<b><?php echo " ".$key; ?></b> items</td>
                <td style="text-align:right;"><b><?php echo number_format("".$Total[$key],2); ?></b></td>
            </tr>

    <?php   }
          } ?>

            <tr style="background: burlywood;">
                <td colspan="4" style="text-align:right;"><b>TOTAL CASH/NETS RECEIVED</b></td>
                <td style="text-align:right;"><b>
                  <?php
                    $ttlPrice=0;
                    foreach($Total AS $key => $val){
                      $ttlPrice=$ttlPrice+$val;
                    }
                    echo number_format($ttlPrice-$vouchers-$invoices+$adjustments,2);
                  ?></b>
                </td>
            </tr>
        </table>
      </br>
      <table cellpadding="2" cellspacing="2" border="0">
            <?php
          //  echo "<tr ><td>TOTAL CASH/NETS RECEIVED</td><td>: </td><td align='right'>$".number_format($ttlPrice-$vouchers-$invoices-$adjustments,2)."</td></tr >";
            echo "<tr ><td>TOTAL SYSTEM PRICE</td><td>: </td><td align='right'>$".number_format($ttlPrice,2)."</td></tr >";
            if($vouchers!=0) echo "<tr ><td>TOTAL VALUE OF VOUCHER RECEIVED</td><td>: </td><td align='right'>$".number_format($vouchers,2)."</td></tr >";
            if($invoices!=0) echo "<tr ><td>TOTAL INVOICE SALES (TIMES PRINTERS) RECEIVED</td><td>: </td><td align='right'>$".number_format($invoices,2)."</td></tr >";
            if($adjustments!=0) echo "<tr ><td>TOTAL ADJUSTMENT</td><td>: </td><td align='right'>$".number_format($adjustments,2)."</td></tr >";
           } ?>
     </table>
</div>

<div>
    <table style="width:100%;">
      <tr>
          <td>
              <h1>CCK POS SYSTEM</h1>
          </td>
          <td align="right">
            <?php echo img(array('src'=>'assets/images/user.png','style'=>'width:25px; height:25px;')); ?>
          </td>
          <td width="50px" align="left">
            <label id="lbluser"><?php echo $this->session->userdata('username'); ?></label>
          </td>
      </tr>
    </table>

    <table style="width:100%;">
      <tr>
          <td style="width:50%;">
              <?php echo form_input(array('name'=>'txtbarcode','class'=>'txtbarcode','autocomplete'=>'off')); ?>
          </td>
          <td align="right">
              <table>
                  <tr>
                      <td width="200px"align="left">
                          <a href="#" style="width:60px; height:60px;" class="btnDailyReport" title="Daily Report"><img src="<?php echo base_url('assets/images/report.png'); ?>" style="width:60px; height:60px;" /></a>
                      </td>
                      <td width="70px" align="center">
                          <?php echo anchor( site_url('pos/aging'), img(array('src'=>'assets/images/aging.png','style'=>'width:60px; height:60px;')),array('title'=>'Stock Aging Report','class'=>'btnStock')) ; ?>
                      </td>
                      <td width="70px" align="center">
                          <?php echo anchor( site_url('pos/stockreport'), img(array('src'=>'assets/images/stockreport.png','style'=>'width:60px; height:60px;')),array('title'=>'Closing Stock Report','class'=>'btnStock')) ; ?>
                      </td>
                      <td width="70px" align="center">
                          <?php echo anchor( site_url('pos/stock'), img(array('src'=>'assets/images/stock.png','style'=>'width:60px; height:60px;')),array('title'=>'Current Stock','class'=>'btnStock')) ; ?>
                      </td>
                      <td width="70px" align="center">
                          <?php echo anchor( site_url('pos/modifyitem'), img(array('src'=>'assets/images/modify.png','style'=>'width:60px; height:60px;')),array('title'=>'Modify Item','class'=>'btnStock')) ; ?>
                      </td>
                      <td width="70px" align="center">
                          <?php echo anchor( site_url('pos/updateitem'), img(array('src'=>'assets/images/item_update.png','style'=>'width:60px; height:60px;')),array('title'=>'Add Item Quantity','class'=>'btnupdateitem')) ; ?>
                      </td>
                      <td width="70px" align="center">
                          <?php echo anchor( site_url('pos/newitem'), img(array('src'=>'assets/images/newitem.png','style'=>'width:60px; height:60px;')),array('title'=>'Add New Item','class'=>'btnnewitem')) ; ?>
                      </td>
                      <td width="80px" align="center">
                          <?php echo anchor( site_url('pos/updatestk'), img(array('src'=>'assets/images/stock_update.png','style'=>'width:60px; height:60px;')),array('title'=>'Stock Update','class'=>'btnupdatestk')) ; ?>
                      </td>
                      <td width="70px" align="right">
                          <?php echo anchor( site_url('pos/logout'), img(array('src'=>'assets/images/logout.png','style'=>'width:60px; height:60px;')),array('title'=>'Log Out Now','class'=>'btnLogout')) ; ?>
                      </td>
                  </tr>
              </table>
           </td>
      </tr>
    </table>

    <table cellpadding="0" cellspacing="0" border="1" style="width:100%; margin-top:5px; border:1px solid gray; border-collapse:collapse;">
        <?php
        if($this->cart->total_items()==""){?>

        <tr>
            <td colspan="5" style="height:40px; padding:5px;">There is no items in your cart.</td>
        </tr>

        <?php }else{ ?>

        <!-- header -->
        <tr style="background:#D0D0D0; height:50px;">
            <td style="min-width:24px;width:25px;padding:5px;text-align:center;" ><b >No.</b></td>
            <td style="min-width:75px;width:75px; padding:5px;text-align:center;"><b >Brand</b></td>
            <td style="min-width:183px; padding:5px;text-align:center;"><b >Description</b></td>
            <td style="width:150px; padding:5px;text-align:center;"><b >Quantity</b></td>
            <td style="width:75px; padding:5px;text-align:center;"><b >Price</b></td>
            <td style="width:75px; padding:5px;text-align:center;"><b >Subtotal</b></td>
            <td style="width:50px; padding:5px;text-align:center;"><b >Delete</b></td>
            <td style="width:16px;"></td>
        </tr>
        <!-- end header -->

        <!-- item in cart -->
        <tr>
            <td colspan="9">
                <div id="scrolldiv" style="width:100%; height:300px; overflow:scroll;">
                <table cellpadding="0" cellspacing="0" border="0" style="width:100%; height:auto; overflow:scroll; border-collapse:collapse;">
                    <?php $i=1;?>
                    <?php foreach($this->cart->contents() as $items): ?>
                    <tr>
                        <td style="min-width:24px; width:25px; padding:5px; border-right:1px solid gray; border-bottom:1px solid gray;"><?php echo $i; ?></td>
                        <td style="min-width:75px; width:75px; padding:5px; border-right:1px solid gray; border-bottom:1px solid gray;"><?php echo $items['options']['product_BRAND']; ?></td>
                        <?php if($items['types']>1){

                          $barcode=$items['options']['product_BarCode'];
                          $product_row=$this->Product_model->getProductsbyBarCode($barcode);
                        ?>
                        <td style="min-width:183px; padding:5px; border-right:1px solid gray; border-bottom:1px solid gray;">
                            <select name="selsimilar" class="selsimilar" style="width:300px; height:34px; padding:5px;">
                                <option value="">-Select Item-</option>
                                <?php  foreach($product_row AS $similar){
                                          if($similar->current_stock>0){
                                ?>
                                            <option value="<?php  echo $similar->PRODUCTID.'~'.$items['rowid'] ?>" ><?php  echo $similar->DESCRIP1; ?></option>
                                <?php     }
                                      }?>
                            </select>
                        </td>
                        <td style="width:150px; padding:5px; border-right:1px solid gray;border-bottom:1px solid gray;text-align:right;"></td>
                        <td style="width:75px; padding:5px; border-right:1px solid gray;border-bottom:1px solid gray;text-align:right;"></td>
                        <td style="width:75px; padding:5px; border-right:1px solid gray;border-bottom:1px solid gray;text-align:right;"></td>

                        <?php }else{ ?>
                        <td style="min-width:183px; padding:5px; border-right:1px solid gray; border-bottom:1px solid gray;">
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        <?php echo $items['options']['product_DESCRIP1']; ?>
                                    </td>
                                    <td align="right">
                                        <?php
                                            if($items['stockid']=='5571A157AA'){

                                              if ($items['paperPcRm'] == 'pc'){

                                              ?>
                                                <select name="selpaperType" id="selpaperType" class="selpaperType" style="width:100px; height:34px; padding:5px;">
                                                    <option value="<?php echo 'pc'.$items['rowid'];?>">pc</option>
                                                    <option value="<?php echo 'rm'.$items['rowid'];?>">ream</option>
                                                </select>

                                                <?php if ($items['paperColor'] == 'bw'){ ?>

                                                      <select name="selpaperBWC" id=<?php echo $items['rowid']."selpaperBWC";?> class="selpaperBWC" style="width:100px; height:34px; padding:5px;">
                                                          <option value="<?php echo 'bw'.$items['rowid'];?>">B&W</option>
                                                          <option value="<?php echo '4c'.$items['rowid'];?>">4C</option>
                                                      </select>

                                                <?php }else{ ?>

                                                  <select name="selpaperBWC" id=<?php echo $items['rowid']."selpaperBWC";?> class="selpaperBWC" style="width:100px; height:34px; padding:5px;">
                                                      <option value="<?php echo '4c'.$items['rowid'];?>">4C</option>
                                                      <option value="<?php echo 'bw'.$items['rowid'];?>">B&W</option>
                                                  </select>

                                                <?php } if ($items['paperSide'] == '1s'){ ?>

                                                    <select name="selpaperSide" id="selpaperSide" class="selpaperSide" style="width:100px; height:34px; padding:5px;">
                                                        <option value="<?php echo '1s'.$items['rowid'];?>">1S</option>
                                                        <option value="<?php echo '2s'.$items['rowid'];?>">2S</option>
                                                    </select>

                                                <?php }else{ ?>

                                                    <select name="selpaperSide" id="selpaperSide" class="selpaperSide" style="width:100px; height:34px; padding:5px;">
                                                        <option value="<?php echo '2s'.$items['rowid'];?>">2S</option>
                                                        <option value="<?php echo '1s'.$items['rowid'];?>">1S</option>
                                                    </select>

                                                <?php }
                                              } else { ?>
                                                  <select name="selpaperType" id="selpaperType" class="selpaperType" style="width:100px; height:34px; padding:5px;">
                                                      <option value="<?php echo 'rm'.$items['rowid'];?>">ream</option>
                                                      <option value="<?php echo 'pc'.$items['rowid'];?>">pc</option>
                                                  </select>
                                            <?php }
                                            }else if($items['stockid']=='XPAP00081'){

                                              if ($items['paperPcRm'] == 'pc'){

                                              ?>
                                                <select name="selpaperTypeA3" id="selpaperTypeA3" class="selpaperTypeA3" style="width:100px; height:34px; padding:5px;">
                                                    <option value="<?php echo 'pc'.$items['rowid'];?>">pc</option>
                                                    <option value="<?php echo 'rm'.$items['rowid'];?>">ream</option>
                                                </select>
                                                <?php
                                                  if ($items['paperColor'] == 'bw'){ ?>

                                                      <select name="selpaperBWCA3" id=<?php echo $items['rowid']."selpaperBWCA3"; ?> class="selpaperBWCA3" style="width:100px; height:34px; padding:5px;">
                                                          <option value="<?php echo 'bwA3'.$items['rowid'];?>">B&W</option>
                                                          <option value="<?php echo '4cA3'.$items['rowid'];?>">4C</option>
                                                      </select>

                                                  <?php }else{ ?>

                                                      <select name="selpaperBWCA3" id=<?php echo $items['rowid']."selpaperBWCA3"; ?> class="selpaperBWCA3" style="width:100px; height:34px; padding:5px;">
                                                          <option value="<?php echo '4cA3'.$items['rowid'];?>">4C</option>
                                                          <option value="<?php echo 'bwA3'.$items['rowid'];?>">B&W</option>
                                                      </select>

                                                  <?php }

                                                  if ($items['paperSide'] == '1s'){ ?>

                                                      <select name="selpaperSideA3" id="selpaperSideA3" class="selpaperSideA3" style="width:100px; height:34px; padding:5px;">
                                                          <option value="<?php echo '1sA3'.$items['rowid'];?>">1S</option>
                                                          <option value="<?php echo '2sA3'.$items['rowid'];?>">2S</option>
                                                      </select>

                                              <?php }else{ ?>

                                                      <select name="selpaperSideA3" id="selpaperSideA3" class="selpaperSideA3" style="width:100px; height:34px; padding:5px;">
                                                          <option value="<?php echo '2sA3'.$items['rowid'];?>">2S</option>
                                                          <option value="<?php echo '1sA3'.$items['rowid'];?>">1S</option>
                                                      </select>

                                            <?php  }
                                                } else { ?>
                                                    <select name="selpaperTypeA3" id="selpaperTypeA3" class="selpaperTypeA3" style="width:100px; height:34px; padding:5px;">
                                                        <option value="<?php echo 'rm'.$items['rowid'];?>">ream</option>
                                                        <option value="<?php echo 'pc'.$items['rowid'];?>">pc</option>
                                                    </select>
                                              <?php }
                                              }
                                              else if($items['stockid']=='SOCK'){
                                                ?>
                                                  <select name="selSock" id="selSock" class="selSock" style="width:100px; height:34px; padding:5px;">
                                                  <?php if($this->session->userdata('sock')!==False){
                                                          if($this->session->userdata('sock')=='1'){ ?>
                                                            <option value="<?php echo '1'.$items['rowid'];?>" selected>1 pair</option>
                                                    <?php }else{ ?>
                                                            <option value="<?php echo '1'.$items['rowid'];?>" >1 pair</option>
                                                          <?php }
                                                          if($this->session->userdata('sock')=='2'){ ?>
                                                            <option value="<?php echo '2'.$items['rowid'];?>" selected>2 pair</option>
                                                    <?php }else{ ?>
                                                            <option value="<?php echo '2'.$items['rowid'];?>" >2 pair</option>
                                                          <?php }
                                                        }else{ ?>
                                                            <option value="<?php echo '1'.$items['rowid'];?>" selected>1 pair</option>
                                                            <option value="<?php echo '2'.$items['rowid'];?>">2 pair</option>
                                                    <?php
                                                            $this->session->set_userdata('sock','1');
                                                        } ?>
                                                  </select>
                                        <?php } ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:150px; padding:5px; border-right:1px solid gray; border-bottom:1px solid gray;" class="QTY">
                            <table cellpadding="0" cellspacing="0" border="0" align="center">
                              <tr>
                                  <td><?php echo anchor('pos/increaseitem/'.$items['rowid'], img(array('src'=>'assets/images/up.png','title'=>'increase','style'=>'cursor:pointer; border:0px;'))); ?></td>
                                  <td><input type="text" name="txtqty" class="txtqty" id="<?php echo $items['rowid']; ?>" value="<?php echo $items['qty']; ?>" />
                                    <input type="hidden" id="<?php echo 'stkQty'.$items['rowid']; ?>" name="txtStkQty" value="<?php if($items['options']['product_BarCode']=='BINDA4' || $items['options']['product_BarCode']=='BINDA3') echo '-1'; else echo $items['stock_Qty']; ?>"></td>
                                  <td><?php echo anchor('pos/decreaseitem/'.$items['rowid'], img(array('src'=>'assets/images/down.png','title'=>'decrease','style'=>'cursor:pointer; border:0px;'))); ?></td>
                              </tr>
                            </table>
                        </td>
                        <td style="width:75px; padding:5px; border-right:1px solid gray;border-bottom:1px solid gray;text-align:right;"><p id="<?php echo "priceblk".$items['rowid'];?>"><?php echo $items['price']; ?></p></td>
                        <td style="width:75px; padding:5px; border-right:1px solid gray;border-bottom:1px solid gray;text-align:right;"><p id="<?php echo "subttlblk".$items['rowid'];?>"><?php echo '$ '.$items['price']*$items['qty']; ?></td>
                          <?php } ?>
                        <td style="width:50px; padding:5px; border-left:1px solid gray;border-bottom:1px solid gray;"><?php echo anchor('pos/remove/'.$items['rowid'],img(array('src'=>'assets/images/delete.png','title'=>'delete item','style'=>'cursor:pointer; border:0px;')),array('class' => 'removeitem')); ?></td>

                    </tr>

                    <?php $i++;?>
                    <?php endforeach ; ?>
                </table>
                </div>
           </td>
        </tr>
        <!-- end of item in cart -->

        <!-- Total Price -->
        <tr>
            <td colspan="5" align="right" style="height:40px; padding:5px;"><b>Total Price</b></td>
            <td colspan="3" style="background: bisque; padding:5px;"><b>$ <label class="txttotalprice"><?php echo $this->cart->total(); ?></label></b></td>
        </tr>
        <tr>
            <td colspan="5" align="right" style="height:40px; padding:5px;">
              <select name="selpaymenttype" class="selpaymenttype" style="height:34px; padding:5px;">
                  <option value="0" selected="selected">Select - Payment made via</option>
                  <option value="voucher">Voucher</option>
                  <option value="invoice">Invoice Sales (Times Printers)</option>
              </select>
            </td>
            <td colspan="3" style="background: bisque; padding:5px;">$ <input type="text" name="txtvoucher" class="txtvoucher" style="width:80px; height:20px; padding:3px;background: bisque;outline: none !important;border:none !important;-webkit-box-shadow: none;box-shadow: none;" readonly/>
            </td>
        </tr>
        <tr>
            <td colspan="5" align="right" style="height:35px; padding:5px;"><b>CASH/NETS RECEIVED</b></td>
            <td colspan="3" style="background: bisque; padding:5px; vertical-align: middle;" valign="middle">
              $ <input type="text" name="txtreceived" class="txtreceived" style="width:80px; height:20px; padding:3px;background: bisque;outline: none !important;border:none !important;-webkit-box-shadow: none;box-shadow: none;"
              value="<?php echo $this->Product_model->roundingFunc($this->cart->total()); ?>" readonly/>
              <?php echo img(array('src'=>'assets/images/pencil.png','title'=>'Edit','style'=>'width:20px; height:20px;cursor:pointer; border:0px;margin-left:2px;')); ?></td>
        </tr>
        <tr>
            <td colspan="5" style="background: #D0D0D0; height:35px; padding:5px;">
              <table style="width:100%;">
                <tr>
                  <td style="width:10%;" align="center"><b>Remark</b></td>
                  <td style="width:70%;"><input type="text" name="txtremark" class="txtremark" style="width:100%; height:20px; padding:3px;" /></td>
                  <td align="right"><b>Adjustment</b></td>
                </tr>
              </table>
            </td>
            <td colspan="3" style="background: #D0D0D0; padding:5px;">$ <input type="text" name="txtadjustment" class="txtadjustment" style="width:80px; height:20px; padding:3px;" value="<?php echo number_format($this->Product_model->roundingFunc($this->cart->total()) - $this->cart->total(),2); ?>" /></td>
        </tr>
  <!--      <tr>
            <td colspan="5" align="right" style="height:35px; padding:5px;"><b>Change</b></td>
            <td colspan="3" style="background: bisque; padding:5px;"><b>$ <label class="txtchange">0.0</label></b></td>
        </tr>-->
        <tr style="background: #D0D0D0;">
            <td colspan="8" align="right">
                <input type="button" id="viewreceipt" value="Check Out with Receipt" style="width:180px; height:40px; margin:5px 0 5px 0;cursor: pointer; " />
                <input type="button" class="emptycart" value="Empty Cart" style="width:180px; height:40px; margin:5px 0 5px 0;cursor: pointer; " />
                <input type="button" class="checkout" value="Check Out Now" style="width:180px; height:40px; margin:5px; background-color: #4CAF50;cursor: pointer; " />
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
<div id="confirm" style="display: none; background-color: #F3F5F6;color: #000000;
            border: 3px solid #aaa;position: fixed;bottom: 0;right: 0;  width: 300px;  height: 100px;
              box-sizing: border-box;  text-align: center;margin:10px;border-radius: 15px;">
   <div class="message" style="text-align: center;margin-top:10px;">Are you sure you want to check out?</div><br>
   <button class="yes" style="background-color: #FFFFFF;display: inline-block;  border-radius: 12px;
      border: 4px solid green;  padding: 5px;  text-align: center;  width: 60px; cursor: pointer;">Yes</button>
   <button class="no" style="background-color: #FFFFFF;display: inline-block;  border-radius: 12px;
         border: 4px solid red;  padding: 5px;  text-align: center;  width: 60px; cursor: pointer;">No</button>
</div>
