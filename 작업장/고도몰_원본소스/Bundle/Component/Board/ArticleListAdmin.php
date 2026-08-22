<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Smart to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */
namespace Bundle\Component\Board;

use Component\Database\DBTableField;
use Component\Page\Page;
use Framework\Utility\ArrayUtils;
use Session;
use Request;

class ArticleListAdmin extends \Component\Board\ArticleAdmin
{
    private $_page;
    private $_cfg = [];
    /**
     * Description
     * @param unknown $req
     */
    public function __construct($req)
    {
        parent::__construct($req);

        if (!isset($this->req['page'])) {
            $this->req['page'] = 1;
        } else {
            $this->req['page'] = preg_replace('/[^0-9]*/', '', $this->req['page']);
        }

        // 값이 없는 경우 미리 체크
        gd_isset($this->cfg['bdListCnt'], 20);
        gd_isset($this->cfg['bdListCols'], 5);
        gd_isset($this->cfg['bdListRows'], 4);
        gd_isset($this->cfg['bdListImgWidth'], 45);
        gd_isset($this->cfg['bdListImgHeight'], 45);

        if ($this->canList() != 'y') {
            throw new \Exception(sprintf(parent::TEXT_NOTHAVE_AUTHORITY, __('글 목록')));
        }
    }

    /**
     * Description
     * 게시판 엑셀다운로드
     * @return unknown
     */
    public function getExcelList($getValue = null)
    {
        $where = '';
        $arrBind = [];

        if (gd_isset($getValue['searchWord'])) {
            switch ($getValue['searchField']) {
                case 'subject' :
                    $arrWhere[] = "subject LIKE concat('%',?,'%')";
                    $this->db->bind_param_push($arrBind, 's', $getValue['searchWord']);
                    break;
                case 'contents' :
                    $arrWhere[] = "contents LIKE concat('%',?,'%')";
                    $this->db->bind_param_push($arrBind, 's', $getValue['searchWord']);
                    break;
                case 'writerNm' :

                    if ($this->_cfg['bdUserDsp'] == 'name') {
                        $_searchField = 'writerNm';
                    } else {
                        $_searchField = 'writerNick';
                    }

                    $arrWhere[] = $_searchField." LIKE concat('%',?,'%')";
                    $this->db->bind_param_push($arrBind, 's', $getValue['searchWord']);
                    break;
                case 'subject_contents' :
                    $arrWhere[] = "(subject LIKE concat('%',?,'%') or contents LIKE concat('%',?,'%') )";
                    $this->db->bind_param_push($arrBind, 's', $getValue['searchWord']);
                    $this->db->bind_param_push($arrBind, 's', $getValue['searchWord']);
                    break;
            }
        }

        if (gd_isset($getValue['rangDate'][0]) && gd_isset($getValue['rangDate'][1])) {
            if ($getValue['searchDateFl'] == 'modDt') {
                $dateField = 'bd.modDt';
            } else {
                $dateField = 'bd.regDt';
            }
            $arrWhere[] = $dateField . " between ? AND ?";
            $this->db->bind_param_push($arrBind, 's', $getValue['rangDate'][0]);
            $this->db->bind_param_push($arrBind, 's', $getValue['rangDate'][1] . ' 23:59');
        }

        if(gd_isset($getValue['sno'])) {
            $arrWhere[] = ' bd.sno in (' . gd_implode(',', array_map('intval', (array)$getValue['sno'])) . ')';
        }

        if(gd_isset($getValue['category'])) {
            $arrWhere[] = ' bd.category = ?';
            $this->db->bind_param_push($arrBind, 's', $getValue['category']);
        }

        // 신고 게시글은 무조건 미노출
        $arrWhere[] = ' bd.isShow = \'y\'';

        if(gd_isset($arrWhere)) $where = " WHERE  ".gd_implode(' AND ', gd_isset($arrWhere));

        if(gd_is_provider()) {
            $sql = "SELECT bd.*,g.*,mg.groupNm,m.groupSno FROM " . DB_BD_ . $this->cfg['bdId'] ." as bd JOIN ".DB_GOODS." as g on bd.goodsNo = g.goodsNo AND g.scmNo ='".Session::get('manager.scmNo')."' LEFT OUTER JOIN ".DB_MEMBER." as m ON bd.memNo = m.memNo LEFT OUTER JOIN ".DB_MEMBER_GROUP." as mg ON m.groupSno = mg.sno". $where;
        }
        else {
            $sql = "SELECT bd.*,mg.groupNm,m.groupSno FROM " . DB_BD_ . $this->cfg['bdId'] ." as bd LEFT OUTER JOIN ".DB_MEMBER." as m ON bd.memNo = m.memNo LEFT OUTER JOIN ".DB_MEMBER_GROUP." as mg ON m.groupSno = mg.sno ". $where;
        }

        $getData['list'] = gd_htmlspecialchars_stripslashes($this->db->query_fetch($sql, $arrBind));
        foreach ($getData['list'] as &$data) {
            $this->getAttachments($data);
        }

        $getData['columns'] = gd_htmlspecialchars_stripslashes($this->db->query_fetch("DESC " . DB_BD_ . $this->cfg['bdId']));


        return $getData['list'];

    }

    public function getList($isPaging = true, $listCount = 0, $subjectCut = 0, $arrWhere = [], $arrInclude = null,$displayNotice = false)
    {
        return parent::getList($isPaging, $listCount, $subjectCut, $arrWhere, $arrInclude, $displayNotice);
    }

    public function getReportMemoList()
    {
        return parent::getReportMemoList();
    }
}
