<div class="header row">
    <?php
    $caller = $this->router->fetch_class() . '/' . $this->router->fetch_method();
    //if (!isset($user))
    //   $this->load->view('login');
    ?>

    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="" title="Pagina principale"><img alt="logo" class = 'logo' src="application/asset/img/logo.png"/></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
<!--        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <?php
            if (isset($user)) {
                ?>
                <ul class="nav navbar-nav">
                    <li class="user"><a href="/user">I miei dati</a></li>
                    <li class="routes"><a href="/user/routes">I miei itinerari</a></li>
                    <li class="my_ecommuters"><a href="/user/my_ecommuters">Il mio gruppo</a></li>
                </ul>
            <?php } ?>
            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($user)) { ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Ciao <?php echo $user->to_string(); ?></a>
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="doLogoff()">Esci</a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li>
                        <a data-toggle="modal" href="#loginModal" data-backdrop="static"><p class="text-right" style="padding-right: 30px">Accedi</p></a>
                    </li>
                <?php } ?>
            </ul>
        </div> /.navbar-collapse -->
    </nav>
</div>