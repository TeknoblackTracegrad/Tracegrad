<?php

/**
 * TRACEGRAD – Alumni Account Activation
 * --------------------------------------------------------
 * Allows a graduate listed in the official TRACEGRAD roster
 * to activate an alumni portal account.
 *
 * Verification:
 * - Student Number
 * - Last Name
 * - Registered Email
 * - New Password
 */

require_once __DIR__ . '/config.php';


/* ============================================================
   ALREADY AUTHENTICATED
============================================================ */

if (!empty($_SESSION['alumni_id'])) {

    header(
        'Location: alumni-dashboard.php'
    );

    exit;
}


/* ============================================================
   PAGE STATE
============================================================ */

$error = '';
$success = '';

$studentId =
    trim(
        (string)(
            $_GET['student_id']
            ?? $_POST['student_id']
            ?? ''
        )
    );


/* ============================================================
   CSRF TOKEN
============================================================ */

if (empty($_SESSION['csrf'])) {

    $_SESSION['csrf'] =
        bin2hex(
            random_bytes(32)
        );
}


/* ============================================================
   ESCAPE HELPER
============================================================ */

function esc_activate($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
 * TRACEGRAD activation comparison helpers.
 * Kept intentionally small for PHP 7.2 / older XAMPP.
 */
function tg_activate_name_key($value)
{
    $value = trim((string)$value);

    /* Convert common imported non-breaking spaces to normal spaces. */
    $value = str_replace("\xC2\xA0", ' ', $value);

    /* Collapse repeated whitespace. */
    $value = preg_replace('/\s+/', ' ', $value);

    if ($value === null) {
        $value = trim((string)$value);
    }

    return strtolower($value);
}


function tg_activate_email_key($value)
{
    $value = trim((string)$value);

    /* Remove normal and common imported spaces from email. */
    $value = str_replace(
        [" ", "\t", "\r", "\n", "\xC2\xA0"],
        '',
        $value
    );

    return strtolower($value);
}


/* ============================================================
   LOAD GRADUATE RECORD
============================================================ */

$graduate = null;


if ($studentId !== '') {

    try {

        $stmt = $pdo->prepare(
            "SELECT
                g.graduate_id,
                g.student_id,
                g.firstname,
                g.lastname,
                g.middlename,
                g.student_email,
                g.personal_email,
                g.batch_year,
                g.account_activation,

                aa.account_id,
                aa.account_status

             FROM graduates g

             LEFT JOIN alumni_accounts aa
                ON aa.graduate_id = g.graduate_id

             WHERE g.student_id = ?

             LIMIT 1"
        );


        $stmt->execute([
            $studentId
        ]);


        $graduate =
            $stmt->fetch();


    } catch (PDOException $e) {

        error_log(
            'TRACEGRAD activation lookup error: ' .
            $e->getMessage()
        );


        $error =
            'Unable to verify your student record right now. Please try again later.';
    }
}


/* ============================================================
   POST – ACTIVATE ACCOUNT
============================================================ */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    $error === ''
) {

    $csrf =
        (string)(
            $_POST['csrf'] ?? ''
        );


    $confirmStudentId =
        trim(
            (string)(
                $_POST['student_id'] ?? ''
            )
        );


    $lastName =
        trim(
            (string)(
                $_POST['lastname'] ?? ''
            )
        );


    $email =
        trim(
            (string)(
                $_POST['email'] ?? ''
            )
        );


    $password =
        (string)(
            $_POST['password'] ?? ''
        );


    $confirmPassword =
        (string)(
            $_POST['confirm_password'] ?? ''
        );


    /* --------------------------------------------------------
       CSRF Validation
    -------------------------------------------------------- */

    if (
        $csrf === '' ||
        !hash_equals(
            (string)$_SESSION['csrf'],
            $csrf
        )
    ) {

        $error =
            'Security validation failed. Please refresh the page and try again.';
    }


    /* --------------------------------------------------------
       Student Number Validation
    -------------------------------------------------------- */

    elseif (
        $studentId === '' ||
        $confirmStudentId === '' ||
        !hash_equals(
            $studentId,
            $confirmStudentId
        )
    ) {

        $error =
            'Invalid activation request. Please start the activation process again.';
    }


    /* --------------------------------------------------------
       Graduate Must Exist
    -------------------------------------------------------- */

    elseif (!$graduate) {

        $error =
            'Student number not found in the official alumni roster.';
    }


    /* --------------------------------------------------------
       Already Activated
    -------------------------------------------------------- */

    elseif (
        ($graduate['account_activation'] ?? '') ===
        'Activated'
    ) {

        $error =
            'This alumni account has already been activated. Please sign in instead.';
    }


    /* --------------------------------------------------------
       Verify Last Name
    -------------------------------------------------------- */

    elseif (
        $lastName === '' ||
        tg_activate_name_key($lastName) !==
        tg_activate_name_key(
            $graduate['lastname'] ?? ''
        )
    ) {

        error_log(
            'TRACEGRAD activation mismatch: lastname; student_id=' .
            $studentId
        );

        $error =
            'The last name does not match the official alumni roster.';
    }


    else {

        /* ----------------------------------------------------
           Verify Registered Email
        ---------------------------------------------------- */

        $registeredEmails = [];


        if (
            !empty(
                $graduate['student_email']
            )
        ) {

            $registeredEmails[] =
                tg_activate_email_key(
                    $graduate['student_email']
                );
        }


        if (
            !empty(
                $graduate['personal_email']
            )
        ) {

            $registeredEmails[] =
                tg_activate_email_key(
                    $graduate['personal_email']
                );
        }


        $registeredEmails =
            array_values(
                array_unique(
                    array_filter(
                        $registeredEmails
                    )
                )
            );


        $submittedEmail =
            tg_activate_email_key(
                $email
            );


        /*
         * Keep activation secure: there must be an email in the
         * official roster, and the submitted email must match it.
         */
        $emailMatches =
            $submittedEmail !== '' &&
            !empty($registeredEmails) &&
            in_array(
                $submittedEmail,
                $registeredEmails,
                true
            );


        if (!$emailMatches) {

            error_log(
                'TRACEGRAD activation mismatch: email; student_id=' .
                $studentId
            );

            if (empty($registeredEmails)) {
                $error =
                    'No registered email is stored in this alumni roster record. Please contact the Department Administrator.';
            } else {
                $error =
                    'The registered email does not match the official alumni roster.';
            }
        }


        /* ----------------------------------------------------
           Password Length
        ---------------------------------------------------- */

        elseif (
            strlen($password) < 8
        ) {

            $error =
                'Your password must contain at least 8 characters.';
        }


        /* ----------------------------------------------------
           Password Confirmation
        ---------------------------------------------------- */

        elseif (
            $password !==
            $confirmPassword
        ) {

            $error =
                'Passwords do not match.';
        }


        /* ----------------------------------------------------
           Existing Account
        ---------------------------------------------------- */

        elseif (
            !empty(
                $graduate['account_id']
            )
        ) {

            $error =
                'An alumni account already exists for this graduate. Please use the Alumni Sign In page.';
        }


        /* ----------------------------------------------------
           Create Account
        ---------------------------------------------------- */

        else {

            try {

                $pdo->beginTransaction();


                /* --------------------------------------------
                   Lock Graduate Record
                -------------------------------------------- */

                $check =
                    $pdo->prepare(
                        "SELECT
                            graduate_id,
                            account_activation

                         FROM graduates

                         WHERE student_id = ?

                         LIMIT 1

                         FOR UPDATE"
                    );


                $check->execute([
                    $studentId
                ]);


                $current =
                    $check->fetch();


                if (!$current) {

                    throw new RuntimeException(
                        'Graduate record no longer exists.'
                    );
                }


                if (
                    $current['account_activation'] !==
                    'Not Activated'
                ) {

                    throw new RuntimeException(
                        'This account has already been activated.'
                    );
                }


                /* --------------------------------------------
                   Prevent Duplicate Alumni Account
                -------------------------------------------- */

                $accountCheck =
                    $pdo->prepare(
                        "SELECT account_id

                         FROM alumni_accounts

                         WHERE graduate_id = ?

                         LIMIT 1

                         FOR UPDATE"
                    );


                $accountCheck->execute([
                    (int)$current['graduate_id']
                ]);


                if (
                    $accountCheck->fetch()
                ) {

                    throw new RuntimeException(
                        'An alumni account already exists.'
                    );
                }


                /* --------------------------------------------
                   Hash Password
                -------------------------------------------- */

                $passwordHash =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                if (
                    $passwordHash === false
                ) {

                    throw new RuntimeException(
                        'Unable to secure the password.'
                    );
                }


                /* --------------------------------------------
                   Create Authentication Account
                -------------------------------------------- */

                $insert =
                    $pdo->prepare(
                        "INSERT INTO alumni_accounts
                            (
                                graduate_id,
                                password,
                                account_status,
                                password_changed_at
                            )

                         VALUES
                            (
                                ?,
                                ?,
                                'Active',
                                NOW()
                            )"
                    );


                $insert->execute([
                    (int)$current['graduate_id'],
                    $passwordHash
                ]);


                /* --------------------------------------------
                   Mark Graduate as Activated
                -------------------------------------------- */

                $update =
                    $pdo->prepare(
                        "UPDATE graduates

                         SET account_activation =
                             'Activated'

                         WHERE graduate_id = ?

                           AND account_activation =
                               'Not Activated'"
                    );


                $update->execute([
                    (int)$current['graduate_id']
                ]);


                if (
                    $update->rowCount() !== 1
                ) {

                    throw new RuntimeException(
                        'Unable to complete account activation.'
                    );
                }


                /* --------------------------------------------
                   Commit
                -------------------------------------------- */

                $pdo->commit();


                /* --------------------------------------------
                   Success
                -------------------------------------------- */

                $success =
                    'Your alumni account has been activated successfully. You can now sign in using your Student Number and new password.';


                $_POST['password'] = '';
                $_POST['confirm_password'] = '';


                /*
                 * Rotate the token after a successful
                 * state-changing request.
                 */

                $_SESSION['csrf'] =
                    bin2hex(
                        random_bytes(32)
                    );


            } catch (Throwable $e) {

                if (
                    $pdo->inTransaction()
                ) {

                    $pdo->rollBack();
                }


                error_log(
                    'TRACEGRAD alumni activation error: ' .
                    $e->getMessage()
                );


                if (
                    $e instanceof RuntimeException
                ) {

                    $error =
                        $e->getMessage();

                } else {

                    $error =
                        'Unable to activate your account right now. Please try again later.';
                }
            }
        }
    }
}


/* ============================================================
   REFRESH GRADUATE AFTER SUCCESS
============================================================ */

if (
    $success !== '' &&
    $studentId !== ''
) {

    try {

        $stmt =
            $pdo->prepare(
                "SELECT
                    graduate_id,
                    firstname,
                    lastname,
                    student_id,
                    batch_year,
                    account_activation

                 FROM graduates

                 WHERE student_id = ?

                 LIMIT 1"
            );


        $stmt->execute([
            $studentId
        ]);


        $graduate =
            $stmt->fetch();


    } catch (PDOException $e) {

        error_log(
            'TRACEGRAD activation refresh error: ' .
            $e->getMessage()
        );
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
        Activate Alumni Account – TRACEGRAD
    </title>


    <!-- Google Fonts -->

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap"
        rel="stylesheet"
    >


    <!-- Tabler Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"
    >


    <!-- Alumni Activation CSS -->

    <link
        rel="stylesheet"
        href="assets/css/alum-activate.css"
    >


    <!-- Alumni Activation JavaScript -->

    <script
        src="assets/js/alum-activate.js"
        defer
    ></script>

</head>


<body>


<div class="activation-page">


    <div class="activation-container">


        <!-- =================================================
             LEFT PANEL
        ================================================== -->

        <section class="activation-left">


            <!-- TRACEGRAD Logo -->

            <div class="activation-logo">

                <img
                    src="assets/images/tracegrad-logo.png"
                    alt="TRACEGRAD Logo"
                >

            </div>


            <!-- School -->

            <div class="school-label">

                ISUFST · San Enrique Campus

            </div>


            <!-- Title -->

            <h1>

                TRACE<em>GRAD</em>

                <br>

                Account Activation

            </h1>


            <!-- Description -->

            <p class="left-description">

                Activate your verified TRACEGRAD alumni
                portal account using information registered
                in the official ISUFST graduate roster.

            </p>


            <!-- Features -->

            <div class="feature-list">


                <div class="feature-item">

                    <div class="feature-icon">

                        <i class="ti ti-shield-check"></i>

                    </div>

                    <span>

                        Verified against the official alumni roster

                    </span>

                </div>


                <div class="feature-item">

                    <div class="feature-icon">

                        <i class="ti ti-lock"></i>

                    </div>

                    <span>

                        Secure password-protected alumni account

                    </span>

                </div>


                <div class="feature-item">

                    <div class="feature-icon">

                        <i class="ti ti-user-graduate"></i>

                    </div>

                    <span>

                        One TRACEGRAD account per graduate

                    </span>

                </div>


                <div class="feature-item">

                    <div class="feature-icon">

                        <i class="ti ti-database-check"></i>

                    </div>

                    <span>

                        Identity details checked against roster data

                    </span>

                </div>


            </div>


            <!-- Footer -->

            <div class="activation-left-footer">

                Account activation is available only
                to verified ISUFST graduate roster members

            </div>


        </section>



        <!-- =================================================
             RIGHT PANEL
        ================================================== -->

        <section class="activation-right">


            <!-- Back -->

            <a
                class="back-link"
                href="alum-login.php"
            >

                <i class="ti ti-arrow-left"></i>

                Back to Alumni Login

            </a>



            <div class="activation-content">


                <!-- Heading -->

                <div class="activation-heading">


                    <span class="activation-heading-icon">

                        <i class="ti ti-user-plus"></i>

                    </span>


                    <div>

                        <h2>

                            Activate Your Account

                        </h2>

                        <p>

                            Set up your secure TRACEGRAD access

                        </p>

                    </div>


                </div>



                <p class="activation-description">

                    Verify your graduate record and create
                    your alumni portal password.

                </p>



                <!-- Error -->

                <?php if ($error !== ''): ?>

                    <div
                        class="activation-alert activation-alert-error"
                        role="alert"
                    >

                        <i class="ti ti-alert-circle"></i>

                        <span>

                            <?= esc_activate(
                                $error
                            ) ?>

                        </span>

                    </div>

                <?php endif; ?>



                <!-- Success -->

                <?php if ($success !== ''): ?>

                    <div
                        class="activation-alert activation-alert-success"
                        role="status"
                    >

                        <i class="ti ti-circle-check"></i>

                        <span>

                            <?= esc_activate(
                                $success
                            ) ?>

                        </span>

                    </div>

                <?php endif; ?>



                <!-- =========================================
                     SUCCESS STATE
                ========================================== -->

                <?php if ($success !== ''): ?>


                    <div class="success-panel">

                        <div class="success-icon">

                            <i class="ti ti-circle-check-filled"></i>

                        </div>


                        <h3>

                            Account Activated

                        </h3>


                        <p>

                            Your TRACEGRAD alumni account
                            is ready to use.

                        </p>


                        <a
                            class="primary-button"
                            href="alum-login.php"
                        >

                            <i class="ti ti-login"></i>

                            <span>

                                Go to Alumni Sign In

                            </span>

                        </a>

                    </div>



                <!-- =========================================
                     STUDENT NUMBER LOOKUP
                ========================================== -->

                <?php elseif (!$graduate): ?>


                    <form
                        method="get"
                        autocomplete="off"
                        class="activation-form"
                        id="lookup-form"
                    >


                        <div class="form-group">

                            <label for="student-id">

                                Student Number

                            </label>


                            <div class="input-wrapper">


                                <span class="input-icon">

                                    <i class="ti ti-id"></i>

                                </span>


                                <input
                                    type="text"
                                    id="student-id"
                                    name="student_id"
                                    placeholder="e.g. 2024-00142"
                                    value="<?= esc_activate(
                                        $studentId
                                    ) ?>"
                                    autocomplete="username"
                                    maxlength="30"
                                    required
                                    autofocus
                                >


                            </div>


                            <p class="field-hint">

                                Enter the Student Number
                                recorded in the official
                                ISUFST graduate roster.

                            </p>

                        </div>


                        <button
                            class="primary-button"
                            id="lookup-button"
                            type="submit"
                        >

                            <i class="ti ti-search"></i>

                            <span>

                                Find My Graduate Record

                            </span>

                        </button>


                    </form>



                <!-- =========================================
                     ALREADY ACTIVATED
                ========================================== -->

                <?php elseif (
                    ($graduate['account_activation'] ?? '') ===
                    'Activated'
                ): ?>


                    <div class="already-activated-panel">


                        <div class="already-icon">

                            <i class="ti ti-shield-check"></i>

                        </div>


                        <h3>

                            Account Already Activated

                        </h3>


                        <p>

                            This Student Number already has
                            an activated TRACEGRAD alumni account.

                        </p>


                        <a
                            class="primary-button"
                            href="alum-login.php"
                        >

                            <i class="ti ti-login"></i>

                            <span>

                                Go to Alumni Sign In

                            </span>

                        </a>


                    </div>



                <!-- =========================================
                     ACTIVATION FORM
                ========================================== -->

                <?php else: ?>


                    <!-- Graduate Summary -->

                    <div class="graduate-summary">


                        <div class="graduate-summary-icon">

                            <i class="ti ti-user-check"></i>

                        </div>


                        <div class="graduate-summary-info">


                            <span class="graduate-label">

                                Graduate record found

                            </span>


                            <strong>

                                <?= esc_activate(
                                    trim(
                                        $graduate['firstname'] .
                                        ' ' .
                                        $graduate['lastname']
                                    )
                                ) ?>

                            </strong>


                            <div class="graduate-meta">

                                <span>

                                    <i class="ti ti-id"></i>

                                    <?= esc_activate(
                                        $graduate['student_id']
                                    ) ?>

                                </span>


                                <span>

                                    <i class="ti ti-calendar"></i>

                                    Batch
                                    <?= esc_activate(
                                        $graduate['batch_year']
                                    ) ?>

                                </span>

                            </div>


                        </div>


                    </div>



                    <form
                        method="post"
                        autocomplete="off"
                        class="activation-form"
                        id="activation-form"
                    >


                        <!-- CSRF -->

                        <input
                            type="hidden"
                            name="csrf"
                            value="<?= esc_activate(
                                $_SESSION['csrf']
                            ) ?>"
                        >


                        <!-- Student ID -->

                        <input
                            type="hidden"
                            name="student_id"
                            value="<?= esc_activate(
                                $studentId
                            ) ?>"
                        >



                        <!-- Last Name -->

                        <div class="form-group">

                            <label for="lastname">

                                Last Name

                            </label>


                            <div class="input-wrapper">


                                <span class="input-icon">

                                    <i class="ti ti-user"></i>

                                </span>


                                <input
                                    type="text"
                                    id="lastname"
                                    name="lastname"
                                    placeholder="Enter your last name"
                                    value="<?= esc_activate(
                                        $_POST['lastname'] ?? ''
                                    ) ?>"
                                    autocomplete="family-name"
                                    maxlength="100"
                                    required
                                >


                            </div>

                        </div>



                        <!-- Email -->

                        <div class="form-group">

                            <label for="email">

                                Registered Email

                            </label>


                            <div class="input-wrapper">


                                <span class="input-icon">

                                    <i class="ti ti-mail"></i>

                                </span>


                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="Enter your registered email"
                                    value="<?= esc_activate(
                                        $_POST['email'] ?? ''
                                    ) ?>"
                                    autocomplete="email"
                                    maxlength="150"
                                    required
                                >


                            </div>


                            <p class="field-hint">

                                Use your student or personal email
                                registered in the official alumni roster.

                            </p>

                        </div>



                        <!-- Password -->

                        <div class="form-group">

                            <label for="password">

                                Create Password

                            </label>


                            <div
                                class="input-wrapper password-wrapper"
                            >


                                <span class="input-icon">

                                    <i class="ti ti-lock"></i>

                                </span>


                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Minimum 8 characters"
                                    autocomplete="new-password"
                                    minlength="8"
                                    required
                                >


                                <button
                                    class="password-toggle"
                                    type="button"
                                    aria-label="Show password"
                                    data-password-toggle="password"
                                >

                                    <i class="ti ti-eye"></i>

                                </button>


                            </div>

                        </div>



                        <!-- Confirm Password -->

                        <div class="form-group">

                            <label for="confirm-password">

                                Confirm Password

                            </label>


                            <div
                                class="input-wrapper password-wrapper"
                            >


                                <span class="input-icon">

                                    <i class="ti ti-lock-check"></i>

                                </span>


                                <input
                                    type="password"
                                    id="confirm-password"
                                    name="confirm_password"
                                    placeholder="Re-enter your password"
                                    autocomplete="new-password"
                                    minlength="8"
                                    required
                                >


                                <button
                                    class="password-toggle"
                                    type="button"
                                    aria-label="Show password"
                                    data-password-toggle="confirm-password"
                                >

                                    <i class="ti ti-eye"></i>

                                </button>


                            </div>


                            <p
                                class="password-match-message"
                                id="password-match-message"
                                aria-live="polite"
                            ></p>

                        </div>



                        <!-- Activate -->

                        <button
                            class="primary-button"
                            id="activation-button"
                            type="submit"
                        >

                            <i class="ti ti-user-check"></i>

                            <span>

                                Activate My Account

                            </span>

                        </button>


                    </form>


                <?php endif; ?>



                <!-- Security Note -->

                <div class="security-note">

                    <i class="ti ti-shield-lock"></i>

                    <span>

                        Your activation details are used only
                        to verify your official graduate record.

                    </span>

                </div>


            </div>


        </section>


    </div>


</div>


</body>

</html>