<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Pdf as ModelsPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    // PDF দেখানো (Browser এ View)
    public function show()
    {
        $customers = Customer::all();

        $data = [
            'title' => 'All customers show',
            'customers' => $customers
        ];

        $pdf = Pdf::loadView('pdf.template', $data);
        return $pdf->stream('example.pdf');
    }

    // // PDF ডাউনলোড
    // public function download()
    // {
    //     $data = [ 'title' => 'Laravel PDF Example' ];
    //     $pdf = Pdf::loadView('pdf.template', $data);
    //     return $pdf->download('example.pdf');
    // }

    // PDF Store করা (Storage এ) এবং Database-এ save
    public function store()
    {
        $customers = Customer::all();

        $data = [
            'title' => 'All customers show',
            'customers' => $customers
        ];

        $pdf = Pdf::loadView('pdf.template', $data);

        // File path
        $fileName = 'pdfs/example_' . time() . '.pdf';

        // Store to storage/app/pdfs/
        Storage::put($fileName, $pdf->output());

        // Save to database (assume model has a 'file_path' column)
        $model = new ModelsPdf();
        $model->file_path = $fileName;
        $model->save();

        return response()->json(['message' => 'PDF saved successfully', 'path' => $fileName]);
    }

    public function viewStored($id)
    {
        $model = ModelsPdf::findOrFail($id);

        if (!Storage::exists($model->file_path)) {
            abort(404, 'File not found!');
        }

        return response()->file(storage_path('app/private/' . $model->file_path));
    }
}