<?php

namespace app\controllers;

use app\models\Voucher;

class VoucherController extends Controller
{
    public function POST_generateAction()
    {
        $data = $this->request->post->getParams();
        $voucher = new Voucher();
        $data = $voucher->generate($data);
        return ['data' => $data];
    }

    public function POST_applyAction()
    {
        $voucher = new Voucher();
        $data = $voucher->apply($this->request->post->getParams());
        return ['data' => $data];
    }
}