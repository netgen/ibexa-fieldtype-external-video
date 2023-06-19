(function (global, doc, ibexa, React, ReactDOM, Translator) {
    const SELECTOR_FIELD = '.ibexa-field-preview--ngexternalvideo';

    doc.addEventListener('DOMContentLoaded', function () {
        const fieldContainers = doc.querySelectorAll(SELECTOR_FIELD);

        fieldContainers.forEach((fieldContainer) => {
            const dataElement = fieldContainer.querySelector('#data-api');
            const url = dataElement.dataset.url;
            const token = dataElement.dataset.token;
            const options = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            };

            fetch(url, options)
                .then(response => response.json())
                .then(response => {
                    const fieldPreview = fieldContainer.querySelector('.ibexa-field-preview__ngexternalvideo-wrapper');

                    if (response.result.meta.filename) {
                        fieldPreview.querySelector('#video_name').textContent = response.result.meta.filename;
                    }
                    if (response.result.duration) {
                        fieldPreview.querySelector('#video_duration').textContent = response.result.duration + 's';
                    }
                    if (response.result.input.width && response.result.input.height) {
                        fieldPreview.querySelector('#video_resolution').textContent = response.result.input.width + 'x' + response.result.input.height;
                    }
                    if (response.result.thumbnail) {
                        fieldPreview.querySelector('#video_thumbnail_image').src = response.result.thumbnail;
                    }
                    if (response.result.uploaded) {
                        fieldPreview.querySelector('#video_date_uploaded').textContent = response.result.uploaded;
                    }
                    if (response.result.preview) {
                        const videoPreviewLink = fieldContainer.querySelector('#video_preview_link');
                        videoPreviewLink.href = response.result.preview;
                        videoPreviewLink.textContent = response.result.preview;
                    }
                })
                .catch(err => console.error(err));
        });
    });
})(window, window.document, window.ibexa, window.React, window.ReactDOM, window.Translator);
