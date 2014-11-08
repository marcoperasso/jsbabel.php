
<div class="col-md-3"></div>
<div class="col-md-6">
    <div class="container text-center">
        <br />
        <h2>Si è verificato un errore</h2><br />
        <div class="col-md-2">

        </div>

        <div class="col-md-8">
            <p>
               Mi spiace, qualcosa non è andato come ci aspettavamo.
            </p>
            <?php if (!isset($reason)) $reason = "Purtroppo non abbiamo ulteriori dettagli che possano aiutarti."; ?>
            <p>
                <?php echo $reason; ?>
            </p>
            <p class="text-center">
                <a href="">Torna alla pagina principale</a>
            </p>
        </div>
        <div class="col-md-2">

        </div>
    </div>
</div>
<div class="col-md-3"></div>