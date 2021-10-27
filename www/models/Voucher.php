<?php

namespace app\models;

class Voucher extends Model
{
    public string $tableName = 'voucher';

    public function apply($data)
    {
        $voucher = $this->findOne(['code' => $data['code']]);
        $totalPrice = 0;
        foreach ($data['items'] as $item) {
            $totalPrice += $item['price'];
        }

        $leftDiscount = $voucher['value'];
        $leftNeededCount = count($data['items']);
        foreach ($data['items'] as &$item) {
            // Calculate discount
            $percent = $item['price'] / $totalPrice;
            $item['discount'] = round($voucher['value'] * $percent);

            /**
             * Specific Conditions
             */
            if ($item['discount'] > $item['price']){
                $item['discount'] = $item['price'];
            }
            if ($leftDiscount < $item['discount']) {
                $item['discount'] = $leftDiscount;
            }
            if (!$leftDiscount) {
                $item['discount'] = 0;
            }
            if ($leftNeededCount) {
                $leftDiscount -= $item['discount'];
            }

            $item['price_with_discount'] = $item['price'] - $item['discount'];

            $leftNeededCount--;
        }
        //Left Discount put to first item
        if ($leftDiscount && $data['items'][0]['price'] >= $leftDiscount) {
            $data['items'][0]['price_with_discount'] -= $leftDiscount;
        }

        return $data;
    }

    public function generate($data)
    {
        $code = $this->_generateRandomString(6);
        $value = $data['discount'];
        $data = ['code' => $code, 'value' => $value];
        $sql = "INSERT INTO voucher (code, value) VALUE (:code, :value)";
        $dbConnection = $this->db->pdo->prepare($sql);
        $dbConnection->execute($data);

        return $data;
    }

    private function _generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}