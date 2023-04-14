
//cursor initialization for pagination
let next = null;
let prev = null;
let selectedSubscriber;
$(() => {
    const table = $('#test').DataTable({
        columns: [
            { data: "name", name: "name" },
            { data: "email", name: "email" },
            { data: "country", name: "country" },
            { data: "subscribedOn", name: "subscribedOn" },
            { data: "subscribedAt", name: "subscribedAt" },
            { data: null, name: "delete" },
        ],
        columnDefs: [
            {
                targets: 1,
                className: "email_update",
            },
            {
                targets: -1,
                data: null,
                defaultContent: '<button class="btn btn-danger">Delete!</button>',
            },
        ],
        processing: true,
        serverSide: true,
        pageLength: 5,
        pagingType: "simple",
        search: {
            return: true,
        },
        ordering: false,
        ajax: {
            url: '/api/subscribers/',
            headers: {
                'Accept': 'application/json',
            },
            error: (error) => {
                let message = JSON.parse(error.responseText);
                showAlert('failure', message.message ? message.message : 'Something went wrong!');
            },
            data: function (d) {
                d.next = next;
                d.prev = prev;
            },
            dataSrc: function (data) {
                next = data.data.links?.next ? data.data.links.next : null;
                prev = data.data.links?.prev ? data.data.links?.prev : null;
                return data.data.links ? data.data.data : data.data;
            },
        }
    });

    //Update email modal open
    $('#test tbody').on('click', '.email_update', function () {
        var data = table.row($(this)).data();
        selectedSubscriber = data;
        $('#name').val(data.name);
        $('#countriesUpdate').val(data.country);
        $('#countriesUpdate').trigger('change');
        $('#updateSubscriberModal').modal('show');
    });

    //Delete subscriber
    $('#test tbody').on('click', 'button', function () {
        const data = table.row($(this).parents('tr')).data();
        $.ajax({
            url: `/api/subscribers/${data.id}`,
            method: "DELETE",
            headers: {
                'Accept': 'application/json',
            },
            success: function (data) {
                showAlert('success', 'Subscriber deleted successfully');
                table.ajax.reload();

            },
            error: (error) => {
                showAlert('failure', error.responseJSON.data?.message ? error.responseJSON.data?.message : error.responseJSON.message);
            }
        });
    });

    //Update Subscriber
    $('#updateSubscriber').on('submit', (e) => {
        e.preventDefault();
        let newData = {};
        let oldData = {
            name: selectedSubscriber.name,
            country: selectedSubscriber.country
        };
        $('#updateSubscriber').serializeArray().map((val) => {
            newData[val.name] = val.value
        });

        //Prevent API call if no change is made in susbcriber information
        if (JSON.stringify(newData) == JSON.stringify(oldData)) {
            $('#updateSubscriberModal').modal('hide');
            return true;
        }
        $.ajax({
            url: `/api/subscribers/${selectedSubscriber.id}`,
            method: "PUT",
            headers: {
                'Accept': 'application/json',
            },
            data: newData,
            success: function (data) {
                table.ajax.reload();
                $('#updateSubscriberModal').modal('hide');
                showAlert('success', data.message);
            },
            error: function (error) {
                showAlert('failure', error.responseJSON.message);
                $('#updateSubscriberModal').modal('hide');
            }
        });
    });
});


