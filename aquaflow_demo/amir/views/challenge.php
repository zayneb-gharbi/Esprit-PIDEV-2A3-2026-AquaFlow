<section class="page-title"><p class="eyebrow">VÉRIFICATION DE SÉCURITÉ</p><h1>Confirmer votre connexion</h1><p>Saisissez le code affiché dans Google Authenticator.</p></section>
<div class="card form-card">
    <form method="post" action="index.php?page=challenge">
        <label for="code">Code à 6 chiffres ou code de secours</label><input id="code" name="code" type="text" autocomplete="one-time-code" maxlength="16" pattern="([0-9]{6}|[a-f0-9]{16})" required autofocus>
        <p class="help">Un code de secours ne fonctionne qu’une seule fois.</p>
        <div class="form-actions"><a class="btn-light" href="index.php?page=login">Revenir à la connexion</a><button class="btn" type="submit">Vérifier</button></div>
    </form>
</div>
