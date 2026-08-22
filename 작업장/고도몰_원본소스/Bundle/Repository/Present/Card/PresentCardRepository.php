<?php
/*
 * Copyright (C) 2025 NHN COMMERCE. - All Rights Reserved
 *
 * Unauthorized copying or redistribution of this file in source and binary forms via any medium is strictly prohibited.
 */

namespace Bundle\Repository\Present\Card;

use Carbon\Carbon;
use Origin\Model\Present\Card\PresentCard;

class PresentCardRepository
{
    /**
     * 모든 카드 조회
     *
     * SELECT * FROM es_presentCard
     *
     * @return array
     */
    public function findAll(): array
    {
        return PresentCard::all()
            ->toArray();
    }

    /**
     * 카드 데이터 삽입
     *
     * @param array $insertDatas
     * @return void
     */
    public function insert(array $insertDatas): void
    {
        PresentCard::query()->insert($insertDatas);
    }

    /**
     * 카드 번호를 통한 데이터 조회
     *
     * SELECT * FROM es_presentCard WHERE sno IN (?, ?, ?)
     *
     * @param array $snos
     * @return array
     */
    public function findBySnos(array $snos): array
    {
        return PresentCard::query()->whereIn('sno', $snos)
            ->get()
            ->toArray();
    }

    /**
     * 삭제 안한 카드 데이터 조회
     *
     * SELECT * FROM es_presentCard WHERE deleteFl = 'n' ORDER BY uploadType = 'upload' DESC, sno DESC
     *
     * @return array
     */
    public function findNotDeleted(): array
    {
        return PresentCard::query()
            ->where('deleteFl', 'n')
            ->orderByRaw("uploadType = 'upload' DESC, sno DESC")
            ->get()
            ->toArray();
    }

    /**
     * 기본 이미지 외 카드 조회
     * 
     * SELECT sno FROM es_presentCard WHERE uploadType != 'default' AND deleteFl = 'n'
     *
     * @return array sno 배열
     */
    public function findNotDefaultCardSnos(): array
    {
        return PresentCard::query()
            ->whereNot('uploadType', 'default')
            ->where('deleteFl', 'n')
            ->pluck('sno')
            ->toArray();
    }

    /**
     * 선물하기 작성 시 사용 가능한 카드 조회
     *
     * SELECT *
     * FROM es_presentCard
     * WHERE deleteFl = 'n' AND displayFl = 'y'
     * ORDER BY uploadType = 'upload' DESC, sno DESC
     *
     * @return array
     */
    public function findAvailableCardsForPresentWrite(): array
    {
        return PresentCard::query()
            ->where('deleteFl', 'n')
            ->where('displayFl', 'y')
            ->orderByRaw("uploadType = 'upload' DESC, sno DESC")
            ->get()
            ->toArray();
    }

    /**
     * 카드 데이터 업데이트
     *
     * @param array $card
     * @return int 업데이트 된 레코드 수
     */
    public function update(array $card): int
    {
        return PresentCard::query()
            ->where('sno', $card['sno'])
            ->update($card);
    }

    /**
     * 삭제한 카드 삭제 플래그, 날짜 업데이트
     *
     * UPDATE es_presentCard SET deleteFl = 'y', deleteDt = now() WHERE sno = ?
     *
     * @param array $cardSnos
     * @return int 업데이트 된 레코드 수
     */
    public function softDelete(array $cardSnos): int
    {
        return PresentCard::query()
            ->whereIn('sno', $cardSnos)
            ->update([
                'deleteFl' => 'y',
                'deleteDt' => Carbon::now(),
            ]);
    }
}
