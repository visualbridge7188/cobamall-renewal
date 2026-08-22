<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link      http://www.godo.co.kr
 */

namespace Bundle\Component\Sms;

use Component\Sms\SmsSender;
use DTO\Sms\SendAutoMessageDTO;
use DTO\Sms\SendAutoMessageReceiverDTO;
use Framework\Utility\ArrayUtils;
use Framework\Utility\ComponentUtils;
use Framework\Utility\StringUtils;
use Origin\DTO\AutoSend\AutoSendOptionCodeConfigDto;
use Origin\Enum\AutoSend\AutoSendChannel;
use Origin\Enum\AutoSend\AutoSendRecipientPolicy;
use Origin\Enum\AutoSend\AutoSendKakaoVendorSupport;
use Origin\Enum\AutoSend\AutoSendSupport;
use Origin\Enum\Crm\Message\FailedMessageSendType;
use Origin\Enum\Crm\Message\SmsAutoSendOverType;
use Origin\Service\AutoSend\AutoSendMyappService;
use Origin\Service\AutoSend\AutoSendTemplateVariableReplacer;
use Origin\Service\AutoSend\FindAutoSendConfigService;
use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudSendService;
use Origin\Util\EmojiRemover;
use SplObserver;

/**
 * Class SMS 자동 발송
 * 기존 SMS::smsAutoSend 함수를 대신 사용하는 클래스
 * 기존 smsAutoSend 함수 호출 시 전달하던 파라미터는 setter 함수를 통해 전역변수로 설정하면 된다.
 *
 * @package Bundle\Component\Sms
 * @author  yjwee
 */
class SmsAuto implements \SplSubject
{
    const KAKAO_ALRIM_CLOUD = 'cloud';
    const KAKAO_ALRIM_BIZM = 'bizm';
    const KAKAO_ALRIM_LUNA = 'luna'; // 블룸에이아이(구: 루나소프트)
    const MYAPP = 'myapp';
    const SMS = 'sms';

    /** @var \Framework\Database\DBTool $db */
    protected $db;
    /** @var  \Bundle\Component\Sms\Sms $sms */
    protected $sms;
    /** @var  \Bundle\Component\Sms\SmsAutoCode $smsAutoCode */
    protected $smsAutoCode;
    /** @var  string $smsType SMS 자동 발송 종류 ('order', 'member', 'promotion', 'board') */
    protected $smsType;
    /** @var  string $smsAutoCodeType SMS 자동 발송 코드 (ORDER, INCASH, ACCOUNT, DELIVERY .......) */
    protected $smsAutoCodeType;
    /** @var  mixed $receiver 수신정보
     * (1. 전화번호만 변수로 전송,
     * 2. scmNo, memNo, memNm, smsFl, cellPhone 키값으로 1차 배열,
     * 3. 0 => [scmNo, memNo, memNm, smsFl, cellPhone] 이와 같은 2차 배열, scmNo 는 배열로)
     * EX) cellPhone 는 (비)회원 전송폰번호
     * */
    protected $receiver;
    /** @var  array $recipient 수령자 수신정보
     * memNm, cellPhone 키값으로 1차 배열
     * */
    protected $recipient;
    /** @var array $replaceArguments 전송할 데이터 (치환코드 대입용) */
    protected $replaceArguments = null;
    /** @var array $smsAutoType 강제 대상지정 (member or admin or provider) ex)게시판 */
    protected $smsAutoType = null;
    protected $smsAutoConfig;
    /** @var array $smsContentWheres SMS 발송내용 조회 조건절 */
    protected $smsContentWheres = [];
    /** @var string $receiverType 수신자 정보 타입
     * phone : 전화번호
     * array : 배열
     * multi : 다중배열
     */
    protected $receiverType = 'phone';
    /** @var string $recipientType 수령자 정보 타입 (일반 배열로 고정(현재 그외 케이스없음)) */
    protected $recipientType = 'array';
    /** @var array $receiversScmNo SMS 수신받을 공급사 번호 */
    protected $receiversScmNo = [];
    /** @var string $smsContents SMS 발송내용 */
    protected $smsContents = '';
    /** @var array $checkModes 회원수신, 야간발송, 최근 주문일 체크 배열 */
    protected $checkModes = [];
    /** @var null|string 자동 SMS 발송 시간 */
    protected $smsAutoSendDate = null;
    /** @var string 자동 SMS 발송 방식 */
    protected $smsAutoTranType = 'send';
    /** @var \SplObserver[] $smsAutoObservers 자동 SMS 발송 대기 리스트 */
    protected $smsAutoObservers = [];
    /** @var bool sms 지연발송 여부 */
    protected $useObserver = false;
    /** @var array 카카오알림설정 Toast Cloud AutoConfig */
    protected $kakaoAlrimCloudAutoConfig;
    /** @var array 카카오알림설정 Toast Cloud Config */
    protected $kakaoAlrimCloudConfig;
    /** @var array 카카오 CRM 설정  */
    protected $kakaoCrmConfig;
    /** @var array 카카오알림설정 */
    protected $kakaoAlrimAutoConfig;
    /** @var array 카카오알림설정(블룸에이아이(구: 루나소프트)) */
    protected $kakaoAlrimLunaAutoConfig;
    /** @var array 카카오설정 무시 플래그 */
    protected $kakaoAlrimIgnoreFlag = 'n';
    /** @var array 발송내용 중 로그 출력 시 마스킹 정보 */
    protected $contentsMask = [];
    /** @var array 발송내용 출력 시 마스킹 처리될 치환코드 */
    protected $maskArguments = ['rc_certificationCode'];

    /** @var 마이앱 사용유무 */
    protected $useMyapp;

    /** @var 비밀번호 확인 유무 */
    protected $passwordCheckFl = true;

    /** @var AutoSendMyappService $autoSendMyappService */
    private $autoSendMyappService;

    /** @var FindAutoSendConfigService $findAutoSendConfigService */
    private $findAutoSendConfigService;

    /** @var object $logger */
    protected $logger;

    protected $msgType;
    public function __construct(array $config = [])
    {
        if (isset($config['db']) && \is_object($config['db'])) {
            $this->db = $config['db'];
        } else {
            $this->db = \App::load('DB');
        }
        // 카카오 알림 설정 값 (Tosat Cloud)
        $this->kakaoAlrimCloudAutoConfig = $this->getKakaoAlrimCloudAutoPolicy();
        $this->kakaoAlrimCloudConfig = ComponentUtils::getPolicy('kakaoAlrimCloud.config');

        // 카카오알림설정값만 기본셋팅처리
        $this->kakaoAlrimAutoConfig = $this->getKakaoAlrimAutoPolicy();
        // 카카오알림설정값(블룸에이아이(구: 루나소프트))
        $this->kakaoAlrimLunaAutoConfig = $this->getKakaoAlrimLunaAutoPolicy();

        // 마이앱 사용유무
        $this->useMyapp = gd_policy('myapp.config')['useMyapp'];

        $this->findAutoSendConfigService = \App::getInstance(FindAutoSendConfigService::class);
        $this->kakaoCrmConfig = $this->findAutoSendConfigService->getCrmKakaoConfig() ?? [];

        $this->smsAutoConfig = $this->getSmsAutoPolicy();
        $this->smsAutoCode = \App::load('\\Component\\Sms\\SmsAutoCode');
        $this->autoSendMyappService = \App::getInstance(AutoSendMyappService::class);
        $this->logger = \App::getInstance('logger');
    }


    protected function shouldKakaoSmsAlternativeSend(): bool
    {
        return $this->kakaoCrmConfig['autoSmsAlternativeSendFlag'] == 'y';
    }

    protected function shouldKakaoAlternativeBySmsContent(): bool
    {
        return $this->kakaoCrmConfig['autoSmsAlternativeSendType'] == 'SMS';
    }

    /**
     * @param string $vender
     * - kakaoAlrim
     * - kakaoAlrimCloud
     * - kakaoAlrimLuna
     * @return bool
     */
    protected function isActiveCrmKakao(string $vender)
    {
        if (empty($this->kakaoCrmConfig)) {
            $this->logger->debug(sprintf('[자동알림] 코드[%s] CRM 카카오 설정 없음 (벤더=%s)', $this->smsAutoCodeType, $vender));
            return false;
        }

        if ($this->kakaoCrmConfig['useFlag'] != 'y') {
            $this->logger->debug(sprintf('[자동알림] 코드[%s] CRM 카카오 사용 안함 - useFlag[%s] (벤더=%s)', $this->smsAutoCodeType, $this->kakaoCrmConfig['useFlag'], $vender));
            return false;
        }

        if ($this->kakaoCrmConfig['sender'] != $vender) {
            $this->logger->debug(sprintf('[자동알림] 코드[%s] CRM 카카오 발송사 불일치 - 설정발송사[%s], 요청벤더[%s]', $this->smsAutoCodeType, $this->kakaoCrmConfig['sender'], $vender));
            return false;
        }

        return true;
    }

    /**
     * 발송 가능한 수신자 여부 확인
     *
     * @param array $config 설정 배열
     * @param string|null $recipientKey 특정 수신자 키 (예: 'memberSend'). null이면 전체 검사
     * @return bool
     */
    protected function isSendableRecipient(array $config, ?string $recipientKey = null): bool
    {
        $sendConfig = $config[$this->smsType][$this->smsAutoCodeType] ?? [];

        if ($recipientKey !== null) {
            return ($sendConfig[$recipientKey.'Send'] ?? 'n') === 'y';
        }

        $recipients = AutoSendRecipientPolicy::getCodeWithRecipientsWithBoard($this->smsAutoCodeType);
        $recipientKeys = array_map(fn($recipient) => $recipient->toFlagKey()->value, $recipients);

        foreach ($recipientKeys as $key) {
            if (($sendConfig[$key] ?? 'n') === 'y') {
                return true;
            }
        }

        return false;
    }

    /**
     * 카카오 알림톡 블룸에이아이(구: 루나소프트) 사용가능 여부
     * @return bool
     */
    protected function availableKakaoAlrimLuna(?string $recipientKey = null): bool
    {
        $vendorSupported = AutoSendKakaoVendorSupport::isSupported($this->smsAutoCodeType, AutoSendKakaoVendorSupport::LUNA);
        $plusShop = gd_is_plus_shop(PLUSSHOP_CODE_KAKAOALRIMLUNA) === true;
        $ignoreFlag = $this->kakaoAlrimIgnoreFlag == 'n';
        $sendableRecipient = $this->isSendableRecipient($this->kakaoAlrimLunaAutoConfig, $recipientKey);
        $activeCrmKakao = $this->isActiveCrmKakao('kakaoAlrimLuna');
        $result = $vendorSupported && $plusShop && $ignoreFlag && $sendableRecipient && $activeCrmKakao;

        if (!$result) {
            $this->logger->debug(sprintf(
                '[자동알림] 코드[%s] 수신대상[%s] 카카오(루나) 비활성 - 벤더지원[%s], 플러스샵[%s], ignore[%s], 수신발송설정[%s], CRM활성[%s]',
                $this->smsAutoCodeType, $recipientKey ?? 'all',
                $vendorSupported ? 'Y' : 'N', $plusShop ? 'Y' : 'N', $ignoreFlag ? 'Y' : 'N',
                $sendableRecipient ? 'Y' : 'N', $activeCrmKakao ? 'Y' : 'N'
            ));
        }

        return $result;
    }

    /**
     * 카카오 알림톡 클라우드 사용가능 여부
     * @return bool
     */
    protected function availableKakaoAlrimCloud(?string $recipientKey = null): bool
    {
        $vendorSupported = AutoSendKakaoVendorSupport::isSupported($this->smsAutoCodeType, AutoSendKakaoVendorSupport::CLOUD);
        $ignoreFlag = $this->kakaoAlrimIgnoreFlag == 'n';
        $sendableRecipient = $this->isSendableRecipient($this->kakaoAlrimCloudAutoConfig, $recipientKey);
        $activeCrmKakao = $this->isActiveCrmKakao('kakaoAlrimCloud');
        $result = $vendorSupported && $ignoreFlag && $sendableRecipient && $activeCrmKakao;

        if (!$result) {
            $this->logger->debug(sprintf(
                '[자동알림] 코드[%s] 수신대상[%s] 카카오(클라우드) 비활성 - 벤더지원[%s], ignore[%s], 수신발송설정[%s], CRM활성[%s]',
                $this->smsAutoCodeType, $recipientKey ?? 'all',
                $vendorSupported ? 'Y' : 'N', $ignoreFlag ? 'Y' : 'N', $sendableRecipient ? 'Y' : 'N', $activeCrmKakao ? 'Y' : 'N'
            ));
        }

        return $result;
    }

    /**
     * 카카오 알림톡 비즈엠 사용가능 여부
     * @return bool
     */
    protected function availableKakaoAlrimBizm(?string $recipientKey = null): bool
    {
        $vendorSupported = AutoSendKakaoVendorSupport::isSupported($this->smsAutoCodeType, AutoSendKakaoVendorSupport::BIZM);
        $ignoreFlag = $this->kakaoAlrimIgnoreFlag == 'n';
        $sendableRecipient = $this->isSendableRecipient($this->kakaoAlrimAutoConfig, $recipientKey);
        $activeCrmKakao = $this->isActiveCrmKakao('kakaoAlrim');
        $result = $vendorSupported && $ignoreFlag && $sendableRecipient && $activeCrmKakao;

        if (!$result) {
            $this->logger->debug(sprintf(
                '[자동알림] 코드[%s] 수신대상[%s] 카카오(비즈엠) 비활성 - 벤더지원[%s], ignore[%s], 수신발송설정[%s], CRM활성[%s]',
                $this->smsAutoCodeType, $recipientKey ?? 'all',
                $vendorSupported ? 'Y' : 'N', $ignoreFlag ? 'Y' : 'N', $sendableRecipient ? 'Y' : 'N', $activeCrmKakao ? 'Y' : 'N'
            ));
        }

        return $result;
    }

    /**
     * 활성화된 카카오 알림톡 채널 타입을 반환
     * @param string|null $recipientKey 수신자 키 (null이면 전체 검사)
     * @return string|null KAKAO_ALRIM_LUNA, KAKAO_ALRIM_CLOUD, KAKAO_ALRIM_BIZM 중 하나 또는 null
     */
    protected function getActiveAlrimType(?string $recipientKey = null): ?string
    {
        if ($this->availableKakaoAlrimLuna($recipientKey)) {
            return self::KAKAO_ALRIM_LUNA;
        }
        if ($this->availableKakaoAlrimCloud($recipientKey)) {
            return self::KAKAO_ALRIM_CLOUD;
        }
        if ($this->availableKakaoAlrimBizm($recipientKey)) {
            return self::KAKAO_ALRIM_BIZM;
        }
        return null;
    }

    /**
     * 카카오 알림톡 예약 발송 시간 세팅
     * @param string $kakaoAlrimType
     * @return void
     */
    protected function setKakaoAlrimAutoReserveTime(string $kakaoAlrimType = '')
    {
        if (($this->smsType == SmsAutoCode::MEMBER || $this->smsType == SmsAutoCode::PRESENT)
            && gd_array_key_exists($this->smsAutoCodeType, Sms::KAKAO_AUTO_RESERVATION_DEFAULT_TIME)
        ) {
            $this->setSmsAutoSendDate($this->getKakaoAlrimAutoReserveTime($this->smsAutoCodeType, $kakaoAlrimType));
        }
    }

    /**
     * SMS 자동발송 함수
     *
     * @param string $kakaoAlrimIgnoreFlag 카카오알림톡 설정 무시 (기본 n:무시안함)
     *
     * @return array|bool 성공, 실패 Count
     */
    public function autoSend($kakaoAlrimIgnoreFlag = 'n')
    {
        if ($kakaoAlrimIgnoreFlag == 'y') {
            $this->kakaoAlrimIgnoreFlag = 'y';
        }

        $code = $this->smsAutoCodeType;
        $result = [];
        try {
            if (\App::getInstance('session')->has(SESSION_GLOBAL_MALL)) {
                throw new \Exception('해외 쇼핑몰에서는 자동 알림 발송을 지원하지 않습니다.');
            }
            $this->validateRequiredParams();


            $groupCode = $this->smsAutoCode->getGroupByCode($code);
            $this->logger->info(sprintf('[자동알림] 발송 시작 - 코드[%s], 그룹[%s]', $code, $groupCode), $this->replaceArguments ?? []);

            $allAutoConfigs = $this->findAutoSendConfigService->getAllAutoConfigs();
            $allAutoConfigsByCode = array_values(
                array_map(
                    fn(array $autoConfig) => $autoConfig[$groupCode][$code] ?? null,
                    $allAutoConfigs
                )
            );

            $option = AutoSendOptionCodeConfigDto::resolveByConfigs($code, $allAutoConfigsByCode);

            // 예약 발송 시간: option의 sendTime으로 통합 결정 (외부 세팅값 덮어씀)
            if ($option->sendTime !== null) {
                $convertHour = date('H:i:s', strtotime($option->sendTime . ':00:00'));
                $this->setSmsAutoSendDate(date('Y-m-d ' . $convertHour));
            }

            // 공통 초기화 (채널 무관)
            $this->initializeSharedState();
            $this->validateJoinPolicyByApprovalCode();
            $this->validateJoinPolicyByJoinCode($option->includeApprovalPendingMember);
            $this->validateByClaimCodes($code);

            // 수신자 목록 결정 → 수신자:채널 쌍 생성
            $recipients = $this->getRecipients();
            $recipientChannelMaps = $this->resolveRecipientChannels($recipients);
            $this->logger->info(sprintf('[자동알림] 코드[%s] 수신대상:채널 쌍 %d건 결정 - %s', $code, count($recipientChannelMaps), json_encode($recipientChannelMaps, JSON_UNESCAPED_UNICODE)));

            if (empty($recipientChannelMaps)) {
                $this->logger->warning(sprintf('[자동알림] 코드[%s] 발송 가능한 수신대상:채널 쌍이 없어 발송 건너뜀', $code));
                return $result;
            }

            // 쌍별 발송 처리 (채널별 파사드 메소드 호출)
            foreach ($recipientChannelMaps as $recipientChannelMap) {
                $recipient = $recipientChannelMap['recipient'];
                $channel = $recipientChannelMap['channel'];
                try {
                    $pairResult = match ($channel) {
                        self::MYAPP => $this->processMyappSend($recipient),
                        self::KAKAO_ALRIM_LUNA => $this->processKakaoLunaSend($recipient),
                        self::KAKAO_ALRIM_CLOUD => $this->processKakaoCloudSend($recipient),
                        self::KAKAO_ALRIM_BIZM => $this->processKakaoBizmSend($recipient),
                        'sms' => $this->processSmsSend($recipient),
                        default => null,
                    };
                    if ($pairResult !== null) {
                        $result[] = $pairResult;
                    }
                } catch (\Exception $e) {
                    $this->logger->warning(sprintf(
                        '[자동알림] 코드[%s] 수신대상[%s] 채널[%s] 발송 실패 - %s',
                        $code, $recipient, $channel, $e->getMessage()
                    ));
                }
            }

        } catch (\Exception $e) {
            $this->logger->warning(sprintf(
                '[자동알림] 코드[%s] 발송 중단 - 사유: %s (%s:%d)',
                $code, $e->getMessage(), basename($e->getFile()), $e->getLine()
            ));
            return false;
        }

        $this->logger->info(sprintf('[자동알림] 코드[%s] 발송 완료', $code), $result);

        return $result;
    }

    /**
     * SMS 로그 데이터 생성
     *
     * @param array       $item
     * @param array       $logData
     * @param array       $receiverData
     * @param string      $sendFl
     * @param bool        $withSubject
     *
     * @return array
     */
    private function makeSmsLog(
        array $item,
        array $logData,
        array $receiverData,
        string $sendFl,
        bool $withSubject = false
    ): array {
        $smsLog = [
            'sendFl' => $sendFl,
            'smsType' => $item['smsType'],
            'smsDetailType' => $logData['smsAutoCode'],
            'sendType' => 'send',
            'contents' => $item['contents'],
            'receiverCnt' => gd_count($receiverData),
            'replaceCodeType' => '',
            'sendDt' => date('Y-m-d H:i:s'),
            'smsSendKey' => '',
            'smsAutoSendOverFl' => 'none',
            'code' => $item['code'],
            'alternativeSmsAutoSendOverFl' => $item['alternativeSmsAutoSendOverFl'],
            'alternativeContents' => $item['alternativeContents'],
        ];

        if ($withSubject) {
            $smsLog['subject'] = $item['name'];
        }

        if (isset($item['useParam'])) {
            $smsLog['useParam'] = $item['useParam'];
        }

        // 예약 발송 처리
        if ($this->smsAutoSendDate !== null) {
            $smsLog['sendType']  = 'res_send';
            $smsLog['reserveDt'] = $this->smsAutoSendDate;
        }

        return $smsLog;
    }

    /**
     * 시스템에 의해 발송되는 SMS 의 경우 true, 관리자에 의해 발송되는 경우 false
     *
     * @return bool
     */
    protected function isAutoSend()
    {
        return $this->smsAutoCodeType !== Code::COUPON_MANUAL;
    }

    /**
     * 루나 알림톡은 기존 치환자 처리 방식 유지
     *
     * @param $contents
     *
     * @return mixed
     */
    public function replaceContentsKakaoLuna($contents)
    {
        $result = $contents;
        $orgArguments = [
            'rc_mallNm'            => 'rc_mallNm',
            'shopUrl'              => 'shopUrl',
            'orderNo'              => 'orderNo',
            'orderName'            => 'orderName',
            'settlePrice'          => 'settlePrice',
            'bankAccount'          => 'bankAccount',
            'orderDate'            => 'orderDate',
            'deliveryName'         => 'deliveryName',
            'invoiceNo'            => 'invoiceNo',
            'goodsNm'              => 'goodsNm',
            'userExchangeStatus'   => 'userExchangeStatus',
            'expirationDate'       => 'expirationDate',
            'memId'                => 'memId',
            'memNm'                => 'memNm',
            'sleepScheduleDt'      => 'sleepScheduleDt',
            'smsAgreementFl'       => 'smsAgreementFl',
            'smsAgreementDt'       => 'smsAgreementDt',
            'mailAgreementFl'      => 'mailAgreementFl',
            'mailAgreementDt'      => 'mailAgreementDt',
            'groupNm'              => 'groupNm',
            'mileage'              => 'mileage',
            'rc_mileage'           => 'rc_mileage',
            'deleteScheduleDt'     => 'deleteScheduleDt',
            'rc_deleteScheduleDt'  => 'rc_deleteScheduleDt',
            'deposit'              => 'deposit',
            'rc_deposit'           => 'rc_deposit',
            'rc_certificationCode' => 'rc_certificationCode',
            'wriNm'                => 'wriNm',
        ];
        if (ArrayUtils::isEmpty($this->replaceArguments) === false) {
            $patterns = $values = [];
            foreach ($this->replaceArguments as $index => $argument) {
                if ($this->smsType == 'board') { // 게시판은 다른곳이랑 다르게 상점명코드로 작성자명을 대처해서...
                    $patterns[] = '#{' . $index . '}';
                    if ($index == 'rc_mallNm') {
                        $values[] = $this->replaceArguments['wriNm'];
                    } elseif ($index == 'wriNm') {
                        $values[] = $this->replaceArguments['rc_mallNm'];
                    } else {
                        $values[] = $argument;
                    }
                } else {
                    $patterns[] = '#{' . $index . '}';
                    $values[] = $argument;
                }
                unset($orgArguments[$index]);
            }

            foreach ($orgArguments as $index => $argument) {
                $patterns[] = '#{' . $index . '}';
                $values[] = '';
            }

            $result = str_replace($patterns, $values, $contents);
            unset($patterns, $values);
        }

        return $result;
    }

    /**
     * SMS 발송 내용을 반환하는 함수
     *
     * @param array $wheres SMS 수신대상 분류
     *
     * @return array|object
     * @throws \Exception
     */
    public function getSmsContents(array $wheres)
    {
        $logger = $this->logger;
        $binds = [];
        $this->db->strField = 'smsType, smsAutoType, contents, subject';
        $this->db->strJoin = DB_SMS_CONTENTS;
        $this->db->strWhere = 'smsType= ? AND smsAutoCode = ? AND smsAutoType IN (\'' . gd_implode('\', \'', $wheres) . '\') AND contents != \'\' AND contents IS NOT NULL';
        $this->db->bind_param_push($binds, 's', $this->smsType);
        $this->db->bind_param_push($binds, 's', $this->smsAutoCodeType);
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . array_shift($query) . ' ' . gd_implode(' ', $query);
        $getData = $this->db->query_fetch($strSQL, $binds);
        if (ArrayUtils::isEmpty($getData)) {
            $logger->info(sprintf('Not found sms contents query is [%s]', $strSQL), $binds);
            throw new \Exception(__('자동발송 내용을 찾을 수 없습니다.'));
        }
        $getData = StringUtils::htmlSpecialCharsStripSlashes($getData);

        return $getData;
    }

    /**
     * SMS 발송 내용을 반환하는 함수 - 카카오알림톡
     *
     * @param array $wheres SMS 수신대상 분류
     *
     * @return array|object
     * @throws \Exception
     */
    public function getSmsContentsKakao(array $wheres)
    {
        $logger = $this->logger;

        $config = $this->kakaoAlrimAutoConfig[$this->smsType][$this->smsAutoCodeType];
        $aReturn = [];

        foreach ($wheres as $v) {
            $this->db->strField = '*';
            $this->db->strJoin = DB_KAKAO_MESSAGE_TEMPLATE;
            $this->db->strWhere = 'templateCode = ?';
            $this->db->bind_param_push($binds, 's', $config[$v . 'TemplateCode']);
            $query = $this->db->query_complete();
            $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . array_shift($query) . ' ' . gd_implode(' ', $query);
            $getData = $this->db->query_fetch($strSQL, $binds);
            if (ArrayUtils::isEmpty($getData)) {
                $logger->warning(sprintf('Not found sms contents query is [%s]', $strSQL), $binds);
                throw new \Exception(__('자동발송 내용을 찾을 수 없습니다.'));
            }
            $getData = StringUtils::htmlSpecialCharsStripSlashes($getData[0]);
            // 승인되지 않거나 중지된 템플릿 제외
            if (strtoupper($getData['inspectionStatus']) !== 'APR' || strtoupper($getData['status']) === 'S') {
                $logger->info(sprintf('Unavailable kakao alrim bizm contents template, smsAutoType[%s]', $v));
                unset($binds);
                continue;
            }
            $aReturn[] = [
                'smsType'     => $this->smsType,
                'smsAutoType' => $v,
                'name'        => $getData['templateName'],
                'contents'    => $getData['templateContent'],
                'code'        => $getData['templateCode'],
                'button'      => $getData['templateButton'],
                'inspectionStatus' => $getData['inspectionStatus'],
                'status'      => $getData['status'],
            ];
            unset($binds);
        }

        return $aReturn;
    }

    public function getSmsContentsKakaoCloud(array $wheres)
    {
        $logger = $this->logger;

        $config = $this->kakaoAlrimCloudAutoConfig[$this->smsType][$this->smsAutoCodeType];
        $aReturn = [];
        foreach ($wheres as $v) {
            $templateCode = $config[$v . 'TemplateCode'];

            /** @var KakaoAlrimCloudSendService $service */
            $service = \App::getInstance(KakaoAlrimCloudSendService::class);
            $sendInfo = $service->getSendInfo($templateCode);
            if (ArrayUtils::isEmpty($sendInfo)) {
                $logger->warning(sprintf('Not found sms contents query is [%s]', $templateCode), [__METHOD__]);
                throw new \Exception(__('자동발송 내용을 찾을 수 없습니다.'));
            }

            $sendInfo['contents'] = $sendInfo['templateContent'];
            $sendInfo['smsAutoType'] = $v;
            $sendInfo['contents'] = $sendInfo['templateContent'];
            $sendInfo['replaceCodes'] = json_decode($sendInfo['replaceCodes'], true);
            $aReturn[] = $sendInfo;
        }
        return $aReturn;
    }

    public function getSmsContentsKakaoLuna(array $wheres)
    {
        $logger = $this->logger;

        $config = $this->kakaoAlrimLunaAutoConfig[$this->smsType][$this->smsAutoCodeType];
        $aReturn = [];

        foreach ($wheres as $v) {
            $this->db->strField = '*';
            $this->db->strJoin = DB_KAKAO_LUNA_MESSAGE_TEMPLATE;
            $this->db->strWhere = 'templateCode = ?';
            $this->db->bind_param_push($binds, 's', $config[$v . 'TemplateCode']);
            $query = $this->db->query_complete();
            $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . array_shift($query) . ' ' . gd_implode(' ', $query);
            $getData = $this->db->query_fetch($strSQL, $binds);
            if (ArrayUtils::isEmpty($getData)) {
                $logger->info(sprintf('Not found sms contents query is [%s]', $strSQL), $binds);
                throw new \Exception(__('자동발송 내용을 찾을 수 없습니다.'));
            }
            $getData = StringUtils::htmlSpecialCharsStripSlashes($getData[0]);
            $aReturn[] = [
                'smsType'     => $this->smsType,
                'smsAutoType' => $v,
                //'name'        => $getData['templateName'],
                'contents'    => $getData['templateContent'],
                'code'        => $getData['templateCode'],
                'useParam'        => $getData['useParam'],
            ];
            unset($binds);
        }

        return $aReturn;
    }

    /**
     * @param mixed $smsType
     */
    public function setSmsType($smsType)
    {
        $this->smsType = $smsType;
        $this->logger->info(__METHOD__, [$this->smsType]);
    }

    /**
     * @param mixed $smsAutoCodeType
     */
    public function setSmsAutoCodeType($smsAutoCodeType)
    {
        $this->smsAutoCodeType = $smsAutoCodeType;
        $this->logger->info(__METHOD__, [$this->smsAutoCodeType]);
    }

    /**
     * @param mixed $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
        $this->logger->info(__METHOD__, [$this->receiver]);

    }

    /**
     * @param mixed $recipient
     */
    public function setRecipient(mixed $recipient): void
    {
        $this->recipient = $recipient;
        $this->logger->info(__METHOD__, [$this->recipient]);
    }

    /**
     * 자동 SMS 치환코드 정보 설정
     *
     * @param mixed $replaceArguments
     */
    public function setReplaceArguments($replaceArguments)
    {
        $replaceArguments = $this->normalizeReplaceArguments($replaceArguments);
        $this->addContentsMaskByMaskArguments($replaceArguments);
        $this->replaceArguments = $replaceArguments;
        $this->logger->debug(__METHOD__, [$this->replaceArguments]);
    }

    /**
     * 치환자 정규화
     *
     * @param array $args
     * @return array
     */
    private function normalizeReplaceArguments(array $args): array
    {
        if ($this->smsAutoCodeType == Code::SOLD_OUT && gd_array_key_exists('goodsNm', $args) && is_array($args['goodsNm'])) {
            $args['goodsNm'] = $args['goodsNm'][0];
        }

        if (isset($args['groupInGoodsNo']) && is_array($args['groupInGoodsNo'])) {
            $args['groupInGoodsNo'] = implode(',', $args['groupInGoodsNo']);
        }

        $this->syncFieldValues($args, 'name', 'memNm');

        return $this->sanitizeNullValues($args);
    }

    private function sanitizeNullValues(array $args): array
    {
        foreach ($args as $key => $value) {
            if ($value === null || $value === 'null' || $value === 'NULL') {
                $args[$key] = '';
            }
        }
        return $args;
    }

    protected function syncFieldValues(array &$data, string $primaryField, string $aliasField): void
    {
        if (empty($data[$primaryField]) && !empty($data[$aliasField])) {
            $data[$primaryField] = $data[$aliasField];
        }

        if (empty($data[$aliasField]) && !empty($data[$primaryField])) {
            $data[$aliasField] = $data[$primaryField];
        }
    }

    /**
     * @param mixed $smsAutoType
     */
    public function setSmsAutoType($smsAutoType)
    {
        $this->smsAutoType = $smsAutoType;
        $this->logger->info(__METHOD__, [$this->smsAutoType]);
    }

    /**
     * 자동 SMS 예약발송 시 예약발송 시간 설정
     *
     * @param string $smsAutoSendDate
     */
    public function setSmsAutoSendDate(string $smsAutoSendDate)
    {
        $this->smsAutoSendDate = $smsAutoSendDate;
        $this->logger->info(__METHOD__, [$this->smsAutoSendDate]);
    }

    /**
     * 자동 SMS 예약발송 시 res_send 로 설정
     *
     * @param string $smsAutoTranType
     */
    public function setSmsAutoTranType($smsAutoTranType)
    {
        $this->smsAutoTranType = $smsAutoTranType;
        $this->logger->info(__METHOD__, [$this->smsAutoTranType]);
    }

    /**
     * attach
     *
     * @param SplObserver $observer
     */
    public function attach(SplObserver $observer): void
    {
        $this->smsAutoObservers[] = $observer;
    }

    /**
     * detach
     *
     * @param SplObserver $observer
     */
    public function detach(SplObserver $observer): void
    {
        foreach ($this->smsAutoObservers as $index => $smsAutoObserver) {
            if ($observer === $smsAutoObserver) {
                unset($this->smsAutoObservers[$index]);
            }
        }
    }

    /**
     * notify
     *
     */
    public function notify(): void
    {
        $logger = $this->logger;
        $logger->info(sprintf('Start auto sms notify. observers count[%s]', gd_count($this->smsAutoObservers)));
        foreach ($this->smsAutoObservers as $observer) {
            // 에러가 있는 경우 중단 되는 케이스가 있어 아래와 같이 처리
            try {
                $observer->update($this);
            } catch (\Throwable $e) {
                $logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }

    public function reserveNotify($reserveSmsTime)
    {
        $logger = $this->logger;
        $logger->info(sprintf('Start auto sms reserve notify. reserve sms time[%s]. observers count[%s]', $reserveSmsTime, gd_count($this->smsAutoObservers)));
        $this->setSmsAutoSendDate($reserveSmsTime);
        foreach ($this->smsAutoObservers as $observer) {
            // 에러가 있는 경우 중단 되는 케이스가 있어 아래와 같이 처리
            try {
                $observer->update($this);
            } catch (\Throwable $e) {
                $logger->error($e->getMessage(), $e->getTrace());
            }
        }
    }

    /**
     * wrapping 함수
     *
     * @return int
     */
    public function getSmsLogSno()
    {
        return \App::load('Component\\Sms\\SmsSender')->getSmsLogSno();
    }

    /**
     * sms 발송을 지연발송과 즉시발송 동시에 사용하는 경우
     * 해당 값을 참조하여 attach 여부를 결정하도록 한다.
     *
     * @param bool $useObserver
     */
    public function setUseObserver(bool $useObserver)
    {
        $this->useObserver = $useObserver;
    }

    /**
     * @return bool false 인 경우 observer 를 attach 하지 않아야 한다.
     */
    public function useObserver(): bool
    {
        return $this->useObserver;
    }

    /**
     * 본사/공급사 SMS 수신대상 조회 함수
     *
     * @param $smsAutoType
     *
     * @return array|string
     */
    protected function getManagerReceiver($smsAutoType)
    {
        $strWhere = '';
        if ($smsAutoType === 'admin') {
            $strWhere = 'scmNo = 1 AND ';
        }
        if ($smsAutoType === 'provider') {
            if (is_array($this->receiversScmNo)) {
                $strWhere = 'scmNo IN (' . gd_implode(', ', $this->receiversScmNo) . ') AND scmNo != ' . DEFAULT_CODE_SCMNO . ' AND ';
            } else {
                $strWhere = 'scmNo  = ' . $this->receiversScmNo . ' AND scmNo != ' . DEFAULT_CODE_SCMNO . ' AND ';
            }
        }

        // 운영자 및 관리자는 회원 번호 0으로 파라미터 전달 ( 대체 발송시 회원 번호가 있을 경우 회원 검증이 들어감 )
        $this->db->strField = '0 as memNo, managerNm as memNm, \'y\' as smsFl, cellPhone';
        $this->db->strWhere = $strWhere . 'cellPhone !=\'\' AND cellPhone IS NOT NULL';
        $this->db->strWhere .= ' AND isDelete=\'n\'';
        $this->db->strWhere .= ' AND smsAutoReceive LIKE \'%smsAuto' . ucwords($this->smsType) . '%\'';
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MANAGER . gd_implode(' ', $query);
        $receiverData = StringUtils::htmlSpecialCharsStripSlashes($this->db->query_fetch($strSQL));

        return $receiverData;
    }

    /**
     * 수신자 정보가 다중배열 형태인 경우 수신자의 정보를 재가공하여 반환하는 함수
     *
     * @return array ['receivers'=>수신자정보, 'scmNo'=>공급사번호]
     */
    protected function getMultipleReceiver()
    {
        $logger = $this->logger;
        $result = [];
        $key = 0;
        $tmpScmNo = [];
        $logger->info(sprintf('Multiple receiver count[%d]', gd_count($this->receiver)));
        foreach ($this->receiver as $index => $item) {
            $logger->debug($index, $item);
            $result[$key]['memNo'] = StringUtils::strIsSet($this->receiver['memNo']);
            $result[$key]['memNm'] = StringUtils::strIsSet($this->receiver['memNm']);
            $result[$key]['smsFl'] = StringUtils::strIsSet($this->receiver['smsFl'], 'n');
            $result[$key]['cellPhone'] = StringUtils::strIsSet($this->receiver['cellPhone']);
            // 수신동의 회원만 발송
            if ($this->checkModes['agreeCheck'] === 'y' && $this->receiver['smsFl'] === 'n') {
                $logger->info(sprintf('Check receive agreement. agreeCheck[%s], smsFl[%s]', $this->checkModes['agreeCheck'], $this->receiver['smsFl']));
                unset($result[$key]);
            }
            $tmpScmNo = gd_array_merge($tmpScmNo, $item['scmNo']);
            $key++;
        }

        return [
            'receivers' => $result,
            'scmNo'     => gd_array_unique($tmpScmNo),
        ];
    }

    /**
     * @param string $code
     * @return void
     * @throws \Exception
     */
    public function validateByClaimCodes(string $code): void
    {
        if (!AutoSendSupport::canSendIfClaim($code)) {
            throw new \Exception(sprintf('클레임 발송 불가 코드[%s]', $code));
        }
    }


    /**
     * @param mixed $type
     * @return array
     */
    public function getKakaoAlrimAutoConfigByType(mixed $type): array
    {
        if ($type == self::KAKAO_ALRIM_LUNA) {
            $autoConfig = $this->kakaoAlrimLunaAutoConfig;
        } else if ($type == self::KAKAO_ALRIM_CLOUD) {
            $autoConfig = $this->kakaoAlrimCloudAutoConfig;
        } else {
            $autoConfig = $this->kakaoAlrimAutoConfig;
        }
        return $autoConfig;
    }

    /**
     * 치환코드 중 마스킹 처리될 내용 추가
     *
     * @param mixed $replaceArguments
     */
    protected function addContentsMaskByMaskArguments($replaceArguments)
    {
        foreach ($this->maskArguments as $maskArgument) {
            if (gd_array_key_exists($maskArgument, $replaceArguments)) {
                $this->addContentsMask($replaceArguments[$maskArgument]);
            }
        }
    }

    /**
     * 발송 내용 출력 시 마스킹 될 데이터 추가
     *
     * @param $value
     */
    protected function addContentsMask($value)
    {
        $this->contentsMask[] = $value;
    }

    /**
     * `es_config` 에 저장된 자동SMS 정책을 반환하는 함수
     *
     * @return array
     */
    protected function getSmsAutoPolicy()
    {
        return ComponentUtils::getPolicy('sms.smsAuto');
    }

    /**
     * kakao toast cloud 설정 가져오기
     * @return array
     */
    protected function getKakaoAlrimCloudAutoPolicy()
    {
        return ComponentUtils::getPolicy('kakaoAlrimCloud.kakaoAuto');
    }


    /**
     * `es_config` 에 저장된 자동SMS 정책을 반환하는 함수
     *
     * @return array
     */
    protected function getKakaoAlrimAutoPolicy()
    {
        return ComponentUtils::getPolicy('kakaoAlrim.kakaoAuto');
    }
    /**
     * `es_config` 에 저장된 자동SMS 정책을 반환하는 함수
     *
     * @return array
     */
    protected function getKakaoAlrimLunaAutoPolicy()
    {
        return ComponentUtils::getPolicy('kakaoAlrimLuna.kakaoAuto');
    }

    /**
     * 자동 SMS 발송 예약 시간 설정
     *
     * @param string $autoSmsType
     *
     * @return string 예약시간
     */
    public function getSmsAutoReserveTime($autoSmsType)
    {
        $smsAuto = $this->getSmsAutoPolicy();
        switch ($autoSmsType) {
            case Code::AGREEMENT2YPERIOD:
            case Code::GROUP_CHANGE:
            case Code::MILEAGE_EXPIRE:
            case Code::SLEEP_INFO:
            case Code::SLEEP_INFO_TODAY:
                $smsAutoContents = SmsAutoCode::MEMBER;
                break;
            case Code::COUPON_BIRTH:
            case Code::COUPON_WARNING:
            case Code::BIRTH:
                $smsAutoContents = SmsAutoCode::PROMOTION;
                break;
            case Code::PRESENT_REMAIN_THREE:
            case Code::PRESENT_REMAIN_ONE:
                $smsAutoContents = SmsAutoCode::PRESENT;
                break;
            default:
                $smsAutoContents = null;
        }

        if (empty($smsAutoContents) === false) {
            $reserveHour = gd_isset($smsAuto[$smsAutoContents][$autoSmsType]['reserveHour'], Sms::SMS_AUTO_RESERVATION_DEFAULT_TIME[$autoSmsType]);
            $convertHour = date('H:i:s', strtotime($reserveHour . ':00:00'));
        } else {
            return false;
        }

        return date('Y-m-d ' . $convertHour, strtotime('now'));
    }

    /**
     * 자동 kakao 발송 예약 시간 설정
     *
     * @param string $autoKakaoAlrimType
     *
     * @return string 예약시간
     */
    public function getKakaoAlrimAutoReserveTime($autoKakaoAlrimType, $kind = '')
    {
        if($kind == self::KAKAO_ALRIM_LUNA){
            $kakaoAuto = $this->getKakaoAlrimLunaAutoPolicy();
        } elseif ($kind === self::KAKAO_ALRIM_CLOUD) {
            $kakaoAuto = $this->getKakaoAlrimCloudAutoPolicy();
        } else{
            $kakaoAuto = $this->getKakaoAlrimAutoPolicy();
        }

        switch ($autoKakaoAlrimType) {
            case Code::AGREEMENT2YPERIOD:
            case Code::GROUP_CHANGE:
            case Code::MILEAGE_EXPIRE:
            case Code::SLEEP_INFO:
            case Code::SLEEP_INFO_TODAY:
                $kakaoAutoContents = SmsAutoCode::MEMBER;
                break;
            default:
                $kakaoAutoContents = null;
        }

        if($this->smsAutoSendDate && (Code::MILEAGE_MINUS || Code::MILEAGE_PLUS)) {
            return $this->smsAutoSendDate;
        }
        if (empty($kakaoAutoContents) === false) {
            $reserveHour = gd_isset($kakaoAuto[$kakaoAutoContents][$autoKakaoAlrimType]['reserveHour'], Sms::KAKAO_AUTO_RESERVATION_DEFAULT_TIME[$autoKakaoAlrimType]);
            $convertHour = date('H:i:s', strtotime($reserveHour . ':00:00'));
        } else {
            return false;
        }

        return date('Y-m-d ' . $convertHour, strtotime('now'));
    }

    public function setPasswordCheckFl($passwordCheckFl)
    {
        $this->passwordCheckFl = $passwordCheckFl;
    }

    /**
     * @param string $msgType
     */
    public function setMsgType(string $msgType)
    {
        $this->msgType = $msgType;
    }

    /**
     * 알림톡/SMS 사용 여부에 따라 적절한 예약 시간 반환
     * 알림톡 사용 시 알림톡 시간, 그렇지 않으면 SMS 시간 반환
     *
     * @param string $autoType 자동발송 타입 (Code::GROUP_CHANGE 등)
     * @return string|false 예약시간
     */
    public function getNotificationReserveTime(string $autoType)
    {
        // 알림톡 클라우드 사용 여부 확인
        if ($this->kakaoAlrimCloudAutoConfig['useFlag'] == 'y' && $this->kakaoAlrimCloudConfig['useFlag'] == 'y') {
            return $this->getKakaoAlrimAutoReserveTime($autoType, self::KAKAO_ALRIM_CLOUD);
        }

        // 알림톡 비즈엠 사용 여부 확인
        if ($this->kakaoAlrimAutoConfig['useFlag'] == 'y') {
            return $this->getKakaoAlrimAutoReserveTime($autoType, '');
        }

        // 알림톡 미사용 시 SMS 시간 반환
        return $this->getSmsAutoReserveTime($autoType);
    }

    /**
     * 자동알림 발송 DTO 생성 메서드
     * 수신자 정보, 치환코드 추가가 필요한 경우 상속받아 튜닝하여 사용하세요
     * - 현재 선물하기 1일전, 3일전 알림발송 스케줄리에 사용됩니다
     * @param array{
     *      type: string,
     *      code: string,
     *      receivers: list<array{
     *          receiverInfo: array{ // 수신자 정보
     *              cellPhone: string,
     *              memberNo: int|null,
     *              memberName: string|null,
     *              smsFl: string|null,
     *              scmNo: int|null
     *          },
     *          recipientInfo: array{ // 수령자 정보
     *              cellPhone: string,
     *              memberNo: int|null,
     *              memberName: string|null,
     *              smsFl: string|null,
     *              scmNo: int|null
     *          },
     *          replaceArguments: array|null
     *      }>,
     *  } $data
     *
     * @return SendAutoMessageDTO
     */
    public function createSendAutoMessageDto(array $data): SendAutoMessageDTO
    {
        return new SendAutoMessageDTO(
            type: $data['type'],
            code: $data['code'],
            receivers: array_map(function($receiver) {
                return new SendAutoMessageReceiverDTO(
                    receiverInfo: $receiver['receiverInfo'] ?? [],
                    recipientInfo: $receiver['recipientInfo'] ?? [],
                    replaceArguments: $receiver['replaceArguments'] ?? []
                );
            }, $data['receivers'])
        );
    }

    /**
     * 자동알림 발송 DTO를 통한 자동알림 발송
     * @param SendAutoMessageDTO $dto
     * @throws \Exception
     */
    public function sendWithDto(SendAutoMessageDTO $dto): void
    {
        $this->setSmsType($dto->getType());
        $this->setSmsAutoCodeType($dto->getCode());
        $reserveTime = $this->getSmsAutoReserveTime($dto->getCode());
        if($reserveTime !== false) {
            $this->setSmsAutoSendDate($reserveTime);
        }

        foreach ($dto->getReceivers() as $receiver) {
            $this->setReceiver($receiver->getReceiverInfo());
            $this->setRecipient($receiver->getRecipientInfo());
            $this->setReplaceArguments($receiver->getReplaceArguments());
            $this->autoSend();
        }
    }

    private function resolveMemberReceiver(array $item, array $receiverData, bool $isPossibleSend, array $logData): array
    {
        if ($this->receiverType === 'array') {
            $receiverData[0]['memNo'] = StringUtils::strIsSet($this->receiver['memNo']);
            $receiverData[0]['memNm'] = StringUtils::strIsSet($this->receiver['memNm']);
            $receiverData[0]['smsFl'] = StringUtils::strIsSet($this->receiver['smsFl'], 'n');
            $receiverData[0]['cellPhone'] = StringUtils::strIsSet($this->receiver['cellPhone']);
            // 수신동의 회원만 발송
            if ($this->checkModes['agreeCheck'] === 'y' && $this->receiver['smsFl'] === 'n') {
                $this->logger->info(sprintf('[자동알림] 코드[%s] 회원 수신 동의 거부(smsFl=n)로 발송 건너뜀', $this->smsAutoCodeType));
                $isPossibleSend = false;
            }
            $logData['receiver']['type'] = 'each';
            $logData['receiver']['scmNo'] = StringUtils::strIsSet($this->receiver['scmNo']);
        } elseif ($this->receiverType === 'multi') {
            $multipleReceiver = $this->getMultipleReceiver();
            $receiverData = $multipleReceiver['receivers'];
            if (empty($receiverData)) {
                $this->logger->info(sprintf('[자동알림] 코드[%s] 다중 수신자 해석 결과 수신 가능 회원 없음', $this->smsAutoCodeType));
                $isPossibleSend = false;
            }
            $logData['receiver']['type'] = 'group';
            $logData['receiver']['scmNo'] = $multipleReceiver['scmNo'];
        } elseif ($this->receiverType === 'phone') {
            $receiverData[0]['memNo'] = '0';
            $receiverData[0]['memNm'] = '';
            $receiverData[0]['smsFl'] = 'y';
            $receiverData[0]['cellPhone'] = $this->receiver;
            $logData['receiver']['type'] = 'each';
        } else {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 알 수 없는 수신자 타입[%s] 수신자[%s]', $this->smsAutoCodeType, $this->receiverType, json_encode($this->receiver, JSON_UNESCAPED_UNICODE)));
        }

        return [$receiverData, $isPossibleSend, $logData];
    }

    private function resolveManagerReceiver(string $smsAutoType, array $receiverData, array $logData): array
    {
        if ($this->smsAutoCodeType == 'SETTLE_BANK') {
            $receiverData[]['cellPhone'] = $this->receiver;
        } else {
            $receiverData = $this->getManagerReceiver($smsAutoType);
        }
        $logData['receiver']['type'] = 'group';
        $logData['receiver']['scmNo'] = $this->receiversScmNo;
        $logData['receiver']['dbTable'] = 'manager';

        return [$receiverData, $logData];
    }

    private function resolveRecipientReceiver(array $receiverData, bool $isPossibleSend, array $logData): array
    {
        if ($this->recipientType === 'array') {
            $receiverData[0]['memNo'] = StringUtils::strIsSet($this->recipient['memNo']);
            $receiverData[0]['memNm'] = StringUtils::strIsSet($this->recipient['memNm']);
            $receiverData[0]['smsFl'] = StringUtils::strIsSet($this->recipient['smsFl'], 'n');
            $receiverData[0]['cellPhone'] = StringUtils::strIsSet($this->recipient['cellPhone']);
            // 수신동의 회원만 발송
            if ($this->checkModes['agreeCheck'] === 'y' && $this->recipient['smsFl'] === 'n') {
                $this->logger->info(sprintf('[자동알림] 코드[%s] 수령자 수신 동의 거부(smsFl=n)로 발송 건너뜀', $this->smsAutoCodeType));
                $isPossibleSend = false;
            }
            $logData['receiver']['type'] = 'each';
            $logData['receiver']['scmNo'] = StringUtils::strIsSet($this->recipient['scmNo']);
        } else {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수령자 알 수 없는 수신자 타입[%s] 수령자[%s]', $this->smsAutoCodeType, $this->recipientType, json_encode($this->recipient, JSON_UNESCAPED_UNICODE)));
        }

        return [$receiverData, $isPossibleSend, $logData];
    }

    /**
     * 채널 무관 기본 검증
     * smsType, smsAutoCodeType, receiver 존재 여부만 확인
     *
     * @throws \Exception
     */
    protected function validateRequiredParams(): void
    {
        if (empty($this->smsType) || empty($this->smsAutoCode->getGroupByCode($this->smsAutoCodeType))) {
            throw new \Exception('SMS ' . __('발송종류 정보가 없습니다.'));
        }
        if (empty($this->smsAutoCodeType)) {
            throw new \Exception('SMS ' . __('발송코드 정보가 없습니다.'));
        }
        if (empty($this->receiver) && empty($this->recipient)) {
            throw new \Exception(__('수신정보가 없습니다.'));
        }
    }

    /**
     * 채널 무관 공통 상태 초기화
     * checkModes, receiverType, receiversScmNo를 설정
     * orderCheck/nightCheck 검증은 채널별로 수행하므로 여기서는 하지 않음
     */
    protected function initializeSharedState(): void
    {
        // checkModes 로드 (코드 기본값)
        $defaultAutoCode = $this->smsAutoCode->getCodes();
        foreach ($defaultAutoCode[$this->smsType] as $item) {
            if ($item['code'] == $this->smsAutoCodeType) {
                $this->checkModes['orderCheck'] = $item['orderCheck'];
                $this->checkModes['nightCheck'] = $item['nightCheck'];
                $this->checkModes['agreeCheck'] = $item['agreeCheck'];
            }
        }

        // receiverType 판별
        if (is_array($this->receiver)) {
            if (gd_array_key_exists('cellPhone', $this->receiver)) {
                $this->receiverType = 'array';
            } else {
                $this->receiverType = 'multi';
            }
        } else {
            $this->receiverType = 'phone';
        }
        $this->logger->info(sprintf('[자동알림] 코드[%s] 수신자 타입[%s] 수신자[%s]', $this->smsAutoCodeType, $this->receiverType, json_encode($this->receiver, JSON_UNESCAPED_UNICODE)));

        // receiversScmNo 추출
        if ($this->receiverType === 'array') {
            $isCheck = true;
            if ($this->checkModes['agreeCheck'] === 'y' && StringUtils::strIsSet($this->receiver['smsFl'], 'n') === 'n') {
                $isCheck = false;
                $this->logger->info(sprintf('[자동알림] 코드[%s] 수신 동의 미동의 상태 - smsFl[%s]', $this->smsAutoCodeType, $this->receiver['smsFl']));
            }
            if (empty($this->receiver['scmNo'])) {
                $this->logger->info(sprintf('[자동알림] 코드[%s] 수신자 공급사 번호(scmNo) 없음', $this->smsAutoCodeType));
                $isCheck = false;
            }
            if ($isCheck) {
                $this->receiversScmNo = $this->receiver['scmNo'];
            }
        }
        if ($this->receiverType === 'multi') {
            $tmpScmNo = [];
            foreach ($this->receiver as $index => $item) {
                $isCheck = true;
                if ($this->checkModes['agreeCheck'] === 'y' && StringUtils::strIsSet($item['smsFl'], 'n') === 'n') {
                    $isCheck = false;
                }
                if (empty($item['scmNo'])) {
                    $isCheck = false;
                }
                if ($isCheck) {
                    $tmpScmNo = gd_array_merge($tmpScmNo, $item['scmNo']);
                }
            }
            $this->receiversScmNo = gd_array_unique($tmpScmNo);
        }
    }

    /**
     * 이 코드에 대한 수신자 목록 결정
     *
     * @return string[] e.g. ['member', 'admin', 'provider']
     * @throws \Exception
     */
    protected function getRecipients(): array
    {
        if ($this->smsAutoType !== null) {
            $defaultSmsAutoType = ['member', 'admin', 'provider', 'recipient'];
            if (gd_in_array($this->smsAutoType, $defaultSmsAutoType) === false) {
                $this->logger->warning(sprintf('[자동알림] 코드[%s] 잘못된 발송대상 지정[%s]', $this->smsAutoCodeType, $this->smsAutoType));
                throw new \Exception(__('잘못된 SMS 발송대상입니다.'));
            }
            return [$this->smsAutoType];
        }

        $recipients = [];
        foreach (AutoSendRecipientPolicy::getCodeWithRecipientsWithBoard($this->smsAutoCodeType) as $recipient) {
            $recipients[] = strtolower($recipient->name);
        }

        if (empty($recipients)) {
            throw new \Exception(__('지정한 SMS 발송대상이 없습니다.'));
        }

        return $recipients;
    }

    /**
     * 채널별 설정 존재 검증
     *
     * @param string $channel 'luna', 'cloud', 'bizm', 'sms'
     * @throws \Exception
     */
    protected function validateChannelConfig(string $channel): void
    {
        if ($channel === 'sms') {
            if (empty($this->smsAutoConfig[$this->smsType][$this->smsAutoCodeType])) {
                $this->logger->warning(sprintf(
                    '[자동알림] 코드[%s] 채널[SMS] 자동발송 설정 없음 - 그룹[%s]',
                    $this->smsAutoCodeType, $this->smsType
                ));
                throw new \Exception(__('발송종류') . '[' . $this->smsType . '] , ' . __('발송코드') . '[' . $this->smsAutoCodeType . ']' . __('에 맞는 자동발송 정보가 없습니다.'));
            }
        } else {
            $autoConfig = $this->getKakaoAlrimAutoConfigByType($channel);
            if (empty($autoConfig[$this->smsType][$this->smsAutoCodeType])) {
                $this->logger->warning(sprintf(
                    '[자동알림] 코드[%s] 채널[%s] 자동발송 설정 없음 - 그룹[%s]',
                    $this->smsAutoCodeType, $channel, $this->smsType
                ));
                throw new \Exception(__('발송종류') . '[' . $this->smsType . '] , ' . __('발송코드') . '[' . $this->smsAutoCodeType . ']' . __('에 맞는 자동발송 정보가 없습니다.'));
            }
        }
    }

    /**
     * orderCheck/nightCheck 채널별 검증
     * FLAG는 $this->checkModes, VALUES는 채널별 config에서 조회
     *
     * @param string $channel
     * @throws \Exception
     */
    protected function validateOrderAndNightChecks(string $channel): void
    {
        if ($channel === 'sms') {
            $config = $this->smsAutoConfig[$this->smsType][$this->smsAutoCodeType];
        } elseif($channel === 'myapp') {
            $myappConfig = $this->findAutoSendConfigService->getMyappConfig();
            $config = $myappConfig[$this->smsType][$this->smsAutoCodeType] ?? [];
        } else {
            $autoConfig = $this->getKakaoAlrimAutoConfigByType($channel);
            $config = $autoConfig[$this->smsType][$this->smsAutoCodeType];
        }

        if ($this->checkModes['orderCheck'] === 'y') {
            $smsOrderDate = StringUtils::strIsSet($config['smsOrderDate'], 15);
            if (isset($this->replaceArguments['orderNo'])) {
                $tmpDate = '20' . substr($this->replaceArguments['orderNo'], 0, 12);
                $compareDate = strtotime('+' . $smsOrderDate . ' day', strtotime($tmpDate));
                if ($compareDate < time()) {
                    $this->logger->warning(sprintf(
                        '[자동알림] 코드[%s] 채널[%s] 주문일 기준 %s일 초과 - 주문번호 기준일[%s]',
                        $this->smsAutoCodeType, $channel, $smsOrderDate, $tmpDate
                    ));
                    throw new \Exception(__('최근') . ' ' . $smsOrderDate . __('일 주문건만 전송이 가능합니다.'));
                }
            }
        }
        if ($this->checkModes['nightCheck'] === 'y') {
            $smsNightSend = StringUtils::strIsSet($config['smsNightSend'], 'n');
            if ($smsNightSend === 'n') {
                $thisHour = date('G', time());
                if (gd_in_array($thisHour, Sms::SMS_FORBID_TIME)) {
                    throw new \Exception(__('야간 발송 금지인 SMS 입니다.'));
                }
            }
        }
    }

    protected function validateJoinPolicyByApprovalCode()
    {
        if ($this->smsAutoCodeType === Code::APPROVAL && !AutoSendSupport::useJoinPolicy()) {
            $this->logger->info(sprintf(
                '[자동알림] 코드[%s] 가입 승인 코드이나 가입 승인/연령 제한 정책이 미설정되어 발송 중단',
                $this->smsAutoCodeType
            ), [AutoSendSupport::getJoinPolicyFlags()]);
            throw new \Exception('Disapproval member');
        }
    }

    /**
     * 가입 승인 코드인 경우, 가입 정책에 따라 발송 여부 결정
      - 가입 승인 정책 미사용 && 미승인 회원 발송 미설정 && 승인 대기 상태인 경우 발송 불가
     *
     * @param ?string $smsDisapproval
     * @throws \Exception
     */
    protected function validateJoinPolicyByJoinCode(?string $smsDisapproval): void
    {
        if (
            $this->smsAutoCodeType == Code::JOIN &&
            AutoSendSupport::useJoinPolicy() && // 가입 승인 or 가입 연령 제한 사용할 경우에만
            $smsDisapproval !== 'y' && // 미승인 회원 발송 미설정
            ($this->replaceArguments['appFl'] ?? null) === 'n' // 승인 대기
        ) {
            $this->logger->info(sprintf(
                '[자동알림] 코드[%s] 가입 코드 - 미승인 회원 발송 미설정(옵션=%s) & 승인 대기(appFl=n) 상태로 발송 중단',
                $this->smsAutoCodeType, $smsDisapproval ?? 'null'
            ), [
                '가입 정책' => AutoSendSupport::getJoinPolicyFlags(),
            ]);

            throw new \Exception('Disapproval member');
        }
    }

    /**
     * 단일 수신자의 채널별 템플릿 조회
     *
     * @param string $recipientKey e.g. 'member', 'admin'
     * @param string $channel e.g. 'luna', 'cloud', 'bizm', 'sms'
     * @return array|null
     */
    protected function fetchTemplateForRecipient(string $recipientKey, string $channel): ?array
    {
        $wheres = [$recipientKey];

        if ($channel !== 'sms') {
            $autoConfig = $this->getKakaoAlrimAutoConfigByType($channel);
            $codeConfig = $autoConfig[$this->smsType][$this->smsAutoCodeType] ?? [];
            if (empty($codeConfig[$recipientKey . 'TemplateCode'])) {
                return null;
            }
        }

        try {
            if ($channel === self::KAKAO_ALRIM_LUNA) {
                $templates = $this->getSmsContentsKakaoLuna($wheres);
            } elseif ($channel === self::KAKAO_ALRIM_CLOUD) {
                $templates = $this->getSmsContentsKakaoCloud($wheres);
            } elseif ($channel === self::KAKAO_ALRIM_BIZM) {
                $templates = $this->getSmsContentsKakao($wheres);
            } else {
                $templates = $this->getSmsContents($wheres);
            }
        } catch (\Exception $e) {
            $this->logger->warning(sprintf(
                '[자동알림] 코드[%s] 수신대상[%s] 채널[%s] 템플릿 조회 실패 - %s',
                $this->smsAutoCodeType, $recipientKey, $channel, $e->getMessage()
            ));
            return null;
        }

        return !empty($templates) ? $templates[0] : null;
    }

    /**
     * SMS 콘텐츠 조회 (카카오 대체 발송용)
     * SMS 미설정 환경에서는 null 반환
     *
     * @param string $recipientKey
     * @return array|null
     */
    protected function fetchSmsContentForRecipient(string $recipientKey): ?array
    {
        try {
            $templates = $this->getSmsContents([$recipientKey]);
            return !empty($templates) ? gd_htmlspecialchars_stripslashes($templates[0]) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 수신자 해석 공통 헬퍼
     * 수신자 타입에 따라 receiverData와 logData를 반환
     *
     * @param string $recipient
     * @return array|null ['receiverData' => array, 'logData' => array] 또는 null (발송 불가)
     */
    protected function resolveReceiverData(string $recipient): ?array
    {
        $receiverData = [];
        $logData = [];
        $logData['receiver']['smsAutoType'] = $recipient;

        if ($recipient === 'member') {
            [$receiverData, $isPossibleSend, $logData] = $this->resolveMemberReceiver(
                ['smsAutoType' => $recipient], $receiverData, true, $logData
            );
            if (!$isPossibleSend) {
                return null;
            }
        } elseif ($recipient === 'admin' || ($recipient === 'provider' && !empty($this->receiversScmNo))) {
            [$receiverData, $logData] = $this->resolveManagerReceiver($recipient, $receiverData, $logData);
        } elseif ($recipient === 'recipient') {
            [$receiverData, $isPossibleSend, $logData] = $this->resolveRecipientReceiver(
                $receiverData, true, $logData
            );
            if (!$isPossibleSend) {
                return null;
            }
        }

        if (empty($receiverData)) {
            $this->logger->warning(sprintf(
                '[자동알림] 코드[%s] 수신대상[%s] 수신자 정보가 비어있어 발송 건너뜀',
                $this->smsAutoCodeType, $recipient
            ));
            return null;
        }

        $this->logger->info(sprintf(
            '[자동알림] 코드[%s] 수신대상[%s] 수신자 정보 - 수신자수[%d]',
            $this->smsAutoCodeType, $recipient, count($receiverData)
        ), array_map(function ($r) {
            return [
                'memNo' => $r['memNo'] ?? '',
                'memNm' => $r['memNm'] ?? '',
                'cellPhone' => $r['cellPhone'] ?? '',
                'smsFl' => $r['smsFl'] ?? '',
            ];
        }, $receiverData));

        return ['receiverData' => $receiverData, 'logData' => $logData];
    }

    /**
     * 게시판 치환자 변경
     *
     * @return void
     */
    private function replaceBoardArguments(): void
    {
        if ($this->smsType == 'board') {
            $temp = $this->replaceArguments['wriNm'] ?? '';
            $this->replaceArguments['wriNm'] = $this->replaceArguments['rc_mallNm'] ?? '';
            $this->replaceArguments['rc_mallNm'] = $temp;
        }
    }

    protected function buildSendLogData(array $logData): array
    {
        $logData['smsAutoCode'] = $this->smsAutoCodeType;
        if ($this->smsAutoSendDate != null) {
            $logData['reserve']['mode'] = 'reserve';
            $logData['reserve']['date'] = $this->smsAutoSendDate;
            $logData['reserve']['dbTable'] = 'member';
            $logData['reserve']['smsAutoCode'] = $this->smsAutoCodeType;
            if ($this->smsAutoCodeType == 'ACCOUNT') {
                $logData['reserve']['orderNo'] = $this->replaceArguments['orderNo'];
            }
        }
        return $logData;
    }

    /**
     * 수신자:채널 쌍 배열 생성
     * 채널 우선순위: myapp (member 전용) > kakao (luna > cloud > bizm) > SMS
     * myapp이 활성화되면 해당 수신자의 kakao/SMS는 건너뜀 (exclusive)
     *
     * @param string[] $recipients
     * @return array[] [['recipient' => string, 'channel' => string], ...]
     */
    protected function resolveRecipientChannels(array $recipients): array
    {
        $recipientChannels = [];

        foreach ($recipients as $recipient) {
            if ($recipient === 'member') {
                $memNo = !empty($this->replaceArguments['memNo']) ? (int) $this->replaceArguments['memNo'] : null;
                if (AutoSendSupport::isSleepMemberExcluded($this->smsAutoCodeType, $memNo)) {
                    $this->logger->info(sprintf('[자동알림] 코드[%s] 수신대상[%s] 휴면회원 제외 - 발송 건너뜀', $this->smsAutoCodeType, $recipient));
                    continue;
                }
            }

            if ($recipient === 'member' && $this->autoSendMyappService->valid($this->smsAutoCodeType)) {
                $recipientChannels[] = ['recipient' => $recipient, 'channel' => self::MYAPP];
                continue;
            }

            $kakaoType = $this->getActiveAlrimType($recipient);
            if ($kakaoType !== null) {
                $recipientChannels[] = ['recipient' => $recipient, 'channel' => $kakaoType];
                continue;
            }

            if ($this->isSendableRecipient($this->smsAutoConfig, $recipient)) {
                $recipientChannels[] = ['recipient' => $recipient, 'channel' => 'sms'];
                continue;
            }

            $smsSendFlag = $this->smsAutoConfig[$this->smsType][$this->smsAutoCodeType][$recipient . 'Send'] ?? 'n';
            $this->logger->info(sprintf(
                '[자동알림] 코드[%s] 수신대상[%s] 발송 가능한 채널 없음 - SMS발송설정[%s], 카카오/마이앱은 상세 debug 로그 참고',
                $this->smsAutoCodeType, $recipient, $smsSendFlag
            ));
        }

        return $recipientChannels;
    }

    // ========================================
    // 채널별 발송 파사드
    // ========================================

    /**
     * 마이앱 푸시 발송 (member 전용)
     * 검증 → 수신자 해석 → 발송
     */
    protected function processMyappSend(string $recipient): ?array
    {
        // 수신자 해석
        $this->validateOrderAndNightChecks(self::MYAPP);
        $resolved = $this->resolveReceiverData($recipient);
        if ($resolved === null) {
            return null;
        }
        $originalReplaceArguments = $this->replaceArguments;
        $this->replaceBoardArguments();

        $receiverData = $resolved['receiverData'];
        $logData = $resolved['logData'];
        $logData['smsAutoCode'] = $this->smsAutoCodeType;

        // 발송
        $item = [
            'smsType' => $this->smsType,
            'smsAutoType' => $recipient,
            'contents' => '',
            'code' => '',
            'alternativeSmsAutoSendOverFl' => 'none',
            'alternativeContents' => '',
        ];

        $aSmsLog = $this->makeSmsLog($item, $logData, $receiverData, 'myappPush', true);
        $this->autoSendMyappService->sendMyappPush(
            $this->smsAutoCodeType,
            $receiverData,
            $this->replaceArguments,
            $this->smsAutoSendDate,
            $aSmsLog,
            $originalReplaceArguments
        );

        return null;
    }

    /**
     * 카카오 알림톡 블룸에이아이(구: 루나소프트) 발송
     * 검증 → 수신자 해석 → 템플릿 조회 → 내용 치환 → 예약 시간 → 발송
     */
    protected function processKakaoLunaSend(string $recipientKey): ?array
    {
        // 채널 검증
        $this->validateChannelConfig(self::KAKAO_ALRIM_LUNA);
        $this->validateOrderAndNightChecks(self::KAKAO_ALRIM_LUNA);

        // 수신자 해석
        $resolved = $this->resolveReceiverData($recipientKey);
        if ($resolved === null) {
            return null;
        }
        $receiverData = $resolved['receiverData'];
        $logData = $resolved['logData'];

        // 템플릿 조회
        $item = $this->fetchTemplateForRecipient($recipientKey, self::KAKAO_ALRIM_LUNA);
        if ($item === null) {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수신대상[%s] 카카오(루나) 템플릿 없음', $this->smsAutoCodeType, $recipientKey));
            return null;
        }

        // 내용 치환
        $contents = $this->replaceContentsKakaoLuna($item['contents']);
        if (empty($contents)) {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수신대상[%s] 카카오(루나) 치환 후 내용이 비어있음', $this->smsAutoCodeType, $recipientKey));
            return null;
        }

        // 로그 데이터
        $logData = $this->buildSendLogData($logData);

        // 발송 (블룸에이아이는 대체 발송 고려 X - 밴더사에서 처리)
        $oKakao = new \Component\Member\KakaoAlrimLuna;
        $smsUtil = \App::load('Component\\Sms\\SmsUtil');
        $aSender = $smsUtil->getSender();
        $aSmsLog = $this->makeSmsLog($item, $logData, $receiverData, 'kakaoLuna', false);
        $aLogData = $logData;
        foreach ($receiverData as $k => $v) {
            $receiverData[$k]['smsFl'] = 'n';
        }
        $receiverForSaveSmsSendList = $receiverData;
        $oKakao->sendKakaoAlrimLuna($aSmsLog, $aSender, $aLogData, $receiverForSaveSmsSendList, $this->replaceArguments, $contents);

        return ['success' => 1, 'fail' => 0];
    }

    /**
     * 카카오 알림톡 클라우드 발송
     * 검증 → 수신자 해석 → 템플릿 조회 → 내용 치환 → SMS 대체 콘텐츠 → 예약 시간 → 발송
     */
    protected function processKakaoCloudSend(string $recipientKey): ?array
    {
        // 채널 검증
        $this->validateChannelConfig(self::KAKAO_ALRIM_CLOUD);
        $this->validateOrderAndNightChecks(self::KAKAO_ALRIM_CLOUD);
        $originalReplaceArguments = $this->replaceArguments;
        $this->replaceBoardArguments();

        // 수신자 해석
        $resolved = $this->resolveReceiverData($recipientKey);
        if ($resolved === null) {
            return null;
        }
        $receiverData = $resolved['receiverData'];
        $logData = $resolved['logData'];

        // 템플릿 조회
        $item = $this->fetchTemplateForRecipient($recipientKey, self::KAKAO_ALRIM_CLOUD);
        if ($item === null) {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수신대상[%s] 카카오(클라우드) 템플릿 없음', $this->smsAutoCodeType, $recipientKey));
            return null;
        }

        // 삭제된 템플릿 체크
        if (($item['delFl'] ?? 'n') !== 'n') {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수신대상[%s] 카카오(클라우드) 템플릿이 삭제 상태', $this->smsAutoCodeType, $recipientKey));
            return null;
        }

        // 내용 치환
        $contents = AutoSendTemplateVariableReplacer::replaceKakaoCodeToValue(
            $item['contents'], $this->replaceArguments, $this->smsAutoCodeType, AutoSendChannel::KAKAO_ALRIM_TALK
        );

        $item['contents'] = AutoSendTemplateVariableReplacer::normalizeNewlines($contents);
        if (empty($contents)) {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수신대상[%s] 카카오(클라우드) 치환 후 내용이 비어있음', $this->smsAutoCodeType, $recipientKey));
            return null;
        }

        // 로그 데이터
        $logData = $this->buildSendLogData($logData);

        // 대체 발송 처리
        $replacedKakaoContents = EmojiRemover::remove(
            AutoSendTemplateVariableReplacer::replaceCodeToValue($contents, $this->replaceArguments, $this->smsAutoCodeType, AutoSendChannel::KAKAO_ALRIM_TALK)
        );
        $kakaoSmsOverFlag = 'none';
        if ($this->shouldKakaoSmsAlternativeSend()) {
            if ($this->shouldKakaoAlternativeBySmsContent()) {
                $smsContentForRecipient = $this->fetchSmsContentForRecipient($recipientKey);
                if ($smsContentForRecipient !== null) {
                    $replacedKakaoContents = AutoSendTemplateVariableReplacer::replaceCodeToValue(
                        $smsContentForRecipient['contents'], $originalReplaceArguments, $this->smsAutoCodeType, AutoSendChannel::SMS
                    );
                }
                $kakaoSmsOverFlag = SmsAutoSendOverType::LIMIT->value;
                if ($this->findAutoSendConfigService->isOverSmsLength($replacedKakaoContents)) {
                    $kakaoSmsOverFlag = $this->findAutoSendConfigService->getSmsSendOverFlag($this->smsAutoConfig)->value;
                }
            } else {
                $kakaoSmsOverFlag = SmsAutoSendOverType::LIMIT->value;
                if ($this->findAutoSendConfigService->isOverSmsLength($replacedKakaoContents)) {
                    $kakaoSmsOverFlag = $this->findAutoSendConfigService->convertCrmToSendOverFlag($this->kakaoCrmConfig);;
                }
            }
        } else {
            $replacedKakaoContents = '';
        }

        $item['alternativeSmsAutoSendOverFl'] = $kakaoSmsOverFlag;
        $item['alternativeContents'] = AutoSendTemplateVariableReplacer::normalizeNewlines($replacedKakaoContents);

        $this->fillMissingCloudReplaceCodesWithEmpty($item['replaceCodes'] ?? []);

        // 발송
        /** @var KakaoAlrimCloudSendService $kakaoService */
        $kakaoService = \App::getInstance(KakaoAlrimCloudSendService::class);
        $aSmsLog = $this->makeSmsLog($item, $logData, $receiverData, 'kakaoCloud', true);
        $kakaoService->send($item, $receiverData, $this->replaceArguments, $this->smsAutoSendDate, $aSmsLog);

        return ['success' => 1, 'fail' => 0];
    }

    /**
     * 카카오 클라우드 알림톡 templateParameter 누락 키 보정
     *
     * 클라우드 알림톡은 카카오 서버가 templateParameter의 key-value로 본문과 버튼 URL의 #{변수}를 치환한다.
     * replaceArguments에 누락된 키가 있으면 templateParameter에도 빠져 #{변수}가 그대로 노출되므로,
     * 템플릿의 치환코드 목록을 순회하며 누락된 키를 빈값으로 채운다.
     *
     * @param array $replaceCodes 템플릿의 치환코드 목록 (예: ['#{name}', '#{shopUrl}'])
     */
    protected function fillMissingCloudReplaceCodesWithEmpty(array $replaceCodes): void
    {
        foreach ($replaceCodes as $replaceCode) {
            if (preg_match('/#\{(.*?)\}/', $replaceCode, $matches) && isset($matches[1])) {
                if (!array_key_exists($matches[1], $this->replaceArguments)) {
                    $this->replaceArguments[$matches[1]] = '';
                }
            }
        }
    }

    /**
     * 카카오 알림톡 비즈엠 발송
     * 검증 → 수신자 해석 → 템플릿 조회 → 내용 치환 → SMS 대체 콘텐츠 → 예약 시간 → 버튼 → 발송
     */
    protected function processKakaoBizmSend(string $recipientKey): ?array
    {
        // 채널 검증
        $this->validateChannelConfig(self::KAKAO_ALRIM_BIZM);
        $this->validateOrderAndNightChecks(self::KAKAO_ALRIM_BIZM);
        $originalReplaceArguments = $this->replaceArguments;
        $this->replaceBoardArguments();

        // 수신자 해석
        $resolved = $this->resolveReceiverData($recipientKey);
        if ($resolved === null) {
            return null;
        }
        $receiverData = $resolved['receiverData'];
        $logData = $resolved['logData'];

        // 템플릿 조회
        $item = $this->fetchTemplateForRecipient($recipientKey, self::KAKAO_ALRIM_BIZM);
        if ($item === null) {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수신대상[%s] 카카오(비즈엠) 템플릿 없음', $this->smsAutoCodeType, $recipientKey));
            return null;
        }

        // 미승인 또는 중지된 템플릿 체크
        if (strtoupper($item['inspectionStatus'] ?? '') !== 'APR' || strtoupper($item['status'] ?? '') === 'S') {
            $this->logger->warning(sprintf(
                '[자동알림] 코드[%s] 수신대상[%s] 카카오(비즈엠) 템플릿 사용 불가 - 검수상태[%s], 상태[%s]',
                $this->smsAutoCodeType, $recipientKey, $item['inspectionStatus'] ?? '', $item['status'] ?? ''
            ));
            return null;
        }

        // 내용 치환
        $contents = AutoSendTemplateVariableReplacer::replaceKakaoCodeToValue(
            $item['contents'], $this->replaceArguments, $this->smsAutoCodeType, AutoSendChannel::KAKAO_ALRIM_TALK
        );
        $contents = AutoSendTemplateVariableReplacer::normalizeNewlines($contents);

        if (empty($contents)) {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수신대상[%s] 카카오(비즈엠) 치환 후 내용이 비어있음', $this->smsAutoCodeType, $recipientKey));
            return null;
        }

        // 예약 시간
        $this->setKakaoAlrimAutoReserveTime(self::KAKAO_ALRIM_BIZM);

        // 로그 데이터
        $logData = $this->buildSendLogData($logData);

        // 대체 발송 처리
        $replacedKakaoContents = EmojiRemover::remove(
            AutoSendTemplateVariableReplacer::replaceCodeToValue($contents, $this->replaceArguments, $this->smsAutoCodeType, AutoSendChannel::KAKAO_ALRIM_TALK)
        );
        $kakaoSmsOverFlag = 'none';
        if ($this->shouldKakaoSmsAlternativeSend()) {
            if ($this->shouldKakaoAlternativeBySmsContent()) {
                $smsContentForRecipient = $this->fetchSmsContentForRecipient($recipientKey);
                if ($smsContentForRecipient !== null) {
                    $replacedKakaoContents = AutoSendTemplateVariableReplacer::replaceCodeToValue(
                        $smsContentForRecipient['contents'], $originalReplaceArguments, $this->smsAutoCodeType, AutoSendChannel::SMS
                    );
                }
                $kakaoSmsOverFlag = SmsAutoSendOverType::LIMIT->value;
                if ($this->findAutoSendConfigService->isOverSmsLength($replacedKakaoContents)) {
                    $kakaoSmsOverFlag = $this->findAutoSendConfigService->getSmsSendOverFlag($this->smsAutoConfig)->value;
                }
            } else {
                $kakaoSmsOverFlag = SmsAutoSendOverType::LIMIT->value;
                if ($this->findAutoSendConfigService->isOverSmsLength($replacedKakaoContents)) {
                    $kakaoSmsOverFlag = $this->findAutoSendConfigService->convertCrmToSendOverFlag($this->kakaoCrmConfig);;
                }
            }
        } else {
            $replacedKakaoContents = '';
        }

        $oKakao = new \Component\Member\KakaoAlrim;
        $item['alternativeSmsAutoSendOverFl'] = $kakaoSmsOverFlag;
        $item['alternativeContents'] = AutoSendTemplateVariableReplacer::normalizeNewlines($replacedKakaoContents);
        $smsUtil = \App::load('Component\\Sms\\SmsUtil');
        $aSender = $smsUtil->getSender();
        $aSmsLog = $this->makeSmsLog($item, $logData, $receiverData, 'kakao', true);
        $aLogData = $logData;
        foreach ($receiverData as $k => $v) {
            $receiverData[$k]['smsFl'] = 'n';
        }
        $receiverForSaveSmsSendList = $receiverData;

        // 버튼 데이터
        if (empty($item['button']) === false) {
            $aSmsLog['templateButton'] = $item['button'];
            $buttonData = $this->buildKakaoBizmButtonData($item['button']);
            if ($buttonData !== null) {
                $aSmsLog['buttonData'] = $buttonData;
            }
        }

        // 발송
        $oKakao->sendKakaoAlrim($aSmsLog, $aSender, $aLogData, $receiverForSaveSmsSendList, $this->replaceArguments, $contents);

        return ['success' => 1, 'fail' => 0];
    }

    /**
     * SMS 발송
     * 검증 → 수신자 해석 → 템플릿 조회 → 내용 치환 → 발송
     */
    protected function processSmsSend(string $recipientKey): ?array
    {
        // 채널 검증
        $this->validateChannelConfig(self::SMS);
        $this->validateOrderAndNightChecks(self::SMS);

        // 수신자 해석
        $resolved = $this->resolveReceiverData($recipientKey);
        if ($resolved === null) {
            return null;
        }
        $receiverData = $resolved['receiverData'];
        $logData = $resolved['logData'];

        // 템플릿 조회
        $item = $this->fetchTemplateForRecipient($recipientKey, self::SMS);
        if ($item === null) {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수신대상[%s] SMS 템플릿 없음', $this->smsAutoCodeType, $recipientKey));
            return null;
        }

        // 내용 치환
        $contents = AutoSendTemplateVariableReplacer::replaceCodeToValue(
            $item['contents'], $this->replaceArguments, $this->smsAutoCodeType, AutoSendChannel::SMS
        );
        if (empty($contents)) {
            $this->logger->warning(sprintf('[자동알림] 코드[%s] 수신대상[%s] SMS 치환 후 내용이 비어있음', $this->smsAutoCodeType, $recipientKey));
            return null;
        }

        // 로그 데이터
        $logData = $this->buildSendLogData($logData);

        // 발송
        $smsOverFlag = $this->findAutoSendConfigService->getSmsSendOverFlag($this->smsAutoConfig);
        $message = new \Component\Sms\SmsMessage($contents);
        if ($message->exceedSmsLength()) {
            $message = match ($smsOverFlag) {
                SmsAutoSendOverType::LMS => new \Component\Sms\LmsMessage($contents),
                SmsAutoSendOverType::DIVISION => new \Component\Sms\DivisionSmsMessage($contents),
                default => new \Component\Sms\SmsMessage($contents),
            };
        }

        $transport = \App::load(SmsSender::class);
        if ($this->isAutoSend() || !$this->passwordCheckFl) {
            $transport->validPassword(\App::load(\Component\Sms\SmsUtil::class)->getPassword());
        }
        $transport->setSmsPoint(Sms::getPoint());
        $transport->setMessage($message);
        $transport->setSmsType($item['smsType']);
        $transport->setReceiver($receiverData);
        $transport->setSendDate($this->smsAutoSendDate);
        $transport->setTranType($this->smsAutoTranType);
        $transport->setLogData($logData);
        $transport->setContentsMask($this->contentsMask);
        if ($this->smsType == SmsAutoCode::MEMBER && $this->smsAutoCodeType == Code::PASS_AUTH) {
            $transport->setMsgType('auth');
        }
        return $transport->send();
    }

    /**
     * 카카오 비즈엠 버튼 데이터 생성
     * 페이지링크에 송장번호 치환코드가 있는 경우 치환 처리
     */
    private function buildKakaoBizmButtonData(string $buttonJson): ?string
    {
        $buttons = json_decode($buttonJson, true);
        $buttonData = [];
        foreach ($buttons as $key => $val) {
            $buttonData[$key]['name'] = $val['name'];
            $buttonData[$key]['type'] = $val['linkType'];
            if ($val['linkType'] === 'WL') {
                $val['linkMo'] = str_replace('#{invoiceNo}', $this->replaceArguments['invoiceNo'] ?? '', $val['linkMo']);
                $buttonData[$key]['url_mobile'] = $val['linkMo'];
            }
        }
        if (empty($buttonData)) {
            return null;
        }
        return json_encode($buttonData, JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
    }
}
