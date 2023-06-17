(function (global, doc, ibexa, React, ReactDOM, Translator) {
    const SELECTOR_FIELD = '.ibexa-field-edit--ngexternalvideo';

    doc.addEventListener('DOMContentLoaded', function () {
        const fieldContainers = doc.querySelectorAll(SELECTOR_FIELD);

        fieldContainers.forEach((fieldContainer) => {
            const dataElement = fieldContainer.querySelector('#data-api');
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
                const wrapper = fieldContainer.querySelector('.ibexa-field-edit__ngexternalvideo-wrapper');
                wrapper.style.display = 'none';

                const noVideoFoundMessage = fieldContainer.querySelector('#no_video_found_message');
                if (noVideoFoundMessage) {
                    noVideoFoundMessage.parentNode.removeChild(noVideoFoundMessage);
                }
            }

            function showFields() {
                const wrapper = fieldContainer.querySelector('.ibexa-field-edit__ngexternalvideo-wrapper');
                wrapper.style.display = 'block';
            }

            function fetchData(formClass) {
                const url = urlWithoutVideoId + formClass;

                if (!formClass) {
                    clearFields();
                    return;
                }

                fetch(url, options)
                    .then(response => response.json())
                    .then(response => {
                        if (response.result) {
                            if (response.result.meta.filename) {
                                fieldContainer.querySelector('#video_name').textContent = response.result.meta.filename;
                            }
                            if (response.result.duration) {
                                fieldContainer.querySelector('#video_duration').textContent = response.result.duration + 's';
                            }
                            if (response.result.input.width && response.result.input.height) {
                                fieldContainer.querySelector('#video_resolution').textContent = response.result.input.width + 'x' + response.result.input.height;
                            }
                            if (response.result.thumbnail) {
                                fieldContainer.querySelector('#video_thumbnail_image').src = response.result.thumbnail;
                            }
                            if (response.result.uploaded) {
                                fieldContainer.querySelector('#video_date_uploaded').textContent = response.result.uploaded;
                            }
                            showFields();
                            const noVideoFoundMessage = fieldContainer.querySelector('#no_video_found_message');
                            if (noVideoFoundMessage) {
                                noVideoFoundMessage.parentNode.removeChild(noVideoFoundMessage);
                            }
                        } else {
                            clearFields();
                            const noVideoFoundMessage = fieldContainer.querySelector('#no_video_found_message');
                            if (!noVideoFoundMessage) {
                                const message = document.createElement('span');
                                message.id = 'no_video_found_message';
                                message.textContent = 'Video not found';
                                fieldContainer.appendChild(message);
                            }
                        }
                    })
                    .catch(err => console.error(err));
            }

            const formClass = fieldContainer.querySelector('.ibexa-field-edit-ngexternalvideo-id');
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
