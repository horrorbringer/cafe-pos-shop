<?php

namespace App\Services;

use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeService
{
    public function generateMenuUrl(?int $tableId = null): string
    {
        $baseUrl = config('app.url', url('/'));

        if ($tableId) {
            return "{$baseUrl}/menu?table={$tableId}";
        }

        return "{$baseUrl}/menu";
    }

    public function generateQrCode(string $data, int $size = 300): string
    {
        $options = new QROptions([
            'outputType' => QROutputInterface::GDIMAGE_PNG,
            'eccLevel' => QRCode::ECC_M,
            'scale' => max(1, (int) ($size / 40)),
            'imageBase64' => false,
            'bgColor' => [255, 255, 255],
            'drawLightModules' => false,
            'moduleValues' => [
                QRMatrix::M_DATA_DARK => [40, 40, 40],
            ],
        ]);

        $imageData = (new QRCode($options))->render($data);

        return 'data:image/png;base64,'.base64_encode($imageData);
    }

    public function generateMenuQrCode(?int $tableId = null, int $size = 300): string
    {
        $url = $this->generateMenuUrl($tableId);

        return $this->generateQrCode($url, $size);
    }
}
