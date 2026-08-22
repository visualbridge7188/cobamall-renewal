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

namespace Bundle\Component\Mail;

use Component\Database\DBTableField;
use Component\Member\Manager;
use Component\Validator\Validator;
use Exception;
use Framework\Object\SimpleStorage;
use Framework\Utility\StringUtils;
use Session;

/**
 * 메일발송내역
 * @package Bundle\Component\Mail
 * @author  yjwee
 */
class MailLog extends \Component\AbstractComponent
{
    protected $memberDAO;
    protected $arrBind;
    protected $arrWhere;
    /** @var int es_mailLog sno */
    private $_sno;
    /** @var int|null 직전 insertMailSendList 로 INSERT 한 es_mailSendList 의 PK(sno). updateSendList PK 단건 갱신용 */
    private $sendListSno = null;
    /** @var  SimpleStorage */
    private $requestStorage;

    public function __construct($config = [])
    {
        // 기존 DBTool 파라미터를 보장하기 위한 로직
        if (is_array($config)) {
            $this->db = is_object($config['db']) ? $config['db'] : $db = \App::getInstance('DB');;
            $this->memberDAO = is_object($config['memberDAO']) ? $config['memberDAO'] : \App::load('Component\\Member\\MemberDAO');
        } elseif ($config instanceof \Framework\Database\DBTool) {
            $this->db = $config;
        }
        parent::__construct($this->db);
    }


    /**
     * 메일발송내역 목록 조회
     *
     * @param array $requestParams
     *
     * @param int   $offset
     * @param int   $limit
     *
     * @return mixed
     */
    public function getLogList(array $requestParams, $offset = 0, $limit = 10)
    {
        $this->requestStorage = new SimpleStorage($requestParams);
        $this->arrBind = $this->arrWhere = [];

        $arrField = DBTableField::setTableField('tableMailLog', null, ['contents'], 'ml');
        $this->db->strField = 'ml.sno, ' . gd_implode(', ', $arrField) . ', ml.regDt, m.isDelete';

        $this->_bindListParamsBySearchCondition($this->arrBind, $this->arrWhere);
        if ($this->arrWhere) {
            $this->db->strWhere = gd_implode(' AND ', $this->arrWhere);
        }

        $this->db->strLimit = '?, ?';
        $arrBind = $this->arrBind;
        $this->db->bind_param_push($arrBind, 'i', (($offset - 1) * $limit));
        $this->db->bind_param_push($arrBind, 'i', $limit);

        $this->db->strOrder = $this->requestStorage->get('sort', 'regDt DESC');

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MAIL_LOG . ' as ml LEFT OUTER JOIN ' . DB_MANAGER . ' as m ON ml.managerNo = m.sno ' . gd_implode(' ', $query);
        $resultSet = StringUtils::htmlSpecialCharsStripSlashes($this->db->query_fetch($strSQL, $arrBind));
        Manager::displayListData($resultSet);

        return $resultSet;
    }

    /**
     * 관리자 메일발송내역 리스트 검색결과 카운트 함수
     * \Component\Mail\MailLog::getLogList 함수가 실행되어야 검색조건을 참조한다.
     *
     * @return int
     * @throws \Framework\Debug\Exception\DatabaseException
     */
    public function foundRowsByLogList()
    {
        $db = \App::getInstance('DB');
        $db->strField = 'COUNT(*) AS cnt';
        $db->strWhere = gd_implode(' AND ', $this->arrWhere);
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MAIL_LOG . ' as ml LEFT OUTER JOIN ' . DB_MANAGER . ' as m ON ml.managerNo = m.sno ' . gd_implode(' ', $query);
        $resultSet = $this->db->query_fetch($strSQL, $this->arrBind, false);
        StringUtils::strIsSet($resultSet['cnt'], 0);

        return $resultSet['cnt'];
    }

    /**
     * 관리자 CRM 회원 메일발송 내역 조회
     *
     * @param array $requestParams
     * @param int   $offset
     * @param int   $limit
     *
     * @return array|string
     */
    public function getCrmLogList(array $requestParams, $offset = 0, $limit = 10)
    {
        $this->arrBind = $this->arrWhere = [];

        $this->db->bindParameter('memNo', $requestParams, $this->arrBind, $this->arrWhere, 'tableMailSendList', 'ms');
        if ($requestParams['searchKind'] == 'equalSearch') {
            if ($requestParams['key'] == 'sender') {
                $this->db->bindParameter('keyword', $requestParams, $this->arrBind, $this->arrWhere, 'tableMailLog', 'ml', 'sender');
            } else if ($requestParams['key'] == 'subject') {
                $this->db->bindParameter('keyword', $requestParams, $this->arrBind, $this->arrWhere, 'tableMailLog', 'ml', 'subject');
            } else {
                $this->db->bindParameterByEqualKeyword(MailAdmin::CRM_KEYS, $requestParams, $this->arrBind, $this->arrWhere, 'tableMailLog', 'ml');
            }
        } else {
            $this->db->bindParameterByKeyword(MailAdmin::CRM_KEYS, $requestParams, $this->arrBind, $this->arrWhere, 'tableMailLog', 'ml');
        }
        $this->db->bindParameter('mailType', $requestParams, $this->arrBind, $this->arrWhere, 'tableMailLog', 'ml');
        $this->db->bindParameter('sendType', $requestParams, $this->arrBind, $this->arrWhere, 'tableMailLog', 'ml');
        $this->db->bindParameter('sendStatus', $requestParams, $this->arrBind, $this->arrWhere, 'tableMailLog', 'ml');
        $this->db->bindParameterByDateTimeRange('regDt', $requestParams, $this->arrBind, $this->arrWhere, 'tableMailLog', 'ml');

        $this->db->strField = 'ml.*';
        $this->db->strWhere = gd_implode(' AND ', $this->arrWhere);
        $this->db->strOrder = 'ml.' . $requestParams['sort'];
        $this->db->strJoin = ' JOIN ' . DB_MAIL_SEND_LIST . ' AS ms ON ml.sno = ms.mailLogSno';

        if (is_null($offset) === false && is_null($limit) === false) {
            $this->db->strLimit = ($offset - 1) * $limit . ', ' . $limit;
        }

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MAIL_LOG . ' AS ml ' . gd_implode(' ', $query);
        $resultSet = StringUtils::htmlSpecialCharsStripSlashes($this->db->query_fetch($strSQL, $this->arrBind));

        return $resultSet;
    }

    /**
     * 관리자 회원 CRM 메일발송내역 검색결과 카운트 함수
     * \Component\Mail\MailLog::getCrmLogList 함수가 실행되어야 검색조건을 참조한다.
     *
     * @return int
     * @throws \Framework\Debug\Exception\DatabaseException
     */
    public function foundRowsByCrmLogList()
    {
        $db = \App::getInstance('DB');
        $db->strField = 'COUNT(*) AS cnt';
        $db->strWhere = gd_implode(' AND ', $this->arrWhere);
        $db->strJoin = ' JOIN ' . DB_MAIL_SEND_LIST . ' AS ms ON ml.sno = ms.mailLogSno';
        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MAIL_LOG . ' AS ml ' . gd_implode(' ', $query);
        $resultSet = $this->db->query_fetch($strSQL, $this->arrBind, false);
        StringUtils::strIsSet($resultSet['cnt'], 0);

        return $resultSet['cnt'];
    }

    /**
     * 메일발송 내역 상세조회
     *
     * @param $sno
     *
     * @return string
     */
    public function getLogData($sno)
    {
        // --- 목록
        $this->db->strField = "*";
        $this->db->strWhere = "sno=?";
        $this->db->bind_param_push($arrBind, 'i', $sno);

        $query = $this->db->query_complete();
        $strSQL = 'SELECT ' . array_shift($query) . ' FROM ' . DB_MAIL_LOG . ' ' . gd_implode(' ', $query);
        $data = $this->db->query_fetch($strSQL, $arrBind, false);

        return StringUtils::htmlSpecialCharsStripSlashes(gd_isset($data));
    }

    /**
     * @deprecated 기능 개선 시 입력받는 데이터가 증가하면 함수 수정이 필요한 이슈가 있음.
     * @uses       insertMailLogByArray
     *
     * 메일로그 추가
     *
     * @param $subject
     * @param $contents
     * @param $receiver
     * @param $rcverCnt
     * @param $rcverCondition
     * @param $sendType
     *
     * return int|string
     */
    public function insertMailLog($subject, $contents, $receiver, $rcverCnt, $rcverCondition, $sendType = '')
    {
        $sender = '';
        if ($sendType !== null) {
            $manager = Session::get('manager');
            $sender = $manager['managerId'];
            $managerNo = $manager['sno'];
        }

        $arrData = [
            'subject'           => $subject,
            'contents'          => $contents,
            'receiver'          => $receiver,
            'receiverCnt'       => $rcverCnt,
            'receiverCondition' => $rcverCondition,
            'sendType'          => $sendType,
            'sender'            => $sender,
            'managerNo'         => $managerNo,
        ];

        // 저장
        $arrBind = $this->db->get_binding(DBTableField::tableMailLog(), $arrData, 'insert');
        $this->db->set_insert_db(DB_MAIL_LOG, $arrBind['param'], $arrBind['bind'], 'y');
        unset($arrBind);
        unset($arrData);

        $this->_sno = $this->db->insert_id();
    }

    /**
     * 메일로그 추가
     *
     * @param array $log ['subject','contents','receiver','receiverCnt','receiverCondition','sendType','category','mailType','sender','managerNo']
     *
     * @return int|string
     */
    public function insertMailLogByArray(array $log)
    {
        if (Session::has(Manager::SESSION_MANAGER_LOGIN)) {
            $manager = Session::get(Manager::SESSION_MANAGER_LOGIN);
            $log['managerNo'] = gd_isset($manager['sno'], 0);
            $log['sender'] = gd_isset($manager['managerId'], '');
        }
        $arrBind = $this->db->get_binding(DBTableField::tableMailLog(), $log, 'insert');
        $this->db->set_insert_db(DB_MAIL_LOG, $arrBind['param'], $arrBind['bind'], 'y');
        $this->_sno = $this->db->insert_id();

        return $this->_sno;
    }

    /**
     * 메일발송대상 리스트 저장
     *  2016-11-08 yjwee 회원번호가 안넘어오는 경우 메일정보를 이용하여 회원조회를 시도하는 로직 추가
     *
     * @param $memberInfo
     */
    public function insertMailSendList($memberInfo)
    {
        StringUtils::strIsSet($memberInfo['memNo'], 0);
        if ($memberInfo['memNo'] == 0) {
            $memberByEmail = $this->memberDAO->selectMemberByOne($memberInfo['email'], 'email');
            if ($memberByEmail['memNo'] > 0) {
                $memberInfo['memNo'] = $memberByEmail['memNo'];
            }
        }
        $bindArray['mailLogSno'] = $this->_sno;
        $bindArray['memNo'] = $memberInfo['memNo'];
        $bindArray['receiverName'] = $memberInfo['memNm'];
        $bindArray['receiverEmail'] = $memberInfo['email'];
        $bindArray['receiverMailFl'] = $memberInfo['maillingFl'];
        $bindArray['sendCheckFl'] = 'r';
        $bindArray['acceptCheckFl'] = 'n';

        $arrBind = $this->db->get_binding(DBTableField::tableMailSendList(), $bindArray, 'insert');
        $this->db->set_insert_db(DB_MAIL_SEND_LIST, $arrBind['param'], $arrBind['bind'], 'y');
        // 방금 INSERT 한 발송내역 PK 를 보관 → updateSendList 에서 PK 단건 갱신(데드락 회피)에 사용
        $this->sendListSno = (int) $this->db->insert_id();
    }

    /**
     * Mail 전송 내역 접수여부 update
     *
     * @param string $email
     */
    public function updateSendList($email)
    {
        // [데드락 회피] 직전 insertMailSendList 로 확보한 PK(sno)가 있으면 PK 단건 UPDATE
        // 보조인덱스(ix_es_mailSendList_05) 스캔을 제거해 잠금 순서를 PRIMARY 기준으로 통일
        // acceptCheckFl='n' 가드를 유지해 멱등성을 보장(기존 동작과 동일하게 미접수 건만 갱신)
        // 사용 후 즉시 초기화하여, INSERT 가 선행되지 않은 호출은 아래 기존 로직으로 처리
        if (!empty($this->sendListSno)) {
            $sendListSno = $this->sendListSno;
            $this->sendListSno = null;

            $arrBind = [];
            $this->db->bind_param_push($arrBind, 'i', $sendListSno);
            $this->db->set_update_db(DB_MAIL_SEND_LIST, 'acceptCheckFl = \'y\'', 'sno = ? AND acceptCheckFl = \'n\'', $arrBind);

            return;
        }

        // (fallback) PK 미확보 호출 경로 — 기존 동작 100% 유지
        $arrBind = [];
        $this->db->bind_param_push($arrBind, 's', $this->_sno);
        $this->db->bind_param_push($arrBind, 's', $email);
        $this->db->set_update_db(DB_MAIL_SEND_LIST, 'acceptCheckFl = \'y\'', 'mailLogSno = ? AND receiverEmail = ? AND acceptCheckFl = \'n\' LIMIT 1', $arrBind);
    }

    /**
     * @deprecated
     * @uses DBTableField::tableMailLog()
     *
     * es_mailLog 테이블 정보 반환
     *
     * @return mixed
     */
    public function getTableMailLog()
    {
        return DBTableField::tableMailLog();
    }

    /**
     * 메일로그 수정
     *
     * @param $succ
     */
    public function updateMailLog($succ)
    {
        $arrData['receiverCnt'] = $succ;
        $arrBind = $this->db->get_binding(DBTableField::tableMailLog(), $arrData, 'update', ['receiverCnt']);
        $this->db->bind_param_push($arrBind['bind'], 'i', $this->_sno);
        $this->db->set_update_db(DB_MAIL_LOG, $arrBind['param'], 'sno = ?', $arrBind['bind'], false);
        unset($arrBind);
    }

    /**
     * 메일로그 삭제 함수
     *
     * @param array $sno
     *
     * @throws Exception
     */
    public function deleteMailLog(array $sno)
    {
        if (gd_count($sno) < 1) {
            throw new Exception(__('선택된 로그 내역이 없습니다.'));
        }

        foreach ($sno as $key => $value) {
            if (Validator::number($value, null, null, true) === false) {
                throw new Exception(sprintf(__('유효하지 않는 %s 입니다.'), __('로그번호')));
            }

            $arrBind = [
                'i',
                $value,
            ];
            $this->db->set_delete_db(DB_MAIL_LOG, 'sno = ?', $arrBind);
        }
    }

    /**
     * 메일발송 내용 조회 함수
     *
     * @param $sno
     *
     * @return object
     * @throws Exception
     */
    public function getMailLog($sno)
    {
        if (Validator::number($sno, null, null, true) === false) {
            throw new Exception(sprintf(__('유효하지 않는 %s 입니다.'), __('로그번호')));
        }

        $arrBind = [];
        $strSQL = 'SELECT contents, contentsMask FROM ' . DB_MAIL_LOG . ' where sno = ?';
        $this->db->bind_param_push($arrBind, 'i', $sno);
        $data = $this->db->query_fetch($strSQL, $arrBind, false);
        if (!empty($data['contentsMask'])) {
            $mask = explode(STR_DIVISION, $data['contentsMask']);
            $contents = $data['contents'];
            if (is_array($mask)) {
                foreach ($mask as $item) {
                    $length = mb_strlen($item);
                    $replace = str_repeat('*', $length);
                    $contents = str_replace($item, $replace, $contents);
                }
                $data['contents'] = $contents;
            }
        }
        $contents = gd_htmlspecialchars_stripslashes(trim($data['contents']));
        $contents = str_replace('\r\n', '', $contents);


        return $contents;
    }

    /**
     * @return mixed
     */
    public function getSno()
    {
        return $this->_sno;
    }

    /**
     * @param mixed $sno
     */
    public function setSno($sno)
    {
        $this->_sno = $sno;
    }

    private function _bindListParamsBySearchCondition(&$arrBind, &$arrWhere)
    {

        $params = $this->requestStorage->all();
        if ($params['key'] && $params['keyword']) {
            if ($params['key'] == 'all') {
                if ($params['searchKind'] == 'equalSearch') {
                    $arrWhere[] = " ( subject = ? OR sender = ? OR receiver = ? ) ";
                    $this->db->bind_param_push($arrBind, 's', $params['keyword']);
                    $this->db->bind_param_push($arrBind, 's', $params['keyword']);
                    $this->db->bind_param_push($arrBind, 's', $params['keyword']);
                } else {
                    $arrWhere[] = "concat(subject, sender, receiver) LIKE concat('%',?,'%')";
                    $this->db->bind_param_push($arrBind, 's', $params['keyword']);
                }
            } else {
                if ($params['searchKind'] == 'equalSearch') {
                    $arrWhere[] = $params['key'] . " = ? ";
                } else {
                    $arrWhere[] = $params['key'] . " LIKE concat('%',?,'%')";
                }
                $this->db->bind_param_push($arrBind, 's', $params['keyword']);
            }
        }

        if ($params['regdt'][0] && $params['regdt'][1]) {
            $arrWhere[] = "ml.regDt between date_format(?,'%Y-%m-%d 00:00:00') and date_format(?,'%Y-%m-%d 23:59:59')";
            $this->db->bind_param_push($arrBind, 's', $params['regdt'][0]);
            $this->db->bind_param_push($arrBind, 's', $params['regdt'][1]);
        }

        if ($params['sendType']) {
            $arrWhere[] = "ml.sendType=?";
            $this->db->bind_param_push($arrBind, 's', $params['sendType']);
        }
    }
}
