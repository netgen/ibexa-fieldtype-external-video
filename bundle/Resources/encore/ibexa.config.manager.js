const path = require('path');

module.exports = (ibexaConfig, ibexaConfigManager) => {
    /** Content editing */
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-content-edit-parts-js',
        newItems: [path.resolve(__dirname, '../public/admin/field.js')],
    });

    /** Content preview */
    ibexaConfigManager.add({
        ibexaConfig,
        entryName: 'ibexa-admin-ui-location-view-js',
        newItems: [path.resolve(__dirname, '../public/admin/field_preview.js')],
    });
};
