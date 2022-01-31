async function request(element = '') {
    try {

        let init = {method: 'POST', body: null};

        if(element !== '') {
            let new_location = location.href;

            if (element.tagName === 'A') { // If it's an anchor
                new_location = element.href;
                init.body = await URIToFormData(element.href);
            }
            else if (element.type === 'submit' && element.form !== undefined) { // If it's a form
                init.body = new FormData(element.form);
                if(element.name !== undefined) init.body.append(element.name, element.value);
                if (element.form.method === 'get')
                    new_location = element.form.action + '?' + new URLSearchParams(init.body).toString()
            }
            else return;

            // Change the URL of the page (if needed)
            window.history.pushState(null, '', new_location);
        }
        else init.body = await URIToFormData(location.href)

        let response = await fetch('main.php', init);

        // Retrieve response as JSON
        // and convert to object with pattern: { selector: { property/method: value/argument, ... }, ... }
        let json = await response.json();

        for (const selector in json) {
            if (json.hasOwnProperty(selector)) {
                let node = document.querySelector(selector);

                if (node === undefined) console.log(`Cannot find an element ${selector}`);

                else {
                    for (const property in json[selector]) {
                        // Applying changes
                        if (typeof node[property] === 'function') node[property](...json[selector][property]);
                        else if (typeof node[property] !== "undefined") node[property] = json[selector][property];
                        else console.log(`Element "${selector}" don't have property/method "${property}"`);
                    }
                }
            }
        }
    } catch (e) {
        let message = 'Помилка обробки запиту: ' + e;

        console.error(message);
        alert(message);
    }
}

async function URIToFormData(uri) {
    let formData = new FormData();
    let data = new URLSearchParams(uri.split('?')[1]); // Get the URI

    for(let elem of data.entries()) {
        formData.append(elem[0], elem[1]); // Append its data to the form data
    }

    return formData;
}




async function processClick (event) {
    event.preventDefault();
    await request(event.target);
}

window.onload = () => request();
window.addEventListener('popstate',() => request());
window.addEventListener('click', processClick)