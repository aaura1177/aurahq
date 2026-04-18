<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PDFController extends Controller
{
    public function download()
    {
        // Sample data to pass into the view
        $data = [
            'title' => 'Sample PDF Document',
            'content' => 'This is a sample PDF content generated using Laravel and DomPDF.'
        ];

        // Load the Blade view and pass the data to it
        $pdf = PDF::loadView('pdf.sample', $data);

        // Download the PDF file
        return $pdf->download('sample.pdf');
    }
}
