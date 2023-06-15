(function (global, doc, ibexa, React, ReactDOM, Translator) {
    const SELECTOR_FIELD = '.ibexa-field-preview--ngexternalvideo';

    [...doc.querySelectorAll(SELECTOR_FIELD)].forEach((fieldContainer) => {
        document.addEventListener('DOMContentLoaded', function () {
            const dataElement = document.getElementById('data-api');
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
                    if (response.result.meta.filename) {
                        document.getElementById('video_name').textContent = 'Name: ' + response.result.meta.filename;
                    }
                    if (response.result.duration) {
                        document.getElementById('video_duration').textContent = 'Duration: ' + response.result.duration + 's';
                    }
                    if (response.result.input.width && response.result.input.height) {
                        document.getElementById('video_resolution').textContent = 'Resolution: ' + response.result.input.width + 'x' + response.result.input.height;
                    }
                    if (response.result.thumbnail) {
                        document.getElementById('video_thumbnail_image').src =response.result.thumbnail;
                    }
                    if (response.result.uploaded) {
                        document.getElementById('video_date_uploaded').textContent = 'Uploaded at: ' + response.result.uploaded;
                    }
                })
                .catch(err => console.error(err));
        });
    });
})(window, window.document, window.ibexa, window.React, window.ReactDOM, window.Translator);
