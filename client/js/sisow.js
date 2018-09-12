$('.optionset-gateway input').on('change', function() {

    var gatewayVal = $('.optionset-gateway input:checked').val()

    if (gatewayVal == 'Sisow_ideal'){
        $('.show-ideal-banks').show();
    }else{
        $('.show-ideal-banks').hide();
    }
});

$(document).ready(function() {

    var gatewayVal = $('.optionset-gateway input:checked').val()

    if (gatewayVal == 'Sisow_ideal'){
        $('.show-ideal-banks').show();
    }else{
        $('.show-ideal-banks').hide();
    }
});
