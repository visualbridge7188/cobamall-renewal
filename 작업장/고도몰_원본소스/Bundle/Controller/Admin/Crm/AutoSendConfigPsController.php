<?php

namespace Bundle\Controller\Admin\Crm;

use App;
use Bundle\Component\Sms\SmsAutoCode;
use Exception;
use Origin\DTO\AutoSend\AutoSendOptionCodeConfigDto;
use Origin\Enum\AutoSend\AutoSendChannel;
use Origin\Enum\AutoSend\AutoSendChannelPolicy;
use Origin\Enum\AutoSend\AutoSendRecipient;
use Origin\Enum\AutoSend\AutoSendRecipientPolicy;
use Origin\Enum\AutoSend\AutoSendSupport;
use Origin\Service\AutoSend\FindAutoSendConfigService;
use Origin\Service\AutoSend\Request\UpdateAutoSendConfigRequest;
use Origin\Service\AutoSend\SaveAutoSendConfigService;
use Origin\Service\AutoSend\SaveAutoSendTemplateService;
use Origin\Service\Member\KakaoAlrim\KakaoAlrimCloudSyncService;
use Request;

class AutoSendConfigPsController extends \Controller\Admin\Controller
{
    public function index()
    {
        $request = Request::post()->toArray();
        $files = Request::files()->toArray();
        $mode = $request['mode'] ?? ''; // todo throw
        $code = $request['code'];
        $options = $request['options'] ?? [];

        try {
            switch ($mode) {
                case 'save':
                    $configOptions = AutoSendOptionCodeConfigDto::createCodeConfigByOption($code, $options);
                    /** @var SmsAutoCode $smsAutoCode */
                    $smsAutoCode = App::load('Component\\Sms\\SmsAutoCode');
                    $smsAutoCode->getCodes();
                    /** @var SaveAutoSendConfigService $saveAutoSendConfigService */
                    $saveAutoSendConfigService = App::getInstance(SaveAutoSendConfigService::class);
                    $channels = AutoSendChannelPolicy::getCodesWithChannelsWithBoard()[$code] ?? [];
                    /** @var SaveAutoSendTemplateService $saveAutoSendTemplateService */
                    $saveAutoSendTemplateService = App::getInstance(SaveAutoSendTemplateService::class);
                    $recipientFiles = $files['recipients'] ?? [];
                    $flagMap = [];
                    $templateItems = [];

                    foreach ($request['recipients'] as $requestRecipientName => $requestRecipientConfig) {
                        $autoSendRecipient = AutoSendRecipient::fromName(strtoupper($requestRecipientName));
                        foreach ($channels as $channel) {
                            $channelConfig = $requestRecipientConfig[$channel->name] ?? [];
                            if (!empty($channelConfig)) {
                                $imageMode = $channelConfig['imageMode'] ?? null;
                                if ($imageMode === 'FILE') {
                                    $uploadedFile = $this->extractUploadedFile($recipientFiles, $requestRecipientName, $channel->name);
                                    if ($uploadedFile !== null) {
                                        $channelConfig['uploadedImage'] = $uploadedFile;
                                    }
                                }

                                $flagMap[] = [$channel, $requestRecipientName, $channelConfig['isEnabled'] ?? 'n'];
                                $templateItems[] = [
                                    'channel' => $channel,
                                    'recipient' => $autoSendRecipient,
                                    'config' => $channelConfig,
                                ];
                            }
                        }
                    }


                    $insertedSnoMap = \DB::transaction(function () use ($saveAutoSendConfigService, $code, $flagMap, $configOptions, $saveAutoSendTemplateService, $templateItems) {
                        $saveAutoSendConfigService->updateAutoSendConfigBatch($code, $flagMap, $configOptions);
                        $insertedSnoMap = $saveAutoSendTemplateService->updateTemplatesBatch($code, $templateItems);

                        /** @var FindAutoSendConfigService $findAutoSendConfigService */
                        $findAutoSendConfigService = App::getInstance(FindAutoSendConfigService::class);

                        if ($findAutoSendConfigService->getActiveCrmKakaoAlrimSender() == 'kakaoAlrimCloud') {
                            /** @var KakaoAlrimCloudSyncService $kakaoAlrimCloudSyncService */
                            $kakaoAlrimCloudSyncService = App::getInstance(KakaoAlrimCloudSyncService::class);
                            $kakaoAlrimCloudSyncService->syncMessageTemplate();
                        }

                        return $insertedSnoMap;
                    });

                    $this->json(['result'=> true, 'message' => '', 'insertedSnoMap' => $insertedSnoMap]);

                    break;

                default:
                    break;
            }
        } catch (Exception $exception) {
            $this->json(['result'=> false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * $_FILES 중첩 배열에서 수신자/채널별 업로드 파일 정보 추출
     *
     * @param array $recipientFiles $_FILES['recipients']
     * @param string $recipientName 수신자 (member, admin, provider)
     * @param string $channelName 채널 (SMS, KAKAO_ALRIM_TALK, MYAPP_PUSH)
     * @return array|null ['name', 'type', 'tmp_name', 'error', 'size'] 또는 null
     */
    private function extractUploadedFile(array $recipientFiles, string $recipientName, string $channelName): ?array
    {
        $tmpName = $recipientFiles['tmp_name'][$recipientName][$channelName]['image'] ?? null;
        $error = $recipientFiles['error'][$recipientName][$channelName]['image'] ?? UPLOAD_ERR_NO_FILE;
        $size = $recipientFiles['size'][$recipientName][$channelName]['image'] ?? 0;

        if (empty($tmpName) || (int)$error === UPLOAD_ERR_NO_FILE || (int)$size === 0) {
            return null;
        }

        return [
            'name' => $recipientFiles['name'][$recipientName][$channelName]['image'] ?? '',
            'type' => $recipientFiles['type'][$recipientName][$channelName]['image'] ?? '',
            'tmp_name' => $tmpName,
            'error' => $error,
            'size' => $recipientFiles['size'][$recipientName][$channelName]['image'] ?? 0,
        ];
    }
}