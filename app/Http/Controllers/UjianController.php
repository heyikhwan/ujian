<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Soal;
use App\Models\kelas;
use App\Models\jawaban;
use App\Models\pelajaran;
use App\Models\users_kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class UjianController extends Controller
{
    public function index()
    {
        $pelajarans = Pelajaran::with('user')
        ->whereHas('user', function ($query) {
            $query->where('role', 1);
        })
        ->get();

        $kelas = users_kelas::with('user', 'kelas')->get();

        return view('guru.crud_soal.index', compact('pelajarans', 'kelas'));
    }

    public function create()
    {
        $pelajarans = Pelajaran::with('user')
        ->whereHas('user', function ($query) {
            $query->where('role', 1);
        })
        ->get();

        $kelas = users_kelas::with('user', 'kelas')->get();
        return view('guru.crud_soal.create', compact('pelajarans', 'kelas'));
    }

    public function upload(Request $request)
    {
        DB::beginTransaction();
        try {
            $question = Soal::create([
                'user_id' => auth()->id(),
                'kelas_id' => $request->input('kelas_id'),
                'pelajaran_id' => $request->input('pelajaran_id'),
                'isi_soal' => $request->input('data.isi_soal')[0]
            ]);
    
            $jawabanArray = $request->input('data.isi_jawaban');
            $totalJawaban = count($jawabanArray);
            
            $is_correct_option = $request->input('data.isi_jawaban_correct');
            
            for ($key = 0; $key < $totalJawaban; $key++) {
                $jawabanData = $jawabanArray[$key];
                $is_correct = ($is_correct_option == $key) ? 1 : 0;
            
                Jawaban::create([
                    'soal_id' => $question->id,
                    'isi_jawaban' => $jawabanData,
                    'is_correct' => $is_correct,
                ]);
            }

            DB::commit();
    
            return response()->json(['message' => 'Data imported successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response('Error: ' . $e->getMessage(), 500);
        }
    }


    public function uploadExcal(Request $request)
    {
        DB::beginTransaction();

        try {
            $uploadedFile = $request->file('xlsx_file');
    
            $reader = new Xlsx();
            $spreadsheet = $reader->load($uploadedFile);
            $sheets = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            array_shift($sheets);
            $idxJawaban = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10]; // tambahkan jika pilihan ganda diatas i dengan rumus prefix => i
            foreach($sheets as $sheet){
                $isCorrect = $idxJawaban[trim(strtolower($sheet[count($sheet) - 1]))];
                $question = Soal::create([
                    'user_id' => auth()->id(),
                    'kelas_id' => $request->input('kelas_id'),
                    'pelajaran_id' => $request->input('pelajaran_id'),
                    'isi_soal' => trim($sheet[0]),
                ]);
                foreach($sheet as $key => $jawaban){
                    if($key === 0 || $key === (count($sheet) - 1)){
                        continue;
                    }
                    if($isCorrect === $key){
                        Jawaban::create([
                            'soal_id' => $question->id,
                            'isi_jawaban' => $jawaban, 
                            'is_correct' => 1,
                        ]);
                    }else{
                        Jawaban::create([
                            'soal_id' => $question->id,
                            'isi_jawaban' => $jawaban, 
                            'is_correct' => 0,
                        ]);
                    }
                }
            }
    
    
            DB::commit();
    
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response('Error: ' . $e->getMessage(), 500);
        }
    }
}
