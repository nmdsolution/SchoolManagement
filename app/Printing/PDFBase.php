<?php

namespace App\Printing;

use App\Models\Center;
use Illuminate\Support\Facades\Storage;

class PDFBase extends HtmlPdf 
{
    public static function getInstance(int $center_id, $orientation='P', $filename="YADIKO FILE", $add_header=true,
                                       $size = 'A4', $add_page = true){
        $class = get_called_class();
        $pdf = new $class($orientation, 'cm', $size, true);
        $pdf->AliasNbPages();

        $pdf->SetAuthor('YADIKO');
        $pdf->SetTitle($filename);

        if ($add_page) {
            $pdf->AddPage();
        }

        if($add_header){
            // $pdf->addCenterHeader($center_id);
        }

        return $pdf;
    }

    private function addCenterHeader(int $center_id): void{
        $center = Center::find($center_id);

        $this->writeHtmlCenterHeader();
    }

    private function writeHtmlCenterHeader(): void
    {
        $settings = getSettings([
            'report_left_header',
            'report_right_header',
            'report_header_logo'
        ], null, getCurrentMedium()->id);

        $line_width = ($this->GetPageWidth()-2)/2;
        $start_x = $this->GetPageWidth()/2;

        $start_y = $this->GetY();

        if (isset($settings['report_left_header']) && isset($settings['report_right_header'])) {
            $this->SetFont('Arial', '', 6);
            $this->WriteHTMLLines($settings['report_left_header'], 'C', $line_width, 0);
            $first_y = $this->GetY();

            $this->SetY($start_y);
            $this->SetFont('Arial', '');
            $this->WriteHTMLLines($settings['report_right_header'], 'C', $line_width, $start_x+1);
            $last_y = $this->GetY();

            $image_width =3;
            $image_height = 3;
            $image_x = $start_x - ($image_width/2);
            $image_y = ($start_y + max($first_y, $last_y))/2 - ($image_height/2);

            $logo = $this->logoPublicPath();

            if ($logo) {
                $this->Image($logo, $image_x, $image_y, $image_width, $image_height);
            }

            $this->SetY(max($first_y, $last_y, $image_height+$start_y));
            $this->Ln(0.5);
        }
    }

    private function logoPublicPath() {
        $logo = getReportHeaderLogo()?->message;

        if ($logo) {
            $parsedUrl = parse_url($logo);
            $relativePath = ltrim($parsedUrl['path'], '/');
            return public_path($relativePath);
        } else {
            return null;
        }
    }

    public function Footer(): void
    {
        $this->SetY(-2);

        $this->SetFont('Arial', 'I', 6);
        $this->SetFillColor(0,0,0);
        $this->SetTextColor(0,0,0);
        
        $this->Cell(0, 0.07, '', 0, 1,'L', true);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell($this->GetPageWidth()/2, 1, trans('date').': '.date('d-m-Y'), 0, 0,'L');
        $this->Cell(0, 1, 'Page '.$this->PageNo().'/{nb}', 0, 0, 'R');
    }

    public function filterDatabaseEmptyCharacter($string) {
//        return str_replace('�', '', $string);
        return $string;
    }

    public function to_iso_8859_1($text) {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);;
    }

    public function short_gender($gender) {
        $gender = strtolower($gender);
        if ($gender == 'male') {
            return 'M';
        } elseif ($gender == 'female') {
            return 'F';
        } else {
            return '';
        }
    }

    public function short_yes_no($value, $only_yes = false) {
        $value = is_numeric($value) ? $value : strtolower($value);
        if ($value == 1 || $value == 'yes' || $value == 'true') {
            return trans('Y');
        } else if(!$only_yes) {
            return trans('N');
        }
    }

    public function truncatedCell($pdf, $width, $height, $text, $border = 0, $ln = 0, $align = '', $fill = false) {
        $maxWidth = $width - 2; // Réduire légèrement pour éviter que le texte touche le bord
        $ellipsis = '...';
        
        // Vérifier si le texte tient dans la cellule
        while ($pdf->GetStringWidth($text) > $maxWidth) {
            // Supprimer un caractère à la fois et ajouter les points de suspension
            $text = mb_substr($text, 0, -1);
            
            if ($pdf->GetStringWidth($text . $ellipsis) <= $maxWidth) {
                $text .= $ellipsis;
                break;
            }
        }
        
        // Insérer le texte tronqué dans la cellule
        $pdf->Cell($width, $height, $text, $border, $ln, $align, $fill);
    }
    
}
