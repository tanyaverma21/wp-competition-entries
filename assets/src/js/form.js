/**
 * Main container function for all events.
 */
document.addEventListener('DOMContentLoaded', function() {
    /**
     * Scrolls to the entry form on click of Submit Entry button.
     */
    const currentUrl = window.location.href;
    if (currentUrl.indexOf('submit-entry') !== -1) {
        document.querySelector("form#entry-form").scrollIntoView();
    }

    /**
     * Adds event listener on button click of Search.
     * 
     */ 
    document.querySelector('button#entry-submit')?.addEventListener('click', function(e) {
        e.preventDefault();
        const firstName = document.querySelector('input#first-name');
        const lastName = document.querySelector('input#last-name');
        const email = document.querySelector('input#email');
        const phone = document.querySelector('input#phone');
        const description = document.querySelector('textarea#description').value ?? '';
        const competitionId = document.querySelector('#competition-id').value ?? '';

        validateElement(firstName, 'First Name');
        validateElement(lastName, 'Last Name');
        validateElement(email, 'Email');
        validateElement(phone, 'Phone');
        const request = new XMLHttpRequest();
        if (firstName?.value && lastName?.value && phone?.value && email?.value) {
            const params = `_ajaxnonce=${ajaxload_params.nonce}&firstName=${firstName?.value}&lastName=${lastName?.value}&phone=${phone?.value}&email=${email?.value}&description=${description}&competitionId=${competitionId}`;
            request.open('POST', ajaxload_params.ajaxurl, true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            request.onload = function ajaxLoad() {
              if (request.status >= 200 && request.status < 400) {
                const serverResponse = JSON.parse(request.responseText);
                const successDiv = document.querySelector('.success');
                successDiv.style.display = "block";
                successDiv.innerHTML = serverResponse.data;
                successDiv.scrollIntoView();
                document.querySelector("form#entry-form").style.display = "none";
              }
            };
    
            request.send(`action=process_entry_form&${params}`);
        }
    });

    /**
     * Validates email id.
     * @param {*} email 
     * @returns boolean
     */
    const validateEmail = (email) => {
        return String(email)
          .toLowerCase()
          .match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
          );
    };

    /**
     * Validates elements of the form.
     * @param {*} element 
     * @param {*} label 
     */
    const validateElement = (element, label) => {
        if (!element.value) {
            element.classList.add('error');
            element.nextElementSibling.textContent = `*${label} is required`;
            document.querySelector("form#entry-form").scrollIntoView();
        } else {
            if ('Email' === label) {
                if (!validateEmail(element.value)) {
                    element.classList.add('error');
                    element.nextElementSibling.textContent = '*Inappropriate Email Id';
                    document.querySelector("form#entry-form").scrollIntoView();
                } else {
                    element.nextElementSibling.textContent = '';
                    element.classList.remove('error');
                }
            } else {
                element.nextElementSibling.textContent = '';
                element.classList.remove('error');
            }
        }
    };
});
