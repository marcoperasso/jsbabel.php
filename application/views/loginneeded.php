
<div class="col-md-2"></div>
<div class="col-md-4 center-text">
    <a href="" title="Vai alla pagina principale">
        <img class="logo" src="application/asset/img/logo.png" id="ecommuter_logo">
    </a>
</div>
<div class="col-md-4 container">
    <h1>Scusaci, ma non sei autenticato.</h1>
    <h2>Per visualizzare la pagina richiesta devi prima accedere utilizzando le tue credenziali.</h2>
    <a data-toggle="modal" href="#loginModal" class="btn btn-primary btn-lg">Accedi</a>
</div>
<div class="col-md-2"></div>
<script type="text/javascript">window.onLogged = function()
    {
        window.location.href = '<?php echo isset($request) ? $request : '/';?>';
    };
</script>

