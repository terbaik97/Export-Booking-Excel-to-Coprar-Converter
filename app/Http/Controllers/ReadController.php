<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\ChunkReadFilter;
use App\MyReadFilter;

class ReadController extends Controller
{
    public function index(Request $request)
    {
        // var_dump($request()->input('callsign_code'));
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        // $reader->setReadFilter(new MyReadFilter());
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load(request()->file('my_file_input'));
        $worksheet = $spreadsheet->getActiveSheet()->toArray();
        // dd(array_values($worksheet[0]));
        $dt = gmdate("D d M Y H:i:s", time()) . " GMT";
        // dd($dt);
        $line = 0;
        $refno = $this->get_date_str($dt, "");
        $edi = "UNB+UNOA:2+KMT+" . $request->input('recv_code') . "+" . $this->get_date_str($dt, "daterawonly") . ":" . $this->get_date_str($dt, "timetominrawonly") . "+" . $refno . "'\n";
        $edi .= "UNH+" . $refno . "+COPRAR:D:00B:UN:SMDG21+LOADINGCOPRAR'\n";
        // dd($edi);
        $line++;
        $contcount = 0;
        $report_dt = "";
        $voyage = "";
        $vslname = "";
        $callsign = "";
        $opr = "";
        // dd(count($worksheet));
        for ($singleRow = 0; $singleRow < count($worksheet); $singleRow++) {
            if ($singleRow > 6) break;
            // dd($worksheet[$singleRow]);
            $rowCells = $worksheet[$singleRow];
            // dd($rowCells, $singleRow, $allRows[$singleRow]);
            // explode(" ", $pizza);
            if ($singleRow == 1) {

                $tmpdt = explode('/', $rowCells[1]);
                // dd($tmpdt);
                $day = $tmpdt[0];
                $month = $tmpdt[1];
                $tmpyear = explode(' ', $tmpdt[2]);
                $report_date = date($tmpyear[0] . "-" . $month . "-" . $day . " " . $tmpyear[1]);
                $report_dt = $this->get_date_str($report_date, "");
            }
            if ($singleRow == 3) {
                if (isset($rowCells[3])) {
                    $tmp = explode('/', $rowCells[3]);
                    $voyage = $tmp[0];
                    $callsign = $tmp[1];
                    $opr = $tmp[2];
                    $vslname = $rowCells[1];
                }
            }
        }
        $edi .= "BGM+45+" . $report_dt . "+5'\n";
        $line++;
        $edi .= "TDT+20+" . $voyage . "+1++172:" . $opr . "+++" . $request->input('callsign_code') . ":103::" . $vslname . "'\n";
        $line++;
        $edi .= "RFF+VON:" . $voyage . "'\n";
        $line++;
        $edi .= "NAD+CA+" . $opr . "'\n";

        $tmp;
        for ($singleRow = 0; $singleRow < count($worksheet); $singleRow++) {
            // dd(!isset($allRows[$singleRow]));

            if (isset($worksheet[$singleRow])) {
                // dd($worksheet[$singleRow]);
                //$ $rowCells = allRows[singleRow].split(',');
                $rowCells = $worksheet[$singleRow];
                // dd($rowCells);
                // dd($singleRow);
                if ($singleRow > 7) {
                    $contcount++;
                    //$rowCells[3] //5 - F, 4 - E
                    $fe = "5";
                    if (isset($rowCells[3]) && $rowCells[3] == "E") $fe = "4";
                    //2 TS - N, 6 TS - Y
                    $type = "2";
                    if (isset($rowCells[11]) && $rowCells[11] == "Y") $type = "6";

                    if (isset($rowCells[1]) && isset($rowCells[7])) {
                        $edi .= "EQD+CN+" . $rowCells[1] . "+" . $rowCells[7] . ":102:5++" . $type . "+" . $fe . "'\n";
                        $line++;
                    }
                    if (isset($rowCells[6])) {
                        $edi .= "LOC+11+" . $rowCells[5] . ":139:6'\n";
                        $line++;
                    }
                    if (isset($rowCells[6])) {
                        $edi .= "LOC+7+" . $rowCells[6] . ":139:6'\n";
                        $line++;
                    }
                    if (isset($rowCells[19])) {
                        $edi .= "LOC+9+" . $rowCells[19] . ":139:6'\n";
                        $line++;
                    }
                    if (isset($rowCells[13])) {
                        $edi .= "MEA+AAE+VGM+KGM:" . $rowCells[13] . "'\n";
                        $line++;
                    }
                    if (isset($rowCells[17]) && trim($rowCells[17]) != "" && trim($rowCells[17]) != "/") {
                        $tmp = explode(',', $rowCells[17]);
                        //   str_split($rowCells[1], '/');
                        for ($i = 0; $i < count($tmp); $i++) {
                            $dim = explode('/', $rowCells[17]);
                            if (trim($dim[0]) == "OF") {
                                $edi .= "DIM+5+CMT:" . trim($dim[1]) . "'\n";
                                $line++;
                            }
                            if (trim($dim[0]) == "OB") {
                                $edi .= "DIM+6+CMT:" . trim($dim[1]) . "'\n";
                                $line++;
                            }
                            if (trim($dim[0]) == "OR") {
                                $edi .= "DIM+7+CMT::" . trim($dim[1]) . "'\n";
                                $line++;
                            }
                            if (trim($dim[0]) == "OL") {
                                $edi .= "DIM+8+CMT::" . trim($dim[1]) . "'\n";
                                $line++;
                            }
                            if (trim($dim[0]) == "OH") {
                                $edi .= "DIM+9+CMT:::" . trim($dim[1]) . "'\n";
                                $line++;
                            }
                        }
                    }
                    if (isset($rowCells[15]) && trim($rowCells[15]) != "" && trim($rowCells[15]) != "/") {
                        $temperature = $rowCells[15];
                        $temperature = str_replace(" ", "", $temperature);
                        $temperature = str_replace("C", "", $temperature);
                        $temperature = str_replace("+", "", $temperature);
                        $edi .= "TMP+2+" . $temperature . ":CEL'\n";
                        $line++;
                    }
                    if (isset($rowCells[25]) && trim($rowCells[25]) != "" && trim($rowCells[25]) != "/") {
                        $tmp = explode(',', $rowCells[25]);
                        if ($tmp[0] == "L") {
                            $edi .= "SEL+" . $tmp[1] . "+CA'\n";
                            $line++; //seal L - CA, S - SH, M - CU
                        }
                        if ($tmp[0] == "S") {
                            $edi .= "SEL+" . $tmp[1] . "+SH'\n";
                            $line++; //seal L - CA, S - SH, M - CU
                        }
                        if ($tmp[0] == "M") {
                            $edi .= "SEL+" . $tmp[1] . "+CU'\n";
                            $line++; //seal L - CA, S - SH, M - CU
                        }
                    }
                    if (isset($rowCells[8])) {
                        $edi .= "FTX+AAI+++" . $rowCells[8] . "'\n";
                        $line++;
                    }

                    if (isset($rowCells[12]) && trim($rowCells[12]) != "" && trim($rowCells[12]) != "/") {
                        $edi .= "FTX+AAA+++" . trim($rowCells[12]) . "'\n";
                        $line++;
                    }
                    if (isset($rowCells[18]) && trim($rowCells[18]) != "" && trim($rowCells[18]) != "/") {
                        $edi .= "FTX+HAN++" . $rowCells[18] . "'\n";
                        $line++;
                    }
                    if (isset($rowCells[14]) && $rowCells[14] != "" && trim($rowCells[14]) != "/") {

                        $tmp = explode('/', $rowCells[14]);
                        $edi .= "DGS+IMD+" . $tmp[0] . "+" . $tmp[1] . "'\n";
                        $line++;
                    }
                    if (isset($rowCells[2]) && trim($rowCells[2]) != "") {
                        $edi .= "NAD+CF+" . $rowCells[2] . ":160:ZZZ'\n";
                        $line++;
                    } //box 
                    //if(opr!="") { $edi .= "NAD+CA+"+opr+":160:ZZZ'\n"; $line++; } //vsl
                    //if(isset() $rowCells[27] && trim($rowCells[27])!="")  { $edi .= "NAD+GF+"+$rowCells[27]+":160:ZZZ'\n"; $line++; } //slot
                }

                /*if (singleRow === 0) {
              table .= '<thead>';
              table .= '<tr>';
            } else {
              table .= '<tr>';
            }
            $ $rowCells = allRows[singleRow].split(',');
            for ($ rowCell = 0; rowCell < $rowCells.length; rowCell++) {
              if (singleRow === 0) {
                table .= '<th>';
                table .= $rowCells[rowCell];
                table .= '</th>';
              } else {
                table .= '<td>';
                table .= $rowCells[rowCell];
                table .= '</td>';
              }
            }
            if (singleRow === 0) {
              table .= '</tr>';
              table .= '</thead>';
              table .= '<tbody>';
            } else {
              table .= '</tr>';
            }*/
            }
        }

        // dd($edi);
        $contcount--;
        $edi .= "CNT+16:" . $contcount . "'\n";
        $line++;
        $line++;
        $edi .= "UNT+" . $line . "+" . $refno . "'\n";
        $edi .= "UNZ+1+" . $refno . "'";
        //table += '</tbody>';
        //table += '</table>';
        return view('result', ['edi' => $edi]);
        // $('#my_file_output').val(edi);



    } //


    public function get_date_str($d, $type)
    {
        $now = $d;
        // dd($now . date("Y"));
        $dt = date("t");
        // dd($dt);
        $dt = (strlen($dt) < 2) ? "0" + $dt : $dt;

        $hrs = date("h");

        $hrs = (strlen($dt) < 2) ? "0" + $hrs : $hrs;

        $min = date("i");

        $min = (strlen($dt) < 2) ? "0" + $min : $min;
        $sec = date("s");

        $sec = (strlen($dt) < 2) ? "0" + $sec : $sec;
        $mth =  date("m");
        $mth = (strlen($dt) < 2) ? "0" + $mth : $mth;
        // dd($mth);
        if ($type == "daterawonly") {
            return date("Y") . '' . $mth . '' . $dt;
        } else if ($type == "timetominrawonly") {
            return $hrs . '' . $min;
        } else {
            return  date("Y") . '' . $mth . '' . $dt . '' . $hrs . '' . $min . '' . $sec;
        }
        //return now.getHours()+':'+String(min)+':'+String(sec);
    }
}
