<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Enum\Member\Company;

/**
 * 사업자 등록증 인증 서류 종류
 * - backing value : DB(es_memberCompanyCertification.documentType) 저장값
 *
 */
enum DocumentType: string
{
    case REGISTRATION = 'registration';
    case ADDITIONAL_CERT_0 = 'comAddiCert0';
    case ADDITIONAL_CERT_1 = 'comAddiCert1';
    case ADDITIONAL_CERT_2 = 'comAddiCert2';
    case ADDITIONAL_CERT_3 = 'comAddiCert3';
    case ADDITIONAL_CERT_4 = 'comAddiCert4';
}
