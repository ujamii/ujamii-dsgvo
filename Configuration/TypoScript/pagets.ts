# Module configuration, no constant replacement possible!
module.tx_ujamiidsgvo_dsgvocheck {
    settings {
        db {
            core {
                fe_users {
                    andWhere = deleted = 1
                }
                be_users {
                    andWhere = deleted = 1
                }
            }
            powermail {
                tx_powermail_domain_model_mail {
                    allDeleted = 1
                    andWhere = crdate < DATE_SUB(now(), INTERVAL 6 MONTH)
                }
            }
            formhandler {
                tx_formhandler_log {
                    allDeleted = 1
                    andWhere = crdate < DATE_SUB(now(), INTERVAL 6 MONTH)
                }
            }
            tt_address {
                tt_address {
                    andWhere = deleted = 1
                }
            }
            comments {
                tx_comments_comments {
                    andWhere = deleted = 1
                }
            }
        }
    }
}
