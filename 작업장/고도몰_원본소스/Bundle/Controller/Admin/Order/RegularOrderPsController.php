<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium
 * is strictly prohibited.
 */

namespace Bundle\Controller\Admin\Order;

use Controller\Admin\Controller;
use DTO\RegularDelivery\RegularOrder\RegularOrderStatusChangeDTO;
use Framework\Debug\Exception\AlertBackException;

class RegularOrderPsController extends Controller
{
    public function index()
    {
        try {
            $postValue = \Request::post()->toArray();

            switch ($postValue['mode']) {
                // 상태변경
                case 'combine_status_change':
                    $regularOrderStatusChange = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderStatusChange::class);
                    $changeData = [
                        'applyNoList' => $postValue['applyNo'], // 배열형태로 들어옴
                        'updateStatus' => $postValue['selectedStatus'],
                        'sessionType' => 'admin',
                        'sessionSno' => \Session::get('manager.sno')
                    ];
                    $statusChangeDTO = new RegularOrderStatusChangeDTO($changeData);
                    $regularOrderStatusChange->updateApplyStatus($statusChangeDTO);
                    $this->json(['result' => 'success', 'message' => '이용상태 변경처리가 완료되었습니다.']);
                case 'single_status_change':
                    $regularOrderStatusChange = \App::getInstance(\Component\RegularDelivery\RegularOrder\RegularOrderStatusChange::class);
                    $changeData = [
                        'applyNoList' => [$postValue['applyNo']], // 배열형태로 변경
                        'updateStatus' => $postValue['selectedStatus'],
                        'sessionType' => 'admin',
                        'sessionSno' => \Session::get('manager.sno')
                    ];
                    $statusChangeDTO = new RegularOrderStatusChangeDTO($changeData);
                    $regularOrderStatusChange->updateApplyStatus($statusChangeDTO);
                    $this->json(['result' => 'success', 'message' => '이용상태 변경처리가 완료되었습니다.']);
                case 'modify_applier_info':
                    $regularOrderAdminDetailHandler = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetailHandler::class);
                    $regularOrderAdminDetailHandler->updateApplierInfo($postValue);
                    $this->layer(__('저장이 완료되었습니다.'), null, 2000);
                    break;
                case 'modify_consult_memo' :
                    $regularOrderAdminDetailHandler = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetailHandler::class);
                    $regularOrderAdminDetailHandler->updateConsultMemo($postValue);
                    $this->layer(__('저장이 완료되었습니다.'), null, 2000);
                    break;
                case 'delete_consult_memo' :
                    $regularOrderAdminDetailHandler = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetailHandler::class);
                    $regularOrderAdminDetailHandler->deleteConsultMemo($postValue);
                    $this->json(['result' => 'success', 'message' => '삭제가 완료되었습니다.']);
                    break;
                case 'admin_memo_save' :
                    $regularOrderAdminDetailHandler = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetailHandler::class);
                    $regularOrderAdminDetailHandler->saveAdminMemo($postValue);
                    $this->json(['result' => 'success', 'message' => '저장이 완료되었습니다.']);
                    break;
                case 'admin_memo_modify' :
                    $regularOrderAdminDetailHandler = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetailHandler::class);
                    $regularOrderAdminDetailHandler->updateAdminMemo($postValue);
                    $this->json(['result' => 'success', 'message' => '저장이 완료되었습니다.']);
                    break;
                case 'admin_memo_delete' :
                    $regularOrderAdminDetailHandler = \App::getInstance(\Component\RegularDelivery\RegularOrderAdmin\RegularOrderAdminDetailHandler::class);
                    $regularOrderAdminDetailHandler->deleteAdminMemo($postValue);
                    $this->json(['result' => 'success', 'message' => '삭제 완료되었습니다.']);
                    break;
                default:
                    throw new \Exception('정기결제 신청서 관련 모드가 정의되지 않았습니다.');
            }

        } catch (\Throwable $e) {
            $this->json(['result' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
