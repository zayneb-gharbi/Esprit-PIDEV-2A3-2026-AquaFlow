<?php
// Views/pages/signin.php : page de connexion + creation de compte (HTML pur)
// variables : $activeView ('login'|'register'), $message, $messageType, $oldIdentifier
$activeView    = $activeView ?? 'login';
$message       = $message ?? '';
$messageType   = $messageType ?? 'error';
$oldIdentifier = $oldIdentifier ?? '';
?>
<main class="page-container">

        <!-- LEFT SIDE -->
        <section class="visual-section">

            <div class="brand">
                <img src="app/Views/assets/images/logo.png" alt="Aqua Flow Logo">
            </div>

            <div class="visual-content">
                <span class="eyebrow">GESTION INTELLIGENTE DE L'EAU</span>

                <h1>Chaque goutte<br>compte<span>.</span></h1>

                <p>
                    Gérez les ressources en eau, signalez les problèmes de plomberie
                    et contribuez à un avenir plus durable.
                </p>

                <div class="water-illustration">
                    <div class="water-circle circle-one"></div>
                    <div class="water-circle circle-two"></div>
                    <div class="water-circle circle-three"></div>

                    <div class="water-label">
                        <span class="water-dot"></span>
                        L'EAU, C'EST LA VIE
                    </div>
                </div>
            </div>

            <div class="visual-footer">
                <span>© 2026 AQUA FLOW</span>
                <span>Économiser l'eau, sécuriser demain.</span>
            </div>

        </section>


        <!-- RIGHT SIDE -->
        <section class="auth-section">

            <div class="auth-wrapper">

                <!-- LOGIN FORM -->
                <div class="form-view <?= $activeView == 'login' ? 'active' : '' ?>" id="loginView">

                    <div class="form-heading">
                        <span class="welcome-tag">BON RETOUR</span>
                        <h2>Connectez-vous à votre compte</h2>
                        <p>Saisissez vos identifiants pour continuer.</p>
                    </div>

                    <form id="loginForm" action="index.php?page=signin" method="POST" novalidate>

                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                        <div class="form-group">
                            <label for="loginIdentifier">
                                E-mail ou téléphone
                            </label>

                            <input
                                type="text"
                                id="loginIdentifier"
                                name="identifiant"
                                value="<?= e($oldIdentifier) ?>"
                                placeholder="Saisissez votre e-mail ou téléphone"
                            >
                        </div>

                        <div class="form-group">

                            <div class="label-row">
                                <label for="loginPassword">Mot de passe</label>
                            </div>

                            <div class="password-wrapper">
                                <input
                                    type="password"
                                    id="loginPassword"
                                    name="password"
                                    placeholder="Saisissez votre mot de passe"
                                >

                                <button type="button"
                                    class="toggle-password"
                                    data-target="loginPassword">
                                    Afficher
                                </button>
                            </div>

                        </div>

                        <button type="submit" class="primary-btn">
                            Se connecter <span>→</span>
                        </button>

                    </form>

                    <div class="form-divider">
                        <span>OU</span>
                    </div>

                    <p class="switch-text">
                        Vous n'avez pas de compte ?

                        <button type="button"
                            class="text-btn"
                            id="showRegister">
                            Créer un compte
                        </button>
                    </p>

                    <div class="security-note">
                        <span>◆</span>
                        Vos informations sont protégées et sécurisées.
                    </div>

                </div>


                <!-- REGISTRATION FORM -->
                <div class="form-view <?= $activeView == 'register' ? 'active' : '' ?>" id="registerView">

                    <div class="form-heading">
                        <span class="welcome-tag">REJOIGNEZ AQUA FLOW</span>
                        <h2>Créez votre compte</h2>
                        <p>Faites la différence, une goutte à la fois.</p>
                    </div>

                    <form id="registerForm" action="index.php?page=signup" method="POST" novalidate>

                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                        <!-- ROLE SELECTION -->
                        <label class="role-label">
                            Je souhaite m'inscrire en tant que :
                        </label>

                        <div class="role-options">

                            <label class="role-card selected">

                                <input type="radio"
                                    name="role"
                                    value="civilian"
                                    checked>

                                <span class="role-icon">♙</span>

                                <span class="role-name">
                                    Citoyen
                                </span>

                                <span class="role-description">
                                    Signaler des problèmes
                                </span>

                            </label>

                            <label class="role-card">

                                <input type="radio"
                                    name="role"
                                    value="plumber">

                                <span class="role-icon">⚒</span>

                                <span class="role-name">
                                    Plombier
                                </span>

                                <span class="role-description">
                                    Gérer les interventions
                                </span>

                            </label>

                        </div>


                        <!-- FIRST NAME AND LAST NAME -->
                        <div class="form-grid">

                            <div class="form-group">
                                <label for="firstName">
                                    Prénom
                                </label>

                                <input
                                    type="text"
                                    id="firstName"
                                    name="first_name"
                                    placeholder="Prénom"
                                >
                            </div>

                            <div class="form-group">
                                <label for="lastName">
                                    Nom
                                </label>

                                <input
                                    type="text"
                                    id="lastName"
                                    name="last_name"
                                    placeholder="Nom"
                                >
                            </div>

                        </div>


                        <!-- EMAIL -->
                        <div class="form-group">

                            <label for="registerEmail">
                                Adresse e-mail
                            </label>

                            <input
                                type="text"
                                id="registerEmail"
                                name="email"
                                placeholder="example@email.com"
                            >

                        </div>


                        <!-- PHONE -->
                        <div class="form-group">

                            <label for="registerPhone">
                                Numéro de téléphone
                            </label>

                            <input
                                type="text"
                                id="registerPhone"
                                name="phone"
                                placeholder="+216 XX XXX XXX"
                            >

                        </div>


                        <!-- PLUMBER SPECIFIC FIELDS -->
                        <div id="plumberFields" class="hidden">

                            <!-- GOVERNORATE DROPDOWN -->
                            <div class="form-group">

                                <label for="serviceArea">
                                    Zone de service
                                </label>

                                <select id="serviceArea" name="service_area">

                                    <option value="">
                                        Sélectionnez votre gouvernorat
                                    </option>

                                    <option value="Tunis">Tunis</option>
                                    <option value="Ariana">Ariana</option>
                                    <option value="Ben Arous">Ben Arous</option>
                                    <option value="Manouba">Manouba</option>
                                    <option value="Nabeul">Nabeul</option>
                                    <option value="Zaghouan">Zaghouan</option>
                                    <option value="Bizerte">Bizerte</option>
                                    <option value="Beja">Beja</option>
                                    <option value="Jendouba">Jendouba</option>
                                    <option value="Le Kef">Le Kef</option>
                                    <option value="Siliana">Siliana</option>
                                    <option value="Sousse">Sousse</option>
                                    <option value="Monastir">Monastir</option>
                                    <option value="Mahdia">Mahdia</option>
                                    <option value="Sfax">Sfax</option>
                                    <option value="Kairouan">Kairouan</option>
                                    <option value="Kasserine">Kasserine</option>
                                    <option value="Sidi Bouzid">Sidi Bouzid</option>
                                    <option value="Gabes">Gabes</option>
                                    <option value="Medenine">Medenine</option>
                                    <option value="Tataouine">Tataouine</option>
                                    <option value="Gafsa">Gafsa</option>
                                    <option value="Tozeur">Tozeur</option>
                                    <option value="Kebili">Kebili</option>

                                </select>

                            </div>


                            <!-- EXPERIENCE -->
                            <div class="form-group">

                                <label for="experience">
                                    Années d'expérience
                                </label>

                                <input
                                    type="text"
                                    id="experience"
                                    name="experience"
                                    placeholder="ex. 5"
                                >

                            </div>

                        </div>


                        <!-- PASSWORD -->
                        <div class="form-group">

                            <label for="registerPassword">
                                Mot de passe
                            </label>

                            <div class="password-wrapper">

                                <input
                                    type="password"
                                    id="registerPassword"
                                    name="password"
                                    placeholder="Créez un mot de passe"
                                >

                                <button type="button"
                                    class="toggle-password"
                                    data-target="registerPassword">
                                    Afficher
                                </button>

                            </div>

                        </div>


                        <!-- CONFIRM PASSWORD -->
                        <div class="form-group">

                            <label for="confirmPassword">
                                Confirmer le mot de passe
                            </label>

                            <input
                                type="password"
                                id="confirmPassword"
                                placeholder="Confirmez votre mot de passe"
                            >

                        </div>


                        <button type="submit" class="primary-btn">
                            Créer un compte <span>→</span>
                        </button>

                    </form>


                    <p class="switch-text register-switch">

                        Vous avez déjà un compte ?

                        <button type="button"
                            class="text-btn"
                            id="showLogin">
                            Se connecter
                        </button>

                    </p>

                </div>


                <!-- VALIDATION MESSAGES -->
                <div id="message"
                    class="message <?= $message !== '' ? e($messageType) : '' ?>"
                    role="status"
                    aria-live="polite"><?= e($message) ?></div>

            </div>

        </section>

    </main>
