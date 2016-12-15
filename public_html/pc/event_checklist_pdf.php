<?php
require_once("../../includes/initialize.php");
require_once("../../includes/FPDF/cellfit.php");

/*
 * Generates event information/checklist PDfs - one page for each event selected 
 * in the dashboard.php page. Uses FPDF (see details below) to overlay event 
 * information on a template which is based entirely on Pampered Chef's "Host 
 * Information Checklist".
 * 
 * 
 * PDF Overlays with Text Scaling Using FPDF, FPDI, and cellfit.php
 *
 * FPDF        - for generating PDFs in PHP
 * FPDI        - for overlaying an existing PDF with new content
 * cellfit.php - fit text to FPDF cell
 * (http://www.fpdf.org/en/script/script62.php)
 *  
 * FPDF is used to generate PDFs from dynamic PHP content. Unfortunately, one
 * cannot overlay an existing PDF using FPDF alone. To do this we use FPDI. 
 * There exists a handy extension to FPDF I'll call "cellfit.php" which 
 * automatically adjusts the font size and character spacing to fit text to the 
 * CELLs that one works with in FPDF. Unfortunately, cellfit.php needs to be 
 * modified to work with FPDI. But this modification is simple:
 * 
 *  1. Include FPDI. (Add a "require_once('fpdi.php');" line)
 *  2. Where cellfit.php's "FPDF_CellFit" class extends FDPF by default, change
 *     this to extend FPDI instead.
 */

if (isset($_POST)) {

    // Get Events from ids POSTed from corresponding DashboardElements
    $events = [];
    foreach ($_POST AS $id => $value) {
        if ($value == "on") {
            $events[] = Event::find_by_id($id);
        }
    }

    // Get primary contact info associated with the events ids
    foreach ($events AS $event) {
        $events_contacts[] = ["event" => $event, 
                              "contact" => $event->get_primary_contact()];
    }



    // Setup the Document and PDF to be overlayed
    $pdf = new FPDF_CellFit('P', 'mm', 'Letter');
    $pageCount = $pdf->setSourceFile('../../includes/pdfs/p1.pdf');
    $tplIdx = $pdf->importPage(1, '/MediaBox');

    // Insert overlay content - one page per event
    foreach ($events_contacts as $cur) {

        // Replace "empties"  with a space - cellfit.php divides by string 
        // length this results in division by zero. So, for each field to be 
        // overlayed, empties are replaced with a space (length = 1).
        foreach ($cur AS $obj) {
            foreach ($obj AS $key=>$value) {
                if (empty($value)) {
                    $obj->$key = ' ';
                }
            }
        }

        /* 
         * Prepare some fields for overlay
         */
        
        // fullname
        $name = $cur['contact']->first_name . " " . $cur['contact']->last_name;
        // addresses on one line
        $addr = $cur['contact']->addr1 . (!empty($cur['contact']->addr2) ? " " . 
                $cur['contact']->addr2 : "") . 
                (!empty($cur['contact']->addr3) ? " " . 
                $cur['contact']->addr3 : "");
        // format time
        $time = new DateTime($cur['event']->time);
        // trim "https://" from the url
        $event_url = str_replace('https://', '', $cur['event']->showpage_url);

        /*
         * Construct page with overlays
         */
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, 0, 0, 215.9);
        $pdf->SetFont('Arial', 'B', 45);
        $pdf->Cell(70, 12.9, '', 0, 1);
        $pdf->Cell(8, 5, '', 0);
        $pdf->CellFitScale(87, 15, $name, 0);                       //name
        $pdf->SetFont('Arial', 'B', 19);    //change font size
        $pdf->Cell(118, 5, '', 0, 0);
        $pdf->Cell(25, 6.3, '', 1);
        $pdf->Ln(6.3);
        $pdf->Cell(118, 5, '', 0, 0);
        $pdf->CellFitScale(43, 8, $cur['event']->date, 0);          //date
        $pdf->Cell(9, 8, '', 0);
        $pdf->CellFitScale(26, 8, $time->format('g:ia'), 0);        //time
        $pdf->Cell(120, 8, '', 0);
        $pdf->Ln(7.8);
        $pdf->Cell(13, 8, '', 0);
        $pdf->CellFitScale(82, 8, $addr, 0);                        //addr
        $pdf->Ln(7.8);
        $pdf->Cell(5, 8, '', 0);
        $pdf->CellFitScale(43, 8, $cur['contact']->city, 0);        //city
        $pdf->Cell(10, 8, '', 0);
        $pdf->CellFitScale(14, 8, $cur['contact']->state, 0);       //state
        $pdf->Cell(6, 8, '', 0);
        $pdf->CellFitScale(16, 8, $cur['contact']->zip, 0);         //zip
        $pdf->Ln(7.8);
        $pdf->Cell(17, 8, '', 0);
        $pdf->CellFitScale(32, 8, $cur['contact']->day_phone, 0);   //day_phone
        $pdf->Cell(15, 8, '', 0);
        $pdf->CellFitScale(32, 8, $cur['contact']->evening_phone, 0);//eve_phone
        $pdf->Ln(7.8);
        $pdf->Cell(7, 8, '', 0);
        $pdf->CellFitScale(32, 8, $cur['contact']->cell_phone, 0);  //cell_phone
        $pdf->Ln(7.9);
        $pdf->Cell(9, 8, '', 0);
        $pdf->CellFitScale(86, 8, $cur['contact']->email, 0);       //email
        $pdf->Cell(34, 8, '', 0);
        $pdf->SetFont('Courier', 'B', 19);  //change font
        $pdf->CellFitScale(68, 8, $event_url, 1);                   //event_url
    }
    $pdf->Output();
}
?>
