<?php echo form_open('login'); ?>
<table align="center" style="margin:10% auto; border:1px solid gray;box-shadow:
        0 2px 2px rgba(0,0,0,0.2),0 1px 5px rgba(0,0,0,0.2),0 0 0 12px rgba(255,255,255,0.4); background: #fffaf6;border-radius: 4px;
    color: #7e7975;padding: 15px;" cellpadding="10" cellspacing="5">
    <tr style="font-size: 15px;font-weight: bold;color: #bdb5aa;padding-bottom: 8px;border-bottom: 1px solid #EBE6E2;
    text-shadow: 0 2px 0 rgba(255,255,255,0.8);box-shadow: 0 1px 0 rgba(255,255,255,0.8);">
        <td align="center"><b>CCK POS Log In</b></td>
    </tr>
    <?php
        if(validation_errors()){
    ?>
    <tr>
        <td >
            <div class="ui-state-error" style="border:0px; color:red; font-size:12px;"><?php echo validation_errors(); ?></div>
        </td>
    </tr>
    <?php
        }
    ?>
    <tr>

        <td align="center"><?php echo form_input(array('name'=>'txtusername','placeholder'=>'Username','style'=>'height:25px;
    font-size: 13px;    font-weight: 400;    display: block;    width: 92%;    padding: 5px;    margin-bottom: 5px;
    border: 3px solid #ebe6e2; border-radius: 5px;    transition: all 0.3s ease-out;')); ?></td>
    </tr>
    <tr>

        <td align="center"><?php echo form_password(array('name'=>'txtpassword','placeholder'=>"Password",'style'=>'height:25px;
    font-size: 13px;    font-weight: 400;    display: block;    width: 92%;    padding: 5px;
    margin-bottom: 5px;    border: 3px solid #ebe6e2;    border-radius: 5px;    transition: all 0.3s ease-out;')); ?></td>
    </tr>
    <tr>
        <td align="center">
            <?php echo form_submit(array('name'=>'btnSubmit','value'=>'Log In','style'=>'width:100px; height:28px;margin-left: 1%;
    background: linear-gradient(#fbd568, #ffb347);    border: 1px solid #f4ab4c;
    color: #996319;    text-shadow: 0 1px rgba(255,255,255,0.3); cursor: pointer;')); ?>
        </td>
    </tr>
</table>
<?php echo form_close(); ?>
