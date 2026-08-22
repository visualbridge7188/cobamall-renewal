<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Component\Present\Card;

use Component\Present\Exception\PresentCardDisplayException;
use Component\Present\Exception\PresentCardLimitOverException;
use Component\Present\Exception\PresentCardMessageLengthException;
use Component\Present\Exception\PresentCardValidationException;
use Carbon\Carbon;
use DTO\Present\Card\PresentCardDTO;
use DTO\Present\Card\PresentUpdateCardDTO;
use Framework\File\FileValidator;
use Framework\Log\Logger;
use Framework\ObjectStorage\Service\ImageUploadService;
use Framework\Utility\ImageUtils;
use Repository\Present\Card\PresentCardRepository;
use InvalidArgumentException;

class PresentCard
{
    const MAX_FILE_SIZE = 512000;  # 500KB
    const MIN_WIDTH = 500;
    const MIN_HEIGHT = 250;
    const UPLOAD_CARD_LIMIT = 10;
    const ASPECT_RATIO = 2;
    const MIN_DISPLAY_CARD_COUNT = 1;
    const MIN_CARD_MESSAGE_LENGTH = 1;

    public function __construct(
        protected readonly Logger       $logger,
        protected PresentCardRepository $presentCardRepository,
        protected ImageUploadService    $imageUploadService,
    )
    {
    }

    /**
     * 카드 저장, 업데이트, 삭제
     *
     * @param array $cards
     * @param array|null $imageFiles
     * @return bool
     * @throws PresentCardDisplayException
     * @throws \Exception
     */
    public function save(array $cards, ?array $imageFiles): bool
    {
        // 카드 노출 여부 검사
        $this->validateCardDisplayFl($cards);

        // 카드 메시지 길이
        $this->validCardMessageLength($cards);

        $this->insertCard($cards, $imageFiles);
        $this->updateCard($cards);
        $this->deleteCard($cards);

        return true;
    }

    /**
     * 선물하기 카드 삽입
     *
     * @param array $cards
     * @param array|null $imageFiles
     * @return void
     * @throws PresentCardValidationException
     * @throws PresentCardLimitOverException
     * @throws \Exception
     */
    protected function insertCard(array $cards, ?array $imageFiles): void
    {
        // 인서트 데이터만 대상으로
        if (empty($cards['insert']) || empty($imageFiles)) {
            return;
        }
        ksort($cards['insert']);
        $insertCards = $cards['insert'];

        // 카드 개수 확인
        $this->validateCardCount($cards);

        // 새로운 카드 삽입
        $cardImageFiles = $this->convertFileArray($imageFiles);

        // 파일 유효성 검사
        foreach ($cardImageFiles as $cardImageFile) {
            $this->checkImageRatio($cardImageFile['tmp_name']);
            $this->checkImageMime($cardImageFile['tmp_name']);
            $this->checkImageFileBytes((int)$cardImageFile['size']);
        }

        // 인덱스 번호를 이용하여 insert 데이터에 imageUrl 넣어주기
        foreach ($cardImageFiles as $index => $cardImageFile) {
            if (empty($insertCards[$index])) {
                continue;
            }
            $insertCards[$index]['imageUrl'] = $this->cardImageUpload($cardImageFile);
        }

        $this->presentCardRepository->insert($insertCards);
    }

    /**
     * 선물하기 카드 수 유효성 검사
     *
     * @param array $cards
     * @return void
     * @throws PresentCardLimitOverException
     */
    protected function validateCardCount(array $cards): void
    {
        // 삭제 요청 카드 sno
        $requestedDeleteSnos = explode(',', $cards['delete']);

        // 기본 제공이 아닌 카드
        $existingNonDefaultCardSnos = $this->presentCardRepository->findNotDefaultCardSnos();

        // 실제 삭제될 카드 수
        $deleteCardCount = count(array_intersect($requestedDeleteSnos, $existingNonDefaultCardSnos));

        // 저장될 카드 수
        $insertCard = $cards['insert'] ?: [];
        $insertCardCount = count($insertCard);

        // DB에 저장된 수 + 삽입될 카드 수 - 삭제될 카드 수
        $calculatedFinalCardCount = count($existingNonDefaultCardSnos) + $insertCardCount - $deleteCardCount;

        if ($calculatedFinalCardCount > self::UPLOAD_CARD_LIMIT) {
            throw new PresentCardLimitOverException('card limit over');
        }
    }

    /**
     * 선물하기 카드 노출여부 유효성 검사
     *
     * @param array $cards
     * @return void
     * @throws PresentCardDisplayException
     */
    protected function validateCardDisplayFl(array $cards): void
    {
        $displayFlCount = 0;

        $insertCard = $cards['insert'] ?: [];
        $displayFlCount += $this->displayFlCount($insertCard);
        $displayFlCount += $this->displayFlCount($cards['update']);

        if ($displayFlCount < 1) {
            throw new PresentCardDisplayException('card displayFl error');
        }
    }

    /**
     * 선물하기 노출 여부 카운트
     *
     * @param array $cards
     * @return int
     */
    protected function displayFlCount(array $cards): int
    {
        $displayFlCount = 0;

        foreach ($cards as $valueArray) {
            if (($valueArray['displayFl']) === 'y') {
                $displayFlCount++;
            }
        }

        return $displayFlCount;
    }

    /**
     * 카드 메시지 길이 검증
     *
     * @param array $cards
     * @return void
     * @throws \Exception
     */
    protected function validCardMessageLength(array $cards): void
    {
        foreach (['insert', 'update'] as $type) {
            foreach ($cards[$type] as $valueArray) {
                $messageLength = $this->countCardMessageLength($valueArray['message']);

                if ($messageLength < self::MIN_CARD_MESSAGE_LENGTH) {
                    throw new PresentCardMessageLengthException('card message length error');
                }
            }
        }
    }

    /**
     * 메시지 글자 수 카운팅 (이모지 포함)
     *
     * @param string $message
     * @return int
     */
    protected function countCardMessageLength(string $message): int
    {
        if (function_exists('grapheme_strlen')) {
            $graphLength = grapheme_strlen($message);

            if ($graphLength !== false) {
                return $graphLength;
            }
        }

        return mb_strlen($message, 'UTF-8');
    }

    /**
     * 파일 업로드
     *
     * @param $fileData array 파일1개
     * @return string
     * @throws \Exception
     */
    protected function cardImageUpload(array $fileData): string
    {
        $result = $this->imageUploadService->uploadImage($fileData, '/presentCard', false, self::MAX_FILE_SIZE);
        $cdnUrl = $this->imageUploadService->getCdnUrl($result['filePath'], $this->imageUploadService->getObsSaveFileNm($result['saveFileNm']));

        if ($result['result'] === false) {
            $this->logger->warning(__METHOD__ . ' uploadImage Fail');
            throw new \Exception('업로드에 실패하였습니다.');
        }
        return $cdnUrl;
    }

    /**
     * 묶여있는 파일 배열 각 파일로 변환
     *
     * @param array $imageFiles
     * @return array
     */
    protected function convertFileArray(array $imageFiles): array
    {
        $files = [];

        foreach ($imageFiles as $key => $valueArray) {
            foreach ($valueArray as $index => $value) {
                $files[$index][$key] = $value;
            }
        }

        return $files;
    }

    /**
     * 선물하기 카드 업데이트
     *
     * @param array $requestCards
     * @return void
     */
    protected function updateCard(array $requestCards): void
    {
        // 존재했던 카드 확인
        $updateCards = $requestCards['update'];
        $existingCardsData = $this->presentCardRepository->findBySnos(array_keys($updateCards));

        $existingCardsBySno = [];
        foreach ($existingCardsData as $card) {
            $existingCardsBySno[$card['sno']] = $card;
        }

        // 변경사항 확인 후 업데이트
        foreach ($updateCards as $sno => $card) {
            if (!isset($existingCardsBySno[$sno])) {
                continue;
            }

            $existingCard = PresentCardDTO::fromArray($existingCardsBySno[$sno]);
            $newCardData = PresentUpdateCardDTO::fromArray($card);

            $shouldUpdate = false;
            if ($existingCard->getMessage() !== $newCardData->getMessage()) {
                $shouldUpdate = true;
            }
            if ($existingCard->getDisplayFl() !== $newCardData->getDisplayFl()) {
                $shouldUpdate = true;
            }

            if ($shouldUpdate) {
                $newCardData->setModDt(Carbon::now());
                $this->presentCardRepository->update($newCardData->toArray());
            }
        }
    }

    /**
     * 카드 삭제
     *
     * @param array $cards
     * @return void
     */
    protected function deleteCard(array $cards): void
    {
        $requestDeleteCardSnos = explode(',', $cards['delete']);
        $cardSnos = $this->presentCardRepository->findNotDefaultCardSnos();

        $deleteCardSnos = array_intersect($requestDeleteCardSnos, $cardSnos);

        $this->presentCardRepository->softDelete($deleteCardSnos);
    }

    /**
     * 삭제되지 않은 카드 조회
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->presentCardRepository->findNotDeleted();
    }

    /**
     * 작성 가능한 카드 조회
     *
     * @return array
     */
    public function getAvailablePresentCards(): array
    {
        return $this->presentCardRepository->findAvailableCardsForPresentWrite();
    }

    /**
     * 이미지 사이즈 및 가로/세로 비율(2:1) 확인
     *
     * @param string $imageFileName
     * @return void
     * @throws PresentCardValidationException
     */
    public function checkImageRatio(string $imageFileName): void
    {
        try {
            $imageRatio = ImageUtils::getImageSizeWithRatio($imageFileName);

            // 최소 사이즈 확인
            if ($imageRatio->getWidth() < self::MIN_WIDTH || $imageRatio->getHeight() < self::MIN_HEIGHT) {
                throw new PresentCardValidationException('image size error: width - ' . $imageRatio->getWidth() . ' | height - ' . $imageRatio->getHeight());
            }

            // 비율 확인
            if (abs($imageRatio->getRatio() - self::ASPECT_RATIO) > 0.1) {
                throw new PresentCardValidationException("image ratio error: ratio - {$imageRatio->getRatio()}");
            }
        } catch (\Exception $e) {
            $this->logger->warning(__METHOD__, ['error' => $e->getMessage()]);
            throw new PresentCardValidationException('image file or image ratio error.');
        }
    }

    /**
     * 이미지 mime 확인
     *
     * @param string $imageFileName
     * @return void
     * @throws PresentCardValidationException
     */
    public function checkImageMime(string $imageFileName): void
    {
        try {
            // 확장자 확인
            $allowMime = ['image/jpeg', 'image/png'];
            FileValidator::validMimeType($imageFileName, $allowMime);
        } catch (InvalidArgumentException $e) {
            $this->logger->warning(__METHOD__, ['error' => $e->getMessage()]);
            throw new PresentCardValidationException('Not allow mime');
        }
    }

    /**
     * 이미지 용량 확인
     *
     * @param int $byteSize
     * @return void
     * @throws PresentCardValidationException
     */
    public function checkImageFileBytes(int $byteSize): void
    {
        if ($byteSize > self::MAX_FILE_SIZE) {
            throw new PresentCardValidationException("image bytes over: {$byteSize}");
        }
    }
}
