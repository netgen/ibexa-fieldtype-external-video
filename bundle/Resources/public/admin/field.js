(function (global, doc, ibexa, React, ReactDOM, Translator) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ngexternalvideo';

    [...doc.querySelectorAll(SELECTOR_FIELD)].forEach((fieldContainer) => {
        document.addEventListener('DOMContentLoaded', function () {
            const dataElement = document.getElementById('data-api');
            const urlWithoutVideoId = dataElement.dataset.url;
            const token = dataElement.dataset.token;
            const options = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                }
            };

            function clearFields() {
                const wrapper = document.querySelector('.ibexa-field-edit__ngexternalvideo-wrapper');
                wrapper.style.display = 'none';

                const noVideoFoundMessage = document.getElementById('no_video_found_message');
                if (noVideoFoundMessage) {
                    noVideoFoundMessage.parentNode.removeChild(noVideoFoundMessage);
                }
            }

            function showFields() {
                const wrapper = document.querySelector('.ibexa-field-edit__ngexternalvideo-wrapper');
                wrapper.style.display = 'block';
            }

            function fetchData(formClass) {
                const url =  urlWithoutVideoId + formClass;

                if (!formClass) {
                    clearFields();
                    return;
                }

                fetch(url, options)
                    .then(response => response.json())
                    .then(response => {
                        // Convert the API response object to a string
                        const responseString = JSON.stringify(response);
                        // Populate the textarea field with the API response string
                        if (response.result) {
                            if (response.result.meta.filename) {
                                document.getElementById('video_name').textContent = response.result.meta.filename;
                            }
                            if (response.result.duration) {
                                document.getElementById('video_duration').textContent = response.result.duration + 's';
                            }
                            if (response.result.input.width && response.result.input.height) {
                                document.getElementById('video_resolution').textContent = response.result.input.width + 'x' + response.result.input.height;
                            }
                            if (response.result.thumbnail) {
                                document.getElementById('video_thumbnail_image').src = response.result.thumbnail;
                            }
                            if (response.result.uploaded) {
                                document.getElementById('video_date_uploaded').textContent = response.result.uploaded;
                            }
                            showFields();
                            const noVideoFoundMessage = document.getElementById('no_video_found_message');
                            if (noVideoFoundMessage) {
                                noVideoFoundMessage.parentNode.removeChild(noVideoFoundMessage);
                            }
                        } else {
                            clearFields();
                            const noVideoFoundMessage = document.getElementById('no_video_found_message');
                            if (!noVideoFoundMessage) {
                                const message = document.createElement('span');
                                message.id = 'no_video_found_message';
                                message.textContent = 'Video not found';
                                const container = document.querySelector(SELECTOR_FIELD);
                                container.appendChild(message);
                            }
                        }
                    })
                    .catch(err => console.error(err));
            }

            const formClass = document.getElementsByClassName('ibexa-field-edit-ngexternalvideo-id')[0]; // Access the first element in the collection
            const initialFormClass = formClass.value;

            formClass.addEventListener('input', function (event) {
                const formClass = event.target.value;
                fetchData(formClass);
            });

            clearFields();
            fetchData(initialFormClass);
        });
    });
})(window, window.document, window.ibexa, window.React, window.ReactDOM, window.Translator);
