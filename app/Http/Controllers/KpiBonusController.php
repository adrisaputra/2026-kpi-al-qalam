<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\KpiBonus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\DataTables;

class KpiBonusController extends Controller
{
    public function index($kpi)
    {
        $title = "Bonus KPI";
        $kpi = Crypt::decrypt($kpi);
        $kpi = Kpi::where('id', $kpi)->first();
        return view('admin.kpi_bonus.index', compact('title', 'kpi'));
    }


    public function get_kpi_bonus_index(Request $request, $kpi)
    {
        if ($request->ajax()) {
            $counters = 1;

            $kpi = Crypt::decrypt($kpi);
            $kpi = Kpi::where('id', $kpi)->first();

            $kpi_bonus = KpiBonus::where('kpi_id', $kpi->id)->where('id','!=',0)->limit(10);

            return DataTables::of($kpi_bonus)
            ->addIndexColumn()
            ->addColumn('number', function () use (&$counters) {
                return $counters++;
            })
            ->addColumn('is_employee', function ($v) {
                if($v->is_employee==true){
                    $status ='<span class="badge badge-danger">Diinput Pegawai</span>';
                }else{
                    $status ='<span class="badge badge-success">Diinput Admin Unit</span>';
                }
                return $status;
            })
            ->addColumn('action', function ($v) {
                $btn = '<a href="#" onClick="getData('.$v->id.')" id="'.$v->id.'" title="Edit" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 text-success"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                        </a>';
                $btn .= '<a href="#" onclick="deleteData('.$v->id.')" id="'.$v->id.'" class="warning confirm" data-toggle="tooltip" data-placement="top" title="Hapus">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </a>';
                return $btn;
            })
            ->rawColumns(['is_employee', 'action'])
            ->make(true);
        }
    }

    public function validate(Request $request, $action)
    {
        if ($request->ajax()) {

            $attributes = [
                'category' => 'Kategori',
                'indicator' => 'Indikator',
                'is_employee' => 'Jenis Inputan'
            ];

            if ($action === "Simpan") {
                $rules = [
                    'category' => 'required|max:255',
                    'indicator' => 'required|max:255',
                    'is_employee' => 'required|max:255'
                ];
            } else {
                $rules = [
                    'category' => 'required|max:255',
                    'indicator' => 'required|max:255',
                    'is_employee' => 'required|max:255'
                ];
            }

            $request->validate($rules, [], $attributes);

            return response()->json(['success' => true]);
        }
    }

    ## Save Indikator KPI 
    public function store(Request $request)
    {
        if ($request->ajax()) {

            $kpi_bonus = new KpiBonus();
            $kpi_bonus->kpi_id = $request->kpi_id;
            $kpi_bonus->category = $request->category;
            $kpi_bonus->indicator = $request->indicator;
            $kpi_bonus->target = $request->target;
            $kpi_bonus->weight = $request->weight;
            $kpi_bonus->is_employee = $request->is_employee;
            // $kpi_bonus->is_employee = $request->has('is_employee') ? 1 : 0;
            $kpi_bonus->save();
            activity()->log('Create Indikator KPI Data');
            return response()->json(['success' => true, 'message' => 'Tambah Indikator KPI Berhasil']);
        }
    }

    ## Get Indikator KPI
    public function edit(Request $request, KpiBonus $kpi_bonus)
    {
        if ($request->ajax()) {
            return response()->json(['success' => true, 'data' => $kpi_bonus]);
        }
    }

    ## Edit Indikator KPI
    public function update(Request $request, KpiBonus $kpi_bonus)
    {
        if ($request->ajax()) {

            $kpi_bonus->kpi_id = $request->kpi_id;
            $kpi_bonus->category = $request->category;
            $kpi_bonus->indicator = $request->indicator;
            $kpi_bonus->target = $request->target;
            $kpi_bonus->weight = $request->weight;
            $kpi_bonus->is_employee = $request->is_employee;
            // $kpi_bonus->is_employee = $request->has('is_employee') ? 1 : 0;
            $kpi_bonus->save();

            activity()->log('Edit Indikator KPI Data With ID = ' . $kpi_bonus->id);
            return response()->json(['success' => true, 'message' => 'Ubah Indikator KPI Berhasil']);
        }
    }

    ## Delete Indikator KPI
    public function delete(Request $request, KpiBonus $kpi_bonus)
    {
        if ($request->ajax()) {
            $kpi_bonus->delete();
            activity()->log('Delete Indikator KPI Data With ID = ' . $kpi_bonus->id);
            return response()->json(['success' => true, 'message' => 'Hapus Indikator KPI Berhasil']);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        $reader = new Xlsx();

        $spreadsheet = $reader->load(
            $request->file('file')->getRealPath()
        );

        $data = $spreadsheet->getSheet(0)->toArray();

        $kpiIndicatorImported = 0;
        $kpiIndicatorItemImported = 0;

        DB::transaction(function () use (
            $data,
            $request,
            &$kpiIndicatorImported,
            &$kpiIndicatorItemImported
        ) {

            $currentKpiBonus = null;

            foreach ($data as $index => $row) {

                // Skip header
                if ($index == 0) {
                    continue;
                }

                /*
                * Kolom Excel:
                * 0 = No
                * 1 = Indikator KPI
                * 2 = Target
                * 3 = Bobot
                * 4 = Di Input pegawai
                * 5 = Alat Ukur
                * 6 = Bukti Fisik
                */

                $indicator = trim($row[1] ?? '');
                $target = $row[2] ?? null;
                $weight = $row[3] ?? null;
                $isEmployee = $row[4] ?? null;

                $measurementTool = trim($row[5] ?? '');
                $physicalEvidence = trim($row[6] ?? '');

                /*
                * Jika kolom Indikator KPI terisi,
                * berarti ini indikator baru.
                */
                if ($indicator !== '') {

                    $currentKpiBonus = KpiBonus::create([
                        'kpi_id'      => $request->kpi_id,
                        'indicator'   => $indicator,
                        'target'      => $target,
                        'weight'      => $weight,
                        'is_employee' => $isEmployee,
                    ]);

                    $kpiIndicatorImported++;
                }

                /*
                * Jika ada Alat Ukur / Bukti Fisik,
                * masukkan ke kpi_bonus_items
                * menggunakan ID indikator yang sedang aktif.
                */
                if (
                    $currentKpiBonus &&
                    ($measurementTool !== '' || $physicalEvidence !== '')
                ) {

                    KpiBonusItem::create([
                        'kpi_bonus_id' => $currentKpiBonus->id,
                        'measurement_tool' => $measurementTool !== ''
                            ? $measurementTool
                            : null,
                        'physical_evidence' => $physicalEvidence !== ''
                            ? $physicalEvidence
                            : null,
                    ]);

                    $kpiIndicatorItemImported++;
                }
            }
        });

        return back()->with(
            'success',
            "KPI indikator: {$kpiIndicatorImported} data dan KPI indikator item: {$kpiIndicatorItemImported} data berhasil diimport."
        );
    }

}
