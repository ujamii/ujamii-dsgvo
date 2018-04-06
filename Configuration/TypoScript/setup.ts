
# Module configuration
module.tx_ujamiidsgvo_web_ujamiidsgvodsgvocheck {
    persistence {
        storagePid = {$module.tx_ujamiidsgvo_dsgvocheck.persistence.storagePid}
    }
    view {
        templateRootPaths.0 = EXT:ujamii_dsgvo/Resources/Private/Backend/Templates/
        templateRootPaths.1 = {$module.tx_ujamiidsgvo_dsgvocheck.view.templateRootPath}
        partialRootPaths.0 = EXT:ujamii_dsgvo/Resources/Private/Backend/Partials/
        partialRootPaths.1 = {$module.tx_ujamiidsgvo_dsgvocheck.view.partialRootPath}
        layoutRootPaths.0 = EXT:ujamii_dsgvo/Resources/Private/Backend/Layouts/
        layoutRootPaths.1 = {$module.tx_ujamiidsgvo_dsgvocheck.view.layoutRootPath}
    }
}
