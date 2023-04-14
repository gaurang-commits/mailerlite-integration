$(() => {
    $("#countries").select2({
        theme: "bootstrap"
    });
    $("#countriesUpdate").select2({
        theme: "bootstrap",
        dropdownParent: "#updateSubscriberModal"
    });
    $('#addSubscriber').on('submit', (e) => {
        e.preventDefault();
        console.log();
        $.ajax({
            type: 'POST',
            url: '/api/subscribers',
            data: $('#addSubscriber').serializeArray(),
            success: (data) => {
                if (data.isSuccess) {
                    showAlert('success', data.message)
                }
            },
            error: (error) => {
                showAlert('failure', error.responseJSON.message);
            }
        });
    });
});

// Function to show alert messages to user
showAlert = function (alertType, message = 'Api execute successfully') {
    $(`#${alertType}`).addClass('show');
    $(`#${alertType} > #message`).text(' ' + message + ' ');
    setTimeout(function () {
        $(`#${alertType}`).removeClass('show');
    }, 2000);
};

