<div class="header row">
    <?php
    $caller = $this->router->fetch_class() . '/' . $this->router->fetch_method();
    //if (!isset($user))
    //   $this->load->view('login');
    $user = get_user();
    ?>

    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="" title="Pagina principale"><img alt="logo" class = 'logo' src="img/logo.png"/></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <?php
            if ($user) {
                ?>
                <ul class="nav navbar-nav">
                    
                    <li class="user"><a href="<?php echo BASE_URL ?>/user">My data</a></li>
                    <li class="mysites"><a href="<?php echo BASE_URL ?>/mysites">My sites</a></li>
                </ul>
            <?php } ?>
            <ul class="nav navbar-nav navbar-right" style = "padding-right: 30px">
                <?php if ($user) { ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Hello, <?php echo $user->to_string(); ?></a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:doLogoff()">Logoff</a></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div> 
    </nav>
    <script type="text/javascript">
        function doLogoff()
        {
            jQuery.getScript('translator/do_logoff');
        }
    </script>
</div>
