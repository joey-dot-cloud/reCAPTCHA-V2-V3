<?php
$key_public_v3 = "";
$key_private_v3 = "";

$key_public_v2 = "";
$key_private_v2 = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response']) && isset($_POST['submit_v3']))
{
    $recaptcha_v3_response = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $key_private_v3 . '&response=' . $_POST['recaptcha_response']));
    if($recaptcha_v3_response->success == true)
    {
        $recaptcha_v3_success = $recaptcha_v3_response->success;
        $recaptcha_v3_challenge_ts = $recaptcha_v3_response->challenge_ts;
        $recaptcha_v3_hostname = $recaptcha_v3_response->hostname;
        $recaptcha_v3_score = $recaptcha_v3_response->score;
        $recaptcha_v3_action = $recaptcha_v3_response->action;
        if(isset($_POST['force_score'])) { $recaptcha_v3_score = 0; }
        if ($recaptcha_v3_score >= 0.5)
        {
            $status_v3 = true;
        }
        else
        {
            $status_v3 = false;
        }
    }
    else
    {
        $status_v3 = false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['g-recaptcha-response']) && isset($_POST['submit_v2']))
{
    $recaptcha_v2_response = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $key_private_v2 . '&response=' . $_POST['g-recaptcha-response']));
    if($recaptcha_v2_response->success == true)
    {
        var_dump($recaptcha_v2_response);
        $recaptcha_v2_success = $recaptcha_v2_response->success;
        $recaptcha_v2_challenge_ts = $recaptcha_v2_response->challenge_ts;
        $recaptcha_v2_hostname = $recaptcha_v2_response->hostname;
        $status_v2 = true;
    }
    else
    {
        $status_v2 = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $key_public_v3; ?>"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('<?php echo $key_public_v3; ?>', { action: 'form' }).then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
            });
        });
    </script>
</head>
<body>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'):
        if(isset($status_v3) && $status_v3 == true):

            echo "Status: V3 send request and verified<br>";
            echo "success: " . $recaptcha_v3_success . "<br>";
            echo "challenge_ts: " . $recaptcha_v3_challenge_ts . "<br>";
            echo "hostname: " . $recaptcha_v3_hostname . "<br>";
            echo "score: " . $recaptcha_v3_score . "<br>";
            echo "action: " . $recaptcha_v3_action;
            else:
?>
    <script src='https://www.google.com/recaptcha/api.js' async defer></script>
    <form method="POST" name="v2">
        <h1>reCAPTCHA V2</h1>
        <div class="g-recaptcha" data-sitekey="<?php echo $key_public_v2; ?>"></div>
        <button type="submit" name="submit_v2">Send Message</button>
    </form>
<?php
endif;
        if(isset($status_v2) && $status_v2 == true):
            echo "Status: V2 send request and verified<br>";
            echo "success: " . $recaptcha_v2_success . "<br>";
            echo "challenge_ts: " . $recaptcha_v2_challenge_ts . "<br>";
            echo "hostname: " . $recaptcha_v2_hostname;
            elseif(isset($status_v2) && $status_v2 == false):
            echo "V2 not successful";
            endif;
else:
?>
    <form method="POST">
        <h1>reCAPTCHA V3</h1>
        <input type="checkbox" name="force_score"> Force 0 score<br>
        <button type="submit" name="submit_v3">Send Message</button>
        <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
    </form>
<?php
endif;
?>
</body>
</html>