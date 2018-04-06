
module.tx_ujamiidsgvo_dsgvocheck {
    view {
        # cat=module.tx_ujamiidsgvo_dsgvocheck/file; type=string; label=Path to template root (BE)
        templateRootPath = EXT:ujamii_dsgvo/Resources/Private/Backend/Templates/
        # cat=module.tx_ujamiidsgvo_dsgvocheck/file; type=string; label=Path to template partials (BE)
        partialRootPath = EXT:ujamii_dsgvo/Resources/Private/Backend/Partials/
        # cat=module.tx_ujamiidsgvo_dsgvocheck/file; type=string; label=Path to template layouts (BE)
        layoutRootPath = EXT:ujamii_dsgvo/Resources/Private/Backend/Layouts/
    }
    persistence {
        # cat=module.tx_ujamiidsgvo_dsgvocheck//a; type=string; label=Default storage PID
        storagePid =
    }
}
