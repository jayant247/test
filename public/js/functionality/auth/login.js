// import { ajaxCall } from "../commonService/ajaxcall";

$(document).ready(function() {

    function checkLogin(){
        if(localStorage.getItem('token')){
            window.location.href = "/resetAll";
        }else{

        }
    }
    checkLogin();

    $("#login-form").validate({
        errorClass:"error animated fadeInDown",
        rules: {
            // The key name on the left side is the name attribute
            // of an input field. Validation rules are defined
            // on the right side

            email: {
                required: true,
                // Specify that email should be validated
                // by the built-in "email" rule
                email: true
            },
            password: {
                required: true,
                minlength: 5
            }
        },
        // Specify validation error messages
        messages: {
            password: {
                required: "Please provide a password",
                minlength: "Your password must be at least 6 characters long"
            },
            email: "Please enter a valid email address"
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function (form) {
            let dataToSend = {}
            dataToSend['url']='auth/loginWithEmailPassword';
            dataToSend['requestType']='POST';
            dataToSend['data']={
                'email': $('#email').val(),
                'password': $('#password').val(),
            };
            dataToSend['successCallbackFunction'] = onLoginSuccess;
            ajaxCall(dataToSend);
            return false;
        }
    });


     function onLoginSuccess(data) {
        if(data['success']){
            let token = data['data']['token'];
            let userData = data['data']['userData'];
            localStorage.setItem('token',token);
            localStorage.setItem('userData',JSON.stringify(userData));
            document.cookie = "Authorization:";
            showToast('success','Success',data['message']);
        }else{
            showToast('error','Error',data['message']);
        }
    }
});




