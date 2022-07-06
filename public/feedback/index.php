<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://turkey.servicesadvisor.org/feedback/js/jquery.feedback_me.js?v=1222"></script>
<link rel="stylesheet" type="text/css" href="https://turkey.servicesadvisor.org/feedback/css/jquery.feedback_me.css?v=1222">
</head>
<body>

<script type="text/javascript">
$(document).ready(function () {
    
    var langs = ["en", "tr", "ar", "ku", "fa", "ps"];
    var fullUrl = window.location.origin;
    var urlArr = fullUrl.split("/");
    var lang = urlArr[3];

    if(!langs.includes(lang))
        lang = "en";

    fm_options = {
        show_email: false,
        email_required: false, 
        show_radio_button_list: true,
        radio_button_list_required: true,
        radio_button_list_title: "1. Please rate Services Advisor platform (1 bad - 5 very good) ", 
        name_placeholder: "Name ",
        email_placeholder: "Email ",
        message_placeholder: "Please enter your feedback ", 
        name_required: false,
        message_required: false, 
        show_asterisk_for_required: true, 
        feedback_url: "https://turkey.servicesadvisor.org/feedback/send_feedback.php", 
        lang: lang,
        custom_params: {
            csrf: "fasdfdsff8987f77f6555fa87f7fahhhj",
            user_id: "fd",
            feedback_type: "clean_complex"
        },
        delayed_options: {
            delay_success_milliseconds: 3500,
            send_success : "Feedback sent successfully, thank you!"
        }
    }; 
    fm.init(fm_options); 
});
</script>

</body>
</html>
