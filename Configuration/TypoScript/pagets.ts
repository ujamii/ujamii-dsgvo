# Module configuration, no constant replacement possible!
module.tx_ujamiidsgvo_dsgvocheck {
    settings {
        db {
            core {
                fe_users {
                    andWhere = deleted = 1
                    anonymize {
                        username = anonymized
                        name = anonymized
                        first_name = anonymized
                        middle_name = anonymized
                        last_name = anonymized
                        address = anonymized
                        telephone = anonymized
                        fax = anonymized
                        email = anonymized
                        title = anonymized
                        zip = 00000
                        city = anonymized
                        country = anonymized
                        www = anonymized
                        company = anonymized
                        image =
                        description = anonymized
                        lastlogin = 0
                    }
                }
                be_users {
                    andWhere = deleted = 1
                    anonymize {
                        username = anonymized
                        email = anonymized
                        realname = anonymized
                        description = anonymized
                    }
                }
            }
            powermail {
                tx_powermail_domain_model_mail {
                    allDeleted = 1
                    andWhere = crdate < UNIX_TIMESTAMP(DATE_SUB(now(), INTERVAL 6 MONTH))
                    anonymize {
                        deleted = 1
                        sender_name = anonymized
                        sender_mail = anonymized
                        subject = anonymized
                        receiver_mail = anonymized
                        body = anonymized
                        feuser = 0
                        sender_ip =
                        marketing_referer_domain = anonymized
                        marketing_referer = anonymized
                        marketing_country = anonymized
                        marketing_mobile_device = 0
                        marketing_frontend_language = 0
                        marketing_browser_language = anonymized
                        marketing_page_funnel = anonymized
                    }
                }
            }
            formhandler {
                tx_formhandler_log {
                    allDeleted = 1
                    andWhere = crdate < UNIX_TIMESTAMP(DATE_SUB(now(), INTERVAL 6 MONTH))
                    anonymize {
                        deleted = 1
                        ip =
                        params =
                    }
                }
            }
            tt_address {
                tt_address {
                    andWhere = deleted = 1
                    anonymize {
                        name = anonymized
                        gender =
                        first_name = anonymized
                        middle_name = anonymized
                        last_name = anonymized
                        birthday = 0
                        title = anonymized
                        email = anonymized
                        phone = anonymized
                        mobile = anonymized
                        www = anonymized
                        address = anonymized
                        building = anonymized
                        room = anonymized
                        company = anonymized
                        city = anonymized
                        zip = anonymized
                        region = anonymized
                        country = anonymized
                        image =
                        fax = anonymized
                        description = anonymized
                        skype = anonymized
                        twitter = anonymized
                        facebook = anonymized
                        linkedin = anonymized
                        latitude = 0
                        longitude = 0
                    }
                }
            }
            comments {
                tx_comments_comments {
                    andWhere = deleted = 1
                    anonymize {
                        firstname = anonymized
                        lastname = anonymized
                        email = anonymized
                        homepage = anonymized
                        location = anonymized
                        content = anonymized
                        remote_addr = anonymized
                    }
                }
            }
        }
    }
}
