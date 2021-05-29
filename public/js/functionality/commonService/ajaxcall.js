// import { showToast } from "./toaster";

String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

var doAjax_params_default = {
    'url': null,
    'requestType': "GET",
    'contentType': 'application/x-www-form-urlencoded; charset=UTF-8',
    'dataType': 'json',
    'data': {},
    'beforeSendCallbackFunction': null,
    'successCallbackFunction': null,
    'completeCallbackFunction': null,
    'errorCallBackFunction': null,
};

function ajaxCall(doAjax_params) {

    let url = doAjax_params['url'];
    console.log(url)
    let requestType = doAjax_params['requestType'];
    let contentType = doAjax_params.hasOwnProperty('contentType')?doAjax_params['contentType']:'';
    let dataType = doAjax_params.hasOwnProperty('dataType')?doAjax_params['dataType']:'';
    let data = doAjax_params.hasOwnProperty('data')?doAjax_params['data']:{};
    let beforeSendCallbackFunction = doAjax_params.hasOwnProperty('beforeSendCallbackFunction')?doAjax_params['beforeSendCallbackFunction']:null;
    let successCallbackFunction = doAjax_params.hasOwnProperty('successCallbackFunction')?doAjax_params['successCallbackFunction']:null;
    let completeCallbackFunction = doAjax_params.hasOwnProperty('completeCallbackFunction')?doAjax_params['completeCallbackFunction']:null;
    let errorCallBackFunction = doAjax_params.hasOwnProperty('errorCallBackFunction')?doAjax_params['errorCallBackFunction']:null;
    $('#overlay').show()
    $.ajax({
        url: url,
        crossDomain: true,
        type: requestType,
        data: data,

        success: function(data, textStatus, jqXHR) {
            $('#overlay').hide()
            if (typeof successCallbackFunction === "function") {
                successCallbackFunction(data);
            }else{

            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $('#overlay').hide()
            if (typeof errorCallBackFunction === "function") {
                errorCallBackFunction(errorThrown);
            }else{

            }

        },
        complete: function(jqXHR, textStatus) {
            $('#overlay').hide()
            if (typeof completeCallbackFunction === "function") {
                completeCallbackFunction();
            }else{

            }
        }
    });
}
