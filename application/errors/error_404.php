<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'application/views/templates/header.php'; ?>
        <title>404 - Pagina non trovata</title>
        <style>p{text-align: center;}</style>
    </head>

    <body>
        <div class="content">

            <div class="body center-text row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <h1><?php echo $heading; ?></h1>
                    <img class="centered" src="application/asset/img/crashedfrog.png"/>
                    <?php echo $message; ?>
                </div>
                <div class="col-md-2"></div>
            </div>
            <?php include 'application/views/templates/footer.php'; ?>
        </div>
    </body>
</html>