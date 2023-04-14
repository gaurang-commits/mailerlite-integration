updateSubscriberEmail = function (element) {
    const elem = $(`#${element}`);
    elem.validate();
    const oldEmail = elem.data('email');
    const newEmail = elem.val();
    console.log(oldEmail, newEmail);
};