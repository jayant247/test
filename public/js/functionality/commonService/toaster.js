
function showToast(type, title='', message) {
    let toastOptions = {
        timeOut:5e3,
        closeButton:!0,
        newestOnTop:!0,
        progressBar:!0,
        positionClass:"toast-top-right",
        preventDuplicates:!0,
        onclick:null,
        showDuration:"1000",
        hideDuration:"1000",
        extendedTimeOut:"1000",
        showEasing:"swing",
        hideEasing:"linear",
        showMethod:"fadeIn",
        hideMethod:"fadeOut",
        tapToDismiss:!1
    }
    switch (type.toLowerCase()) {
        case 'success':
            toastr.success(message,title,toastOptions);
            break;
        case 'error':
            toastr.error(message,title,toastOptions);
            break;
        case 'warning':
            toastr.warning(message,title,toastOptions);
            break;
        case 'info':
            toastr.info(message,title,toastOptions);
            break;


    }
}
