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

page {
    includeJSFooterlibs {
        cookie-consent = typo3conf/ext/ujamii_dsgvo/Resources/Public/Assets/cookieconsent.min.js
        cookie-consent {
            excludeFromConcatenation = 1
            disableCompression = 1
        }
    }
    includeCSS {
        cookie-consent = typo3conf/ext/ujamii_dsgvo/Resources/Public/Assets/cookieconsent.min.js
        cookie-consent {
            excludeFromConcatenation = 1
            disableCompression = 1
        }
    }
}

lib.marks {
    PRIVACY-PAGE = TEXT
    PRIVACY-PAGE {
        typolink.parameter = {$page.privacyInfo}
        typolink.returnLast = url
    }
}