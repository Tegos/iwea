<?php echo $header; ?>

<main class="main-content">

    <div class="container">
        <div class="breadcrumb">
            <a href="/">Головна</a>
            <span>Авторизація</span>
        </div>
    </div>

    <?php if ($result): ?>
    <div class="container">
        <div class="alert alert-success">
            <div class="pull-right">
                <p class="close">&times;</p>
            </div>
            <strong>Success!</strong>
            This alert box could indicate a successful or positive action.
        </div>
    </div>
    <?php endif; ?>


    <div class="fullwidth-block">
        <div class="container">

            <div class="col-md-6">
                <h2 class="section-title">Авторизація</h2>
                <p>Для продовження введіть Ваш e-mail та пароль, вказані при реєстрації.
                    Якщо Ви ще не реєструвались, натисніть "Зареєструватись".</p>
                <form action="/?action=auth" method="post" class="contact-form">
                    <div class="row">
                        <div class="col-md-8">
                            <input name="email" type="email" placeholder="Ваш e-mail..."></div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <input name="pass" type="password" placeholder="Ваш пароль..."></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <input type="submit" value="Далі">
                        </div>
                        <div class="col-md-4 text-right">
                            <a class="button-reg" href="/?action=reg">Зареєструватись</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>


</main>

<?php echo $footer; ?>