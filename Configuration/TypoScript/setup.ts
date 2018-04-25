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
        cookie-consent = //cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js
        cookie-consent.external = 1
    }
    includeCSS {
        cookie-consent = //cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css
        cookie-consent.external = 1
    }
}

lib.marks {
    PRIVACY-PAGE = TEXT
    PRIVACY-PAGE {
        typolink.parameter = {$page.privacyInfo}
        typolink.returnLast = url
    }
}