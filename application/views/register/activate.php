<script type="text/javascript" src="<?php echo base_url() ?>application/asset/js/jquery.complexify.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>application/asset/js/md5-min.js"></script>
<script type="text/javascript" >
    function getInvalidFields(scope)
    {
        var shortPwd = false;
        var noMatchPwd = false;
        var pwdLength = 6;
        var invalids = $(".required", scope).filter(function() {
            return this.value === "";
        });
        if ($('#password1', scope).val().length < pwdLength) {
            invalids = invalids.add($('#password1'));
            shortPwd = true;
        }
        if ($('#password2', scope).val().length < pwdLength) {
            invalids = invalids.add($('#password2', scope));
            shortPwd = true;
        }
        else if ($('#password1', scope).val() !== $('#password2', scope).val()) {
            invalids = invalids.add($('#password2', scope));
            noMatchPwd = true;
        }
        invalids.error = "";
        if (shortPwd)
        {
            if (invalids.error.length > 0)
                invalids.error += "\r\n";
            invalids.error += "La password deve essere lunga almeno " + pwdLength + " caratteri";
        }
        if (noMatchPwd)
        {
            if (invalids.error.length > 0)
                invalids.error += "\r\n";
            invalids.error += "Le due password digitate non corrispondono";
        }
        return invalids;
    }

    $(function() {

        $('#submitbtn').click(function(e) {
            e.preventDefault();
            var invalids = getInvalidFields($(this).closest('form'));
            if (invalids.length > 0) {
                $(invalids[0]).focus();
                invalids.addClass('invalid');
                alert(invalids.error);
            }
            else
            {
                $.get("<?php echo base_url() ?>crypt", null, function(data) {
                    eval(data);
                    var pwd = hex_md5($('#password1').val());
                    pwd = this.crypt(pwd);
                    $('#password').val(pwd);
                    $('#password1').val("");
                    $('#password2').val("");
                    $("#completeForm").submit();
                }, "text");
            }
        });

        $('input').change(function() {
            $(this).removeClass('invalid');
        });

        // Use the complexify plugin on the first password field
        $('#password1').complexify({
            minimumChars: 6,
            strengthScaleFactor: 0.7
        },
        function(valid, complexity) {
            $("#pwdmeter").css({"width": complexity + '%'});
        });
    });

</script>


<div class="col-md-3"></div>

<div class="col-md-6">
    <div class="container">
        <h2 class="text-center"><?php if ($resetpwd){?> Ripristino password <?php } else {?>Attiva la registrazione <?php  } ?></h2><br />
        <p>Ben tornato <?php echo $user_draft->name; ?>, inserisci qui la tua nuova password.</p>
        <?php echo validation_errors(); ?>
        <div class="col-md-2">

        </div>

        <div class="col-md-8">
            <?php echo form_open(base_url('register/activate'), array('id' => 'completeForm')) ?>


            <div >
                <div class="text-center">
                    <div class="form-group">
                        <input type="password" name="password1" id="password1" class="required form-control" placeholder="Scegli una password"/>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password2" id="password2" class="required form-control" placeholder="Ripeti la password"/>
                    </div>
                    <div class="form-group">
                        <label for="pwdmeter">Sicurezza della password:</label>
                        <div  class="pwdmetercontainer">
                            <div id ="pwdmeter" class="pwdmeter">

                            </div>
                        </div>
                    </div>

                    <input id="userkey" name="userkey" type="hidden" value="<?php echo $key; ?>"/>
                    <input id="resetpwd" name="resetpwd" type="hidden" value="<?php echo $resetpwd; ?>"/>
                    <input id="password" name="password" type="hidden" />
                    <input type="submit" name="submitbtn" id="submitbtn" value="<?php if ($resetpwd){?> Reimposta password <?php } else {?>Attiva la registrazione <?php  } ?>" class="btn btn-default form-control"/> 
                </div>
            </div>

            </form>
        </div>
        <div class="col-md-2">

        </div>
    </div>
</div>
<div class="col-md-3"></div>
