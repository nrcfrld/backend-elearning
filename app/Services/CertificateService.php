<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use setasign\Fpdi\Fpdi;
use Storage;

/**
 * Class CertificateCertificateService
 *
 *
 */
class CertificateService
{
    protected $sourceFile;
    protected $fpdi;

    public function __construct()
    {
        define('FPDF_FONTPATH', app_path('/Fonts'));
        $this->sourceFile = public_path('/master/cert.pdf');
        $this->fpdi = new Fpdi();
        $this->fpdi->AddFont('BebasNeue', '', 'BebasNeue-Regular.php');
        $this->fpdi->AddFont('Raleway', '', 'Raleway-Regular.php');
    }


    public function generate(Course $course, User $user, $date): String
    {

        $this->fpdi->setSourceFile($this->sourceFile);
        $template = $this->fpdi->importPage(1);
        $size = $this->fpdi->getTemplateSize($template);
        $this->fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $this->fpdi->useTemplate($template);
        $this->fpdi->SetFont("BebasNeue", '', 24);
        $this->fpdi->Text(($size['width'] / 2) - ($this->fpdi->GetStringWidth($user->name) / 2), 65, $user->name);
        $this->fpdi->SetFont("Raleway", '', 16);
        $this->fpdi->Text(($size['width'] - 161) - ($this->fpdi->GetStringWidth($course->name) / 2), 78, $course->name);
        $this->fpdi->Text(($size['width'] - 96) - ($this->fpdi->GetStringWidth($date) / 2), 78, $date);
        $hash = Uuid::uuid4();
        $filename = "{$user->id}{$user->name}-{$course->name}.pdf";
        Storage::put("/certificate/$filename", $this->fpdi->Output("S", $filename));

        return "/certificate/" . $filename;
    }

    public function verify()
    {
        //
    }
}
