<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeReport;
use App\Models\WorkUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class PrintController extends Controller
{
    ## Show Data
    public function index()
    {
        $title = "Laporan";
        if(Auth::user()->group->name == 'Admin Unit'){
            $work_unit = WorkUnit::where('id',Auth::user()->work_unit_id)->get();
        } else {
            $work_unit = WorkUnit::get();
        }
        return view('admin.print.index', compact('title', 'work_unit'));
    }
    
    public function print(Request $request)
    {
        if ($request->category_report == 1) {
            return $this->print_recap_report($request);
        }else if ($request->category_report == 2) {
            return $this->print_kpi_final($request);
        } 
    }
    
    public function print_recap_report(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        if ($request->work_unit_id) {
            $work_unit = WorkUnit::where('id', $request->work_unit_id)->first();
            $employee = Employee::where('work_unit_id', $request->work_unit_id)->orderBy('name', 'ASC')->get();
            
        } else {
            $work_unit = NULL;
            $employee = Employee::orderBy('name', 'ASC')->get();
        }

        // Atur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(40);  // Nama
        $sheet->getColumnDimension('C')->setWidth(15);  // Nilai Rapor

        if ($work_unit) {
            $sheet->mergeCells('A1:C1');
            $sheet->mergeCells('A2:C2');
            $sheet->mergeCells('A3:C3');
            $sheet->setCellValue('A1', 'REKAP RAPOR YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('A2', strtoupper($work_unit->name));
            $sheet->setCellValue('A3', 'BULAN '.$request->month.' TAHUN '.$request->year);
            $sheet->getStyle('A1:C3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:C3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:C3')->getFont()->setBold(true);
            $sheet->getStyle('A1:C3')->getAlignment()->setWrapText(true);
        } else {
            $sheet->mergeCells('A1:C1');
            $sheet->mergeCells('A2:C2');
            $sheet->setCellValue('A1', 'REKAP RAPOR YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('A2', 'BULAN '.$request->month.' TAHUN '.$request->year);
            $sheet->getStyle('A1:C2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:C2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:C2')->getFont()->setBold(true);
            $sheet->getStyle('A1:C2')->getAlignment()->setWrapText(true);
        }

        // Header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Nama');
        $sheet->setCellValue('C5', 'Nilai Rapor');

        $sheet->getStyle('A5:H5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:H5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('A5:H5')->getAlignment()->setWrapText(true);

        $rows = 6;
        $no = 1;

        foreach ($employee as $v) {

            $employee_report = EmployeeReport::
                    whereHas('employee_report_period', function ($query) use ($v, $request) {
                        $query->where('employee_id', $v->id)
                        ->whereMonth('date', $request->month)
                        ->whereyear('date', $request->year);
                    })->sum('value');
            
            $sheet->setCellValue('A' . $rows, $no++);
            $sheet->setCellValue('B' . $rows, $v->name);
            $sheet->setCellValue('C' . $rows, $employee_report);
            $rows++;
        }

        // Border dan alignment
        $sheet->getStyle('A5:C' . ($rows - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A5:A' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C5:C' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $type = 'xlsx';
        if ($work_unit) {
            $fileName = "REKAP RAPOR YAYASAN PENDIDIKAN ALQALAM KENDARI (" . strtoupper($work_unit->name) . ") BULAN ".$request->month." TAHUN ".$request->year.".". $type;
        } else {
            $fileName = "REKAP RAPOR YAYASAN PENDIDIKAN ALQALAM KENDARI BULAN ".$request->month." TAHUN ".$request->year." .". $type;
        }

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } else if ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save("public/upload/report/" . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/') . "/public/upload/report/" . $fileName);
    }

    public function print_kpi_final(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        if ($request->work_unit_id) {
            $work_unit = WorkUnit::where('id', $request->work_unit_id)->first();
            $employee = Employee::where('work_unit_id', $request->work_unit_id)->orderBy('name', 'ASC')->get();
            
        } else {
            $work_unit = NULL;
            $employee = Employee::orderBy('name', 'ASC')->get();
        }

        // Atur lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);   // No
        $sheet->getColumnDimension('B')->setWidth(40);  // Nama
        $sheet->getColumnDimension('C')->setWidth(8);  // Nilai Rapor
        $sheet->getColumnDimension('D')->setWidth(8);  // KPI

        $sheet->getColumnDimension('E')->setWidth(8);  // Jan
        $sheet->getColumnDimension('F')->setWidth(8);  // Feb
        $sheet->getColumnDimension('G')->setWidth(8);  // Mar
        $sheet->getColumnDimension('H')->setWidth(8);  // Apr
        $sheet->getColumnDimension('I')->setWidth(8);  // Mei
        $sheet->getColumnDimension('J')->setWidth(8);  // Jun
        $sheet->getColumnDimension('K')->setWidth(8);  // Jul
        $sheet->getColumnDimension('L')->setWidth(8);  // Agu
        $sheet->getColumnDimension('M')->setWidth(8);  // Sep
        $sheet->getColumnDimension('N')->setWidth(8);  // Okt
        $sheet->getColumnDimension('O')->setWidth(8);  // Nov
        $sheet->getColumnDimension('P')->setWidth(8);  // Des

        $sheet->getColumnDimension('Q')->setWidth(15);
        $sheet->getColumnDimension('R')->setWidth(15);
        $sheet->getColumnDimension('S')->setWidth(15);
        $sheet->getColumnDimension('T')->setWidth(15);
        $sheet->getColumnDimension('U')->setWidth(15);

        if ($work_unit) {
            $sheet->mergeCells('A1:U1');
            $sheet->mergeCells('A2:U2');
            $sheet->mergeCells('A3:U3');
            $sheet->setCellValue('A1', 'REKAP BULANAN PENILAIAN KPI YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('A2', strtoupper($work_unit->name));
            $sheet->setCellValue('A3', 'TAHUN '.$request->year);
            $sheet->getStyle('A1:U3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:U3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:U3')->getFont()->setBold(true);
            $sheet->getStyle('A1:U3')->getAlignment()->setWrapText(true);
        } else {
            $sheet->mergeCells('A1:U1');
            $sheet->mergeCells('A2:U2');
            $sheet->setCellValue('A1', 'REKAP BULANAN PENILAIAN KPI YAYASAN PENDIDIKAN ALQALAM KENDARI');
            $sheet->setCellValue('A2', 'TAHUN '.$request->year);
            $sheet->getStyle('A1:U2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:U2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1:U2')->getFont()->setBold(true);
            $sheet->getStyle('A1:U2')->getAlignment()->setWrapText(true);
        }

        // Header tabel
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Nama');
        $sheet->setCellValue('C5', 'Rapor');
        $sheet->setCellValue('D5', 'KPI');
        $sheet->setCellValue('E5', 'Jan');
        $sheet->setCellValue('F5', 'Feb');
        $sheet->setCellValue('G5', 'Mar');
        $sheet->setCellValue('H5', 'Apr');
        $sheet->setCellValue('I5', 'Mei');
        $sheet->setCellValue('J5', 'Jun');
        $sheet->setCellValue('K5', 'Jul');
        $sheet->setCellValue('L5', 'Agu');
        $sheet->setCellValue('M5', 'Sep');
        $sheet->setCellValue('N5', 'Okt');
        $sheet->setCellValue('O5', 'Nov');
        $sheet->setCellValue('P5', 'Des');
        $sheet->setCellValue('Q5', 'Rata-rata Tahunan');
        $sheet->setCellValue('R5', 'Predikat');
        $sheet->setCellValue('S5', 'Tukin');
        $sheet->setCellValue('T5', 'Besaran Tukin');
        $sheet->setCellValue('U5', 'Tukin Diterima');

        $sheet->getStyle('A5:U5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:U5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A5:U5')->getFont()->setBold(true);
        $sheet->getStyle('A5:U5')->getAlignment()->setWrapText(true);

        $rows = 6;
        $no = 1;

        foreach ($employee as $v) {

            $employee_report = EmployeeReport::
                    whereHas('employee_report_period', function ($query) use ($v, $request) {
                        $query->where('employee_id', $v->id)
                        ->whereMonth('date', $request->month)
                        ->whereyear('date', $request->year);
                    })->sum('value');
            
            $employee_kpi = EmployeeReport::
                    whereHas('employee_report_period', function ($query) use ($v, $request) {
                        $query->where('employee_id', $v->id)
                        ->whereMonth('date', $request->month)
                        ->whereyear('date', $request->year);
                    })->sum('value');
            
            $sheet->setCellValue('A' . $rows, $no++);
            $sheet->setCellValue('B' . $rows, $v->name);
            $sheet->setCellValue('C' . $rows, $employee_report);
            $sheet->setCellValue('Q' . $rows, '=AVERAGE(E'.$rows.':P'.$rows.')');
            $sheet->setCellValue('R' . $rows, '=IF(Q' . $rows . '>=90,"Sangat Baik",IF(Q' . $rows . '>=80,"Baik",IF(Q' . $rows . '>=70,"Cukup",IF(Q' . $rows . '>=60,"Kurang","Perlu Pembinaan"))))');
            $sheet->setCellValue('S' . $rows, '=IF(Q' . $rows . '>=90,100%,IF(Q' . $rows . '>=80,90%,IF(Q' . $rows . '>=70,80%,IF(Q' . $rows . '>=60,70%,IF(Q' . $rows . '>=50,60%,IF(Q' . $rows . '>=40,50%,IF(Q' . $rows . '>=30,40%,30%)))))))');
            $sheet->setCellValue('T' . $rows, '200000');
            $sheet->setCellValue('U' . $rows,'=T' . $rows . '*S' . $rows);
            $sheet->getStyle('T' . $rows . ':U' . $rows)->getNumberFormat()->setFormatCode('#,##0');
            $rows++;
        }

        // Border dan alignment
        $sheet->getStyle('A5:U' . ($rows - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->getStyle('A5:A' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('U5:U' . ($rows - 1))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $type = 'xlsx';
        if ($work_unit) {
            $fileName = "REKAP RAPOR YAYASAN PENDIDIKAN ALQALAM KENDARI (" . strtoupper($work_unit->name) . ") TAHUN ".$request->year.".". $type;
        } else {
            $fileName = "REKAP RAPOR YAYASAN PENDIDIKAN ALQALAM KENDARI TAHUN ".$request->year." .". $type;
        }

        if ($type == 'xlsx') {
            $writer = new Xlsx($spreadsheet);
        } else if ($type == 'xls') {
            $writer = new Xls($spreadsheet);
        }
        $writer->save("public/upload/report/" . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        return redirect(url('/') . "/public/upload/report/" . $fileName);
    }

}
