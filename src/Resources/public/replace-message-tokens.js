(function() {
    "use strict";

    const forms = document.querySelectorAll('[class*="hasteform_member-invite-form-"]');

    for (let i = 0; i < forms.length; i++) {
        const form = forms[i];
        const message = form.querySelector('textarea[name="message"]');
        const firstname = form.querySelector('input[name="firstname"]');
        const lastname = form.querySelector('input[name="lastname"]');

        if (null === message) {
            continue;
        }

        let currentMessage = message.value;

        const replaceName = function(e) {
            const token = '##invite_'+e.target.name+'##';
            message.value = currentMessage.replace(token, e.target.value);
        };

        const updateMessage = function() {
            currentMessage = message.value;
        };

        message.addEventListener('input', updateMessage);

        if (null !== firstname) {
            firstname.addEventListener('input', replaceName);
            firstname.addEventListener('blur', updateMessage);
        }

        if (null !== lastname) {
            lastname.addEventListener('input', replaceName);
            lastname.addEventListener('blur', updateMessage);
        }
    }
})();
