
# Module configuration
module.tx_ujamiidsgvo_dsgvocheck {
    persistence {
        storagePid = {$module.tx_ujamiidsgvo_dsgvocheck.persistence.storagePid}
    }
    view {
        templateRootPaths.0 = EXT:ujamii_dsgvo/Resources/Private/Templates/
        templateRootPaths.1 = {$module.tx_ujamiidsgvo_dsgvocheck.view.templateRootPath}
        partialRootPaths.0 = EXT:ujamii_dsgvo/Resources/Private/Partials/
        partialRootPaths.1 = {$module.tx_ujamiidsgvo_dsgvocheck.view.partialRootPath}
        layoutRootPaths.0 = EXT:ujamii_dsgvo/Resources/Private/Layouts/
        layoutRootPaths.1 = {$module.tx_ujamiidsgvo_dsgvocheck.view.layoutRootPath}
    }
}
