<?php

/**
 * TRACEGRAD – Administrator Login
 * --------------------------------------------------------
 * Authentication page for Super Admin and
 * College/Department Admin accounts.
 */

require_once __DIR__ . '/config.php';


$error = '';


/* ============================================================
   LOGIN REQUEST
============================================================ */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username =
        trim(
            $_POST['username'] ?? ''
        );

    $password =
        $_POST['password'] ?? '';

    $ip =
        $_SERVER['REMOTE_ADDR'] ?? '';

    $userAgent =
        $_SERVER['HTTP_USER_AGENT'] ?? '';


    /* --------------------------------------------------------
       Validate Input
    -------------------------------------------------------- */

    if (
        $username === '' ||
        $password === ''
    ) {

        $error =
            'Please enter both username and password.';

    } else {

        /* ----------------------------------------------------
           Find Active Administrator
        ---------------------------------------------------- */

        $stmt = $pdo->prepare(
            "SELECT
                a.*,
                r.role_name,
                a.college_id

             FROM admins a

             JOIN roles r
                ON r.role_id = a.role_id

             WHERE a.username = ?
               AND a.status = 'Active'

             LIMIT 1"
        );


        $stmt->execute([
            $username
        ]);


        $admin =
            $stmt->fetch();


        /* ----------------------------------------------------
           Verify Password
        ---------------------------------------------------- */

        $loginSuccessful =
            $admin &&
            password_verify(
                $password,
                $admin['password']
            );


        /* ----------------------------------------------------
           Record Login Attempt
        ---------------------------------------------------- */

        $logAttempt = $pdo->prepare(
            "INSERT INTO login_attempts
                (
                    user_type,
                    username,
                    login_status,
                    ip_address,
                    user_agent
                )

             VALUES
                (
                    'Admin',
                    ?,
                    ?,
                    ?,
                    ?
                )"
        );


        $logAttempt->execute([
            $username,
            $loginSuccessful
                ? 'Success'
                : 'Failed',
            $ip,
            $userAgent
        ]);


        /* ----------------------------------------------------
           Successful Login
        ---------------------------------------------------- */

        if ($loginSuccessful) {

            /*
             * Regenerate the session ID after authentication
             * to reduce session fixation risk.
             */

            session_regenerate_id(true);


            /* ------------------------------------------------
               Store Admin Session
            ------------------------------------------------ */

            $_SESSION['admin_id'] =
                (int)$admin['admin_id'];


            $_SESSION['admin_name'] =
                $admin['fullname'];


            $_SESSION['admin_role'] =
                $admin['role_name'];


            $_SESSION['role_id'] =
                (int)$admin['role_id'];


            $_SESSION['admin_college_id'] =
                (int)(
                    $admin['college_id'] ?? 0
                );


            /* ------------------------------------------------
               Update Last Login
            ------------------------------------------------ */

            $updateLastLogin =
                $pdo->prepare(
                    "UPDATE admins

                     SET last_login = NOW()

                     WHERE admin_id = ?"
                );


            $updateLastLogin->execute([
                $admin['admin_id']
            ]);


            /* ------------------------------------------------
               Redirect Based on Role
            ------------------------------------------------ */

            if (
                (int)$admin['role_id'] === 2
            ) {

                /*
                 * Department / College Admin
                 */

                header(
                    'Location: dept-admin-dashboard.php'
                );

            } else {

                /*
                 * Super Admin
                 */

                header(
                    'Location: admin-dashboard.php'
                );
            }


            exit;
        }


        /* ----------------------------------------------------
           Invalid Credentials
        ---------------------------------------------------- */

        $error =
            'Incorrect username or password.';
    }
}

?>

<!DOCTYPE html>

<html lang="en">


<head>

    <meta charset="UTF-8">


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >


    <title>
        Admin Login – TRACEGRAD
    </title>


    <!-- =====================================================
         GOOGLE FONTS
    ====================================================== -->

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap"
        rel="stylesheet"
    >


    <!-- =====================================================
         TABLER ICONS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"
    >


    <!-- =====================================================
         ADMIN LOGIN CSS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="assets/css/admin-login.css"
    >


    <!-- =====================================================
         ADMIN LOGIN JAVASCRIPT
    ====================================================== -->

    <script
        src="assets/js/admin-login.js"
        defer
    ></script>


</head>


<body>


<div class="login-page">


    <div class="login-container">


        <!-- =================================================
             LEFT PANEL
        ================================================== -->

        <section class="login-left">


            <!-- TRACEGRAD LOGO -->

            <div class="admin-logo">

                <img
                    src="assets/images/tracegrad-logo.png"
                    alt="TRACEGRAD Logo"
                >

            </div>



            <!-- SCHOOL -->

            <div class="school-label">

                ISUFST · San Enrique Campus

            </div>



            <!-- TITLE -->

            <h1>

                TRACE<em>GRAD</em>

                <br>

                Admin Portal

            </h1>



            <!-- DESCRIPTION -->

            <p class="left-description">

                Secure administrative access for
                Super Administrators and
                College/Department Administrators
                of Iloilo State University of
                Fisheries Science and Technology –
                San Enrique Campus.

            </p>



            <!-- FEATURES -->

            <div class="feature-list">


                <!-- SUPER ADMIN -->

                <div class="feature-item">

                    <div class="feature-icon">

                        <i class="ti ti-shield-star"></i>

                    </div>

                    <span>

                        Super Admin —
                        system-wide full access

                    </span>

                </div>



                <!-- DEPARTMENT ADMIN -->

                <div class="feature-item">

                    <div class="feature-icon">

                        <i class="ti ti-building"></i>

                    </div>

                    <span>

                        Dept Admin —
                        college-level management

                    </span>

                </div>



                <!-- ANALYTICS -->

                <div class="feature-item">

                    <div class="feature-icon">

                        <i class="ti ti-chart-bar"></i>

                    </div>

                    <span>

                        Employment analytics
                        & CHED reports

                    </span>

                </div>



                <!-- ROSTER -->

                <div class="feature-item">

                    <div class="feature-icon">

                        <i class="ti ti-users"></i>

                    </div>

                    <span>

                        Full alumni roster
                        CRUD management

                    </span>

                </div>


            </div>



            <!-- FOOTER -->

            <div class="login-left-footer">

                Iloilo State University of Fisheries
                Science and Technology —
                San Enrique Campus

                <br>

                TRACEGRAD v3.0

            </div>


        </section>



        <!-- =================================================
             RIGHT PANEL
        ================================================== -->

        <section class="login-right">


            <!-- BACK BUTTON -->

            <button
                class="back-button"
                type="button"
                onclick="location.href='index.php'"
            >

                <i class="ti ti-arrow-left"></i>

                Back to Home

            </button>



            <div class="login-content">


                <!-- =========================================
                     LOGIN HEADING
                ========================================== -->

                <div class="login-heading">


                    <span class="login-heading-icon">

                        <i class="ti ti-shield-lock"></i>

                    </span>


                    <div>

                        <h2>

                            Administrator Sign In

                        </h2>


                        <p>

                            Secure TRACEGRAD
                            administrative access

                        </p>

                    </div>


                </div>



                <!-- DESCRIPTION -->

                <p class="login-description">

                    Enter the username and password
                    issued to you by the ISUFST
                    Alumni Affairs Office.

                </p>



                <!-- =========================================
                     ERROR MESSAGE
                ========================================== -->

                <?php if ($error !== ''): ?>


                    <div
                        class="login-alert"
                        role="alert"
                    >


                        <i class="ti ti-alert-circle"></i>


                        <span>

                            <?= htmlspecialchars(
                                $error,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </span>


                    </div>


                <?php endif; ?>



                <!-- =========================================
                     LOGIN FORM
                ========================================== -->

                <form
                    method="post"
                    autocomplete="off"
                    class="login-form"
                >


                    <!-- =====================================
                         USERNAME
                    ====================================== -->

                    <div class="form-group">


                        <label for="admin-username">

                            Username

                        </label>



                        <div class="input-wrapper">


                            <span class="input-icon">

                                <i class="ti ti-user"></i>

                            </span>



                            <input
                                type="text"
                                id="admin-username"
                                name="username"
                                placeholder="Enter your username"
                                value="<?= htmlspecialchars(
                                    $_POST['username'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                autocomplete="username"
                                maxlength="100"
                                required
                                autofocus
                            >


                        </div>


                    </div>



                    <!-- =====================================
                         PASSWORD
                    ====================================== -->

                    <div class="form-group">


                        <label for="admin-password">

                            Password

                        </label>



                        <div
                            class="input-wrapper password-wrapper"
                        >


                            <span class="input-icon">

                                <i class="ti ti-lock"></i>

                            </span>



                            <input
                                type="password"
                                id="admin-password"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >



                            <button
                                class="password-toggle"
                                type="button"
                                aria-label="Show password"
                                onclick="togglePassword(
                                    'admin-password',
                                    this
                                )"
                            >

                                <i class="ti ti-eye"></i>

                            </button>


                        </div>


                    </div>



                    <!-- =====================================
                         LOGIN BUTTON
                    ====================================== -->

                    <button
                        class="login-button"
                        type="submit"
                    >

                        <i class="ti ti-login"></i>

                        <span>

                            Sign In as Administrator

                        </span>

                    </button>


                </form>



                <!-- =========================================
                     SECURITY NOTE
                ========================================== -->

                <div class="security-note">


                    <i class="ti ti-lock-check"></i>


                    <span>

                        This portal is restricted to
                        authorized ISUFST administrators
                        only.

                    </span>


                </div>


            </div>


        </section>


    </div>


</div>


</body>


</html>