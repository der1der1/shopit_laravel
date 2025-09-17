<?php

namespace App\Repository;

use App\Models\User;

class WishlistRepository
{
    public function getUserWantList($userAccount)
    {
        return User::where('account', $userAccount)->pluck('want')->first();
    }

    public function parseWantIds($wantString)
    {
        // 刪除數字及逗點以外的一切 (正則表達式)
        $cleanedString = preg_replace('/[^\d,]/', '', $wantString);
        // 第一個字元如果是逗號則刪除該字元
        $cleanedString = isset($cleanedString[0]) && $cleanedString[0] === ',' ? substr($cleanedString, 1) : $cleanedString;
        // 把該欄位的字串改成陣列
        $idsArray = explode(",", $cleanedString);
        // 過濾空值和重複值
        return array_filter(array_unique($idsArray));
    }

    public function updateUserWantList($userAccount, $newWantList)
    {
        $user = User::where('account', $userAccount)->first();
        if ($user) {
            $user->want = $newWantList . ',';
            $user->save();
        }
        return $user;
    }

    public function removeItemsFromWantList($currentWantIds, $itemsToRemove)
    {
        // 原先陣列要單一值也要刪除空值
        $currentWantIds = array_filter(array_unique($currentWantIds));
        // 刪除原先陣列與itemIds中相同的元素
        $newWantIds = array_diff($currentWantIds, $itemsToRemove);
        // 將結果轉回字串
        return implode(",", $newWantIds);
    }
}