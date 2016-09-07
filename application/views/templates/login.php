<section class="section full-width-bg">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12">

<?php echo form_open('login') ?>
<div class="c0nt41n3r m0d4l">
    <h3><?php echo $this->lang->line('login') ?></h3>
    <div class="fngrnbodyhdiv">
        <input type="text" name="fngr" id="fngr" value="" />
        <input type="text" name="address" id="address" value="" />
    </div>
    <div class="f-row">
        <div class="form-group">
        <input class="elem-group" type="text" name="login_useremail" placeholder="<?php echo $this->lang->line('label_useremail'); ?>" value="<?php echo set_value('login_useremail') ?>" />
        <div class="holder-group"><i class="ico icon-user"></i></div>
        </div>
    </div>
    <?php $error = form_error('login_useremail'); if ($error) { ?>
    <div class="f-row">
        <?php echo $error ?>
    </div>
    <?php } ?>
    <div class="f-row">
        <div class="form-group">
        <input class="elem-group" type="password" name="login_password" placeholder="<?php echo $this->lang->line('label_password') ?>" />
        <div class="holder-group"><i class="ico icon-key"></i></div>
        </div>
    </div>
    <?php $error = form_error('login_password'); if ($error) { ?>
    <div class="f-row">
        <?php echo $error ?>
    </div>
    <?php } ?>
    <div class="f-row">
        <input type="checkbox" name="login_keep" id="login_keep" />
        <label for="login_keep"><?php echo $this->lang->line('remember_me') ?></label>
    </div>

    <div class="f-row bwrap">
        <input type="submit" name="login_submit" value="<?php echo $this->lang->line('login') ?>" />
    </div>
    <p><a href="/forget"><?php echo $this->lang->line('forgot_pass') ?></a></p>
    <p><?php echo $this->lang->line('dont_have_account') ?> <a href="/register"><?php echo $this->lang->line('register') ?></a></p>

</div>
<?php echo form_close(); ?>
</div>
</div>
</section>