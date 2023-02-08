<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// 追加分
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Models\Emp;
use App\Models\Dept;
use App\Models\UserLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\ErrorHandler\Debug;

class TestController extends Controller {

    // ログインユーザー情報取得
    public function profile(Request $req) {
        $user = Auth::user()->id;
        $profile = DB::table('emp')->where('id', $user)->get();
        return $profile;
    }

    // 名前変更
    public function enameChange(Request $req) {
        $empno = $req->input('empno');
        $rename = $req->input('rename');
        DB::table('emp')->where('empno', $empno)->update(['ename' => $rename]);
    }

    // メールアドレス変更
    public function emailChange(Request $req) {
        $empno = $req->input('empno');
        $email = $req->input('change_email');
        $mail =  DB::table('emp')->select('email')->get();
        $encode = json_decode(json_encode($mail), true);
        $array = Arr::flatten($encode);

        if (in_array($email, $array)) {
            $data = ['message' => 'このメールアドレスは既に使用されています'];
            return $data;
        } else {
            DB::table('emp')->where('empno', $empno)->update([
                'email' => $email,
            ]);
        }
    }

    // 郵便番号から住所取得　途中
    public function getAddress(Request $req) {
        $zip = $req->input('change_post_code');
        $searchaddress = DB::table('addresses')->select('*')->where('zip', 'like', "$zip")->get();
        $encode = json_decode(json_encode($searchaddress), true);

        if (empty($encode)) {
            $data = ['message' => '存在しない郵便番号です'];
            return $data;
        } else {
            return $searchaddress;
        }
    }

    // 住所変更
    public function addressChange(Request $req) {
        $empno = $req->input('empno');
        $post_code = $req->input('change_post_code');
        $address1 = $req->input('change_address1');
        $address2 = $req->input('change_address2');
        DB::table('emp')->where('empno', $empno)->update([
            'post_code' => $post_code,
            'address1' => $address1,
            'address2' => $address2,
        ]);
    }

    // 電話番号変更
    public function phoneChange(Request $req) {
        $empno = $req->input('empno');
        $phone_number = $req->input('change_phone_number');
        DB::table('emp')->where('empno', $empno)->update(['phone_number' => $phone_number]);
    }

    // 権限情報取得
    public function roleList(Request $req) {
        $role = DB::table('roles')->orderby('id', 'asc')->get();
        return $role;
    }

    // 社員一覧全データ取得
    public function list1Data(Request $req) {
        $array = $req->array;
        if (in_array(1, $array)) {
            $data =
            Emp::select('emp.id', 'empno', 'ename', 'job', 'mgr', 'hiredate', 'sal', 'comm', 'deptno', 'img1', 'img2', 'roles.name')
            ->join('roles', 'emp.role', '=', 'roles.id')
                ->orderby('id', 'asc')->paginate();
            return $data;
        }
    }

    // 社員一覧編集
    public function edit1(Request $req) {
        $array = $req->array;
        $empno = $req->input('empno');
        $c_id = $req->input('c_id');
        $c_empno = $req->input('c_empno');
        $c_ename = $req->input('c_ename');
        $c_job = $req->input('c_job');
        $c_mgr = $req->input('c_mgr');
        $c_hiredate = $req->input('c_hiredate');
        $c_sal = $req->input('c_sal');
        $c_comm = $req->input('c_comm');
        $c_deptno = $req->input('c_deptno');
        $c_role = $req->input('c_role');

        if (in_array(2, $array)) {
            DB::table('emp')->where('empno', $empno)->update([
                'id' => $c_id,
                'empno' => $c_empno,
                'ename' => $c_ename,
                'job' => $c_job,
                'mgr' => $c_mgr,
                'hiredate' => $c_hiredate,
                'sal' => $c_sal,
                'comm' => $c_comm,
                'deptno' => $c_deptno,
                'role' => $c_role
            ]);
            return true;
        } else {
            $data = ['alert_message' => '不正な通信です'];
            return $data;
        }
    }

    // 社員一覧追加
    public function insert1(Request $req) {
        $array = $req->array;
        $insid = $req->input('c_id');
        $insempno = $req->input('c_empno');
        $insename = $req->input('c_ename');
        $insjob = $req->input('c_job');
        $insmgr = $req->input('c_mgr');
        $inshiredate = $req->input('c_hiredate');
        $inssal = $req->input('c_sal');
        $inscomm = $req->input('c_comm');
        $insdeptno = $req->input('c_deptno');

        if (in_array(2, $array)) {
            DB::table('emp')->insert([
                'id' => $insid,
                'password' => Hash::make($insid),
                'empno' => $insempno,
                'ename' => $insename,
                'job' => $insjob,
                'mgr' => $insmgr,
                'hiredate' => $inshiredate,
                'sal' => $inssal,
                'comm' => $inscomm,
                'deptno' => $insdeptno,
            ]);
            return true;
        } else {
            $data = ['alert_message' => '不正な通信です'];
            return $data;
        }
    }

    // 社員一覧削除
    public function delete1(Request $req) {
        $array = $req->array;
        $empno = $req->input('empno');

        if (in_array(2, $array)) {
            DB::table('emp')->where('empno', $empno)->delete();
            return true;
        } else {
            $data = ['alert_message' => '不正な通信です'];
            return $data;
        }
    }

    // 部署一覧全データ取得
    public function list2Data(Request $req) {
        $array = $req->array;
        if (in_array(3, $array)) {
            $data = Dept::orderby('deptno', 'asc')->paginate();
            return $data;
        }
    }

    // 部署一覧編集
    public function edit2(Request $req) {
        $array = $req->array;
        $deptno = $req->input('deptno');
        $insdeptno = $req->input('c_deptno');
        $insdname = $req->input('c_dname');
        $insloc = $req->input('c_loc');
        $inssort = $req->input('c_sort');

        if (in_array(4, $array)) {
            DB::table('dept')->where('deptno', $deptno)->update([
                'deptno' => $insdeptno,
                'dname' => $insdname,
                'loc' => $insloc,
                'sort' => $inssort,
            ]);
            return true;
        } else {
            $data = ['alert_message' => '不正な通信です'];
            return $data;
        }
    }

    // 部署一覧追加
    public function insert2(Request $req) {
        $array = $req->array;
        $insdeptno = $req->input('c_deptno');
        $insdname = $req->input('c_dname');
        $insloc = $req->input('c_loc');
        $inssort = $req->input('maxSortNum') + 1;

        if (in_array(4, $array)) {
            DB::table('dept')->insert([
                'deptno' => $insdeptno,
                'dname' => $insdname,
                'loc' => $insloc,
                'sort' => $inssort,
            ]);

            return true;
        } else {
            $data = ['alert_message' => '不正な通信です'];
            return $data;
        }
    }

    // 部署一覧削除
    public function delete2(Request $req) {
        $array = $req->array;
        $deptno = $req->input('deptno');

        if (in_array(4, $array)) {
            DB::table('dept')->where('deptno', $deptno)->delete();
            return true;
        } else {
            $data = ['alert_message' => '不正な通信です'];
            return $data;
        }
    }

    // 部署一覧ドラッグドロップで並び替えDB保存
    public function sortNumChange(Request $req) {
        $deptnoList = $req->input('deptnoList');
        $page = $req->input('page');
        $dispNum = $req->input('dispNum');
        $sort = 1 + ($page * $dispNum);
        for ($i = 0; $i < count($deptnoList); $i++) {
            DB::table('dept')->where('deptno', $deptnoList[$i])->update([
                'sort' => $sort,
            ]);
            $sort++;
        }
        return true;
    }

    // 社員一覧CSVエクスポート
    public function empCsvEx(Request $req) {
        $searchName = $req->input('searchName');
        $searchSal_low = $req->input('searchSal_low');
        $searchSal_high = $req->input('searchSal_high');
        $category = $req->input('category');
        $sort = $req->input('sort');

        $file = function () use ($searchName, $searchSal_low, $searchSal_high, $category, $sort) {
            // CSVファイル作成
            $csv = fopen('php://output', 'w');

            // CSVの1行目
            $colums = [
                'id',
                'empno',
                'ename',
                'job',
                'mgr',
                'hiredate',
                'sal',
                'comm',
                'deptno',
                'role',
            ];

            // CSVの1行目を記入
            fputcsv($csv, $colums);

            // CSVの2行目以降

            $data = Emp::where('ename', 'like', '%' . $searchName . '%')
                        ->where('sal', '>=', $searchSal_low)
                        ->where('sal', '<=', $searchSal_high)
                        ->orderby($category, $sort)->get();
            foreach ($data as $emp) {
                $userData = [
                    $emp->id,
                    $emp->empno,
                    $emp->ename,
                    $emp->job,
                    $emp->mgr,
                    $emp->hiredate,
                    $emp->sal,
                    $emp->comm,
                    $emp->deptno,
                    $emp->role,
                ];
                // 文字化け対策はvue側で行うのでUTF-8のまま返す

                // CSVファイルの2行目以降にユーザー情報を記入
                fputcsv($csv, $userData);
            }
            // CSVファイルを閉じる
            fclose($csv);
        };
        // }
        // ファイル名
        $fileName = 'emp' . now()->format('Ymd') . '.csv';

        // レスポンスヘッダー情報
        $responseHeader = [
            'Content-Type' => 'text/csv',
            'Access-Control-Expose-Headers' => 'Content-Disposition'
        ];
        return response()->streamDownload($file, $fileName, $responseHeader);
    }

    // 社員一覧CSVインポート 未完成　文字化け
    public function empCsvIn(Request $req) {
        $row = $req->input('csv');
        $row_count = 2;
        // $valA[] = "";
        // $valB[] = "";

        for ($i = 1; $i < count($row); $i++) {
            $row2 = $row[$i];

            $id = $row2['id'];
            $empno = $row2['empno'];
            $ename = $row2['ename'];
            $job = $row2['job'];
            $mgr = $row2['mgr'];
            $hiredate = $row2['hiredate'];
            $sal = $row2['sal'];
            $comm = $row2['comm'];
            $deptno = $row2['deptno'];
            $role = $row2['role'];


            if (isset($id) && preg_match("/^[a-zA-Z0-9!-\/:-@¥[-`{_~?]+$/", $id)) { //issetで値が空でないか、また、preg_matchで正規表現チェック
            } else {
                if (empty($id)) { //emptyで値が空であればtrue
                    $eid = 'id:空白';
                } else {
                    $eid = 'id:' . $id;
                }
                $valA[] = $eid;
                $valB[] = $row_count;
            }

            if (isset($empno) && preg_match("/^([1-9][0-9]{3})/", $empno)) { //issetで値が空でないか、また、preg_matchで正規表現チェック
            } else {
                if (empty($empno)) { //emptyで値が空であればtrue
                    $emp = 'empno:空白';
                } else {
                    $emp = 'empno:' . $empno;
                }
                $valA[] = $emp;
                $valB[] = $row_count;
            }

            if (!empty($ename) && is_string($ename)) {
            } else {
                if (empty($ename)) {
                    $en = 'ename:空白';
                } else {
                    $en = 'ename:' . $ename;
                }
                $valA[] = $en;
                $valB[] = $row_count;
            }

            if (!empty($job) && is_string($job)) {
            } else {
                if (empty($job)) {
                    $jo = 'job:空白';
                } else {
                    $jo = 'job:' . $job;
                }
                $valA[] = $jo;
                $valB[] = $row_count;
            }

            if (isset($mgr) && preg_match("/^([1-9][0-9]{3})/", $mgr)) {
            } else {
                if (empty($mgr)) {
                    $mg = 'mgr:空白';
                } else {
                    $mg = 'mgr:' . $mgr;
                }
                $valA[] = $mg;
                $valB[] = $row_count;
            }

            if (preg_match("/\d{4}\/\d{1,2}\/\d{1,2}/", $hiredate)) {
            } else {
                if (empty($hiredate)) { //emptyで値が空であればtrue
                    $hire = 'hiredate:空白';
                } else {
                    $hire = 'hiredate:' . $hiredate;
                }
                $valA[] = $hire;
                $valB[] = $row_count;
            }

            if (preg_match("/^[1-9][0-9]*/", $sal)) {
            } else {
                if (empty($sal)) { //emptyで値が空であればtrue
                    $sa = 'sal:空白';
                } else {
                    $sa = 'sal:' . $sal;
                }
                $valA[] = $sa;
                $valB[] = $row_count;
            }

            if (isset($comm) && preg_match("/^[1-9][0-9]*/", $comm)) {
            } else {
                if (empty($comm)) {
                    $com = 'comm:空白';
                } else {
                    $com = 'comm:' . $comm;
                }
                $valA[] = $com;
                $valB[] = $row_count;
            }

            if (isset($deptno) && preg_match("/^[1-9][0-9]$/", $deptno)) {
            } else {
                if (empty($deptno)) {
                    $dep = 'deptno:空白';
                } else {
                    $dep = 'deptno:' . $deptno;
                }
                $valA[] = $dep;
                $valB[] = $row_count;
            }

            if (isset($role) && preg_match("/^[1-3]$/", $role)) {
            } else {
                if (empty($role)) {
                    $rol = 'role:空白';
                } else {
                    $rol = 'role:' . $role;
                }
                $valA[] = $rol;
                $valB[] = $row_count;
            }
            $row_count++;
        }

        if (!empty($valA)) { // $valAに値があれば(エラーがあれば)true
            $resp = array('valA' => $valA, 'valB' => $valB);
            return $resp; // エラー内容を返す
        } else {
            // 比較用データ作成。昇順で取得
            $check = array();
            $list = DB::table('emp')->select('empno')->orderby('empno', 'asc')->get();
            foreach ($list as $key) {
                $value = $key->empno;
                array_push($check, $value);
            }
            for ($z = 1; $z < count($row); $z++) {
                if (in_array($row[$z]['empno'], $check)) {
                    // 同じempnoだった場合は更新
                    DB::table('emp')->where('empno', $row[$z]['empno'])->update([
                        'id' => $row[$z]['id'],
                        'empno' => $row[$z]['empno'],
                        'ename' => $row[$z]['ename'],
                        'job' => $row[$z]['job'],
                        'mgr' => $row[$z]['mgr'],
                        'hiredate' => $row[$z]['hiredate'],
                        'sal' => $row[$z]['sal'],
                        'comm' => $row[$z]['comm'],
                        'deptno' => $row[$z]['deptno'],
                        'role' => $row[$z]['role'],
                    ]);
                    continue;
                } else {
                    // 同じempnoが無かった場合は追加
                    DB::table('emp')->insert([
                        'id' => $row[$z]['id'],
                        'password' => Hash::make($row[$z]['id']),
                        'empno' => $row[$z]['empno'],
                        'ename' => $row[$z]['ename'],
                        'job' => $row[$z]['job'],
                        'mgr' => $row[$z]['mgr'],
                        'hiredate' => $row[$z]['hiredate'],
                        'sal' => $row[$z]['sal'],
                        'comm' => $row[$z]['comm'],
                        'deptno' => $row[$z]['deptno'],
                        'role' => $row[$z]['role'],
                    ]);
                    continue;
                }
            }
            return true;
        }
    }

    // 部署一覧CSVエクスポート
    public function deptCsvEx(Request $req) {
        $searchName = $req->input('searchName');
        $category = $req->input('category');
        $sort = $req->input('sort');

        $file = function () use ($searchName, $category, $sort) {
            // CSVファイル作成
            $csv = fopen('php://output', 'w');

            // CSVの1行目
            $colums = [
                'deptno',
                'dname',
                'loc',
                'sort',
            ];

            // CSVの1行目を記入
            fputcsv($csv, $colums);

            // CSVの2行目以降

            $data = Dept::where('dname', 'like', '%' . $searchName . '%')
                        ->orderby($category, $sort)->get();
            foreach ($data as $dept) {
                $userData = [
                    $dept->deptno,
                    $dept->dname,
                    $dept->loc,
                    $dept->sort,
                ];
                // 文字化け対策はvue側で行うのでUTF-8のまま返す

                // CSVファイルの2行目以降にユーザー情報を記入
                fputcsv($csv, $userData);
            }
            // CSVファイルを閉じる
            fclose($csv);
        };
        // }
        // ファイル名
        $fileName = 'dept' . now()->format('Ymd') . '.csv';

        // レスポンスヘッダー情報
        $responseHeader = [
            'Content-Type' => 'text/csv',
            'Access-Control-Expose-Headers' => 'Content-Disposition'
        ];
        return response()->streamDownload($file, $fileName, $responseHeader);
    }

    // 部署一覧CSVインポート 未完成　文字化け
    public function deptCsvIn(Request $req) {
        $row = $req->input('csv');

        // $row = mb_convert_variables('utf-8', 'SJIS-win', $row);
        // $row = mb_convert_encoding($row, 'UTF-8', 'SJIS-win');

        // Log::debug(print_r($row, true));

        $row_count = 2;
        // $valA[] = null;
        // $valB[] = "";

        for ($i = 1; $i < count($row); $i++) {
            $row2 = $row[$i];

            $deptno = $row2['deptno'];
            $dname = $row2['dname'];
            $loc = $row2['loc'];
            $sort = $row2['sort'];

            if (isset($deptno) && preg_match("/^[1-9][0-9]$/", $deptno)) { //issetで値が空でないか、また、preg_matchで正規表現チェック
            } else {
                if (empty($deptno)) { //emptyで値が空であればtrue
                    $dep = 'deptno:空白';
                } else {
                    $dep = 'deptno:' . $deptno;
                }
                $valA[] = $dep;
                $valB[] = $row_count;
            }

            if (!empty($dname) && is_string($dname)) {
            } else {
                if (empty($dname)) {
                    $dn = 'dname:空白';
                } else {
                    $dn = 'dname:' . $dname;
                }
                $valA[] = $dn;
                $valB[] = $row_count;
            }

            if (!empty($loc) && is_string($loc)) {
            } else {
                if (empty($loc)) {
                    $lo = 'loc:空白';
                } else {
                    $lo = 'loc:' . $loc;
                }
                $valA[] = $lo;
                $valB[] = $row_count;
            }

            if (isset($sort) && preg_match("/^[1-9][0-9]*$/", $sort)) {
            } else {
                if (empty($sort)) {
                    $so = 'sort:空白';
                } else {
                    $so = 'sort:' . $sort;
                }
                $valA[] = $so;
                $valB[] = $row_count;
            }
            $row_count++;
        }

        if (!empty($valA)) { //$valAに値があれば(エラーがあれば)true
            $resp = array('valA' => $valA, 'valB' => $valB);
            return $resp; //エラー内容を返す
        } else {
            //比較用データ作成。昇順で取得
            $check = array();
            $list = DB::table('dept')->select('deptno')->orderby('deptno', 'asc')->get();
            foreach ($list as $key) {
                $value = $key->deptno;
                array_push($check, $value);
            }
            for ($z = 1; $z < count($row); $z++) {
                if (in_array($row[$z]['deptno'], $check)) {
                    // 同じdeptnoだった場合は更新
                    DB::table('dept')->where('deptno', $row[$z]['deptno'])->update([
                        'deptno' => $row[$z]['deptno'],
                        'dname' => $row[$z]['dname'],
                        'loc' => $row[$z]['loc'],
                        'sort' => $row[$z]['sort'],
                    ]);
                    continue;
                } else {
                    // 同じdeptnoが無かった場合は追加
                    DB::table('dept')->insert([
                        'deptno' => $row[$z]['deptno'],
                        'dname' => $row[$z]['dname'],
                        'loc' => $row[$z]['loc'],
                        'sort' => $row[$z]['sort'],
                    ]);
                    continue;
                }
            }
            return true;
        }
    }

    // 画像変更
    public function imgUp(Request $req) {
        $empno = $req->empno;
        if ($req->imgNum == "image1") {
            // formDataからfileを取り出す
            $image = $req->file;
            // ファイルを文字列とする
            $image = file_get_contents($image);
            // ファイルをbase64でエンコード
            $image = base64_encode($image);

            // 上記処理にて保存した画像を、empテーブルのimg2カラムに格納
            DB::table('emp')->where('empno', $empno)->update(['img1' => $image]);

            return true;
        } else if ($req->imgNum == "image2") {
            // formDataからfileを取り出す
            $image = $req->file;
            // storeAs→名前を変更して保存して保存先のパスを返す
            $imagePath = $image->storeAs('public/img/', $req->input('name'));
            // HTML出力用の整形とパスの修正
            $imagePath = 'storage' . str_replace('public', '', $imagePath);

            // 上記処理にて保存した画像を、empテーブルのimg2カラムに格納
            DB::table('emp')->where('empno', $empno)->update(['img2' => $imagePath]);

            return true;
        }
    }

    // 画像削除
    public function imgdelete(Request $req) {
        $empno = $req->input('empno');
        $img = $req->input('num');
        $select = ($img == "image1") ? "img1" : "img2";

        DB::table('emp')->where('empno', $empno)->update([$select => NULL]);
        return true;
    }

    // ログ一覧データ取得
    public function logData(Request $req) {
        $data = UserLog::orderby('access_time', 'desc')->get();
        return $data;
    }

    // ログ一覧CSVエクスポート
    public function logCsvEx(Request $req) {
        $searchTitle = $req->input('searchTitle');
        $searchWord = $req->input('searchWord');
        $firstday = $req->input('day1');
        $finalDay = $req->input('day2');
        $category = $req->input('category');
        $sort = $req->input('sort');

        $file = function () use ($searchTitle, $searchWord, $firstday, $finalDay, $category, $sort) {
            // CSVファイル作成
            $csv = fopen('php://output', 'w');

            // CSVの1行目
            $colums = [
                'access_time',
                'user_id',
                'ip_address',
                'user_agent',
                'session_id',
                'access_url',
                'operation',
            ];

            // CSVの1行目を記入
            fputcsv($csv, $colums);

            // CSVの2行目以降
            if ($searchTitle === 'アクセスタイム') {
                $data = DB::table('user_logs')->select('*')->where('access_time', '>=', $firstday)
                    ->where('access_time', '<=', $finalDay)
                    ->orderby($category, $sort)->get();
            } else {
                if (empty($searchTitle)) {
                    $searchTitle = "user_id";
                    $searchWord = "";
                } else {
                    switch ($searchTitle) {
                        case 'ログインID':
                            $searchTitle = "user_id";
                            break;
                        case 'IPアドレス':
                            $searchTitle = "ip_address";
                            break;
                        case 'ユーザーエージェント':
                            $searchTitle = "user_agent";
                            break;
                        case 'セッションID':
                            $searchTitle = "session_id";
                            break;
                        case 'アクセスURL':
                            $searchTitle = "access_url";
                            break;
                        case '実行操作':
                            $searchTitle = "operation";
                            break;
                    }
                }

                $data = DB::table('user_logs')->select('*')->where($searchTitle, 'like', '%' . $searchWord . '%')
                    ->orderby($category, $sort)->get();
            }
            foreach ($data as $log) {
                $userData = [
                    $log->access_time,
                    $log->user_id,
                    $log->ip_address,
                    $log->user_agent,
                    $log->session_id,
                    $log->access_url,
                    $log->operation,
                ];
                // 文字化け対策はvue側で行うのでUTF-8のまま返す

                // CSVファイルの2行目以降にユーザー情報を記入
                fputcsv($csv, $userData);
            }
            // CSVファイルを閉じる
            fclose($csv);
        };
        // }
        // ファイル名
        $fileName = 'log' . now()->format('Ymd') . '.csv';

        // レスポンスヘッダー情報
        $responseHeader = [
            'Content-Type' => 'text/csv',
            'Access-Control-Expose-Headers' => 'Content-Disposition'
        ];
        return response()->streamDownload($file, $fileName, $responseHeader);
    }

    // ログデータ削除
    public static function logdeletion() {
        $conditions = DB::table('schedulers')->select('conditions')->where('name', 'log_delete')->get();
        $encode = json_decode(json_encode($conditions), true);
        $array = Arr::flatten($encode);
        $condition = $array[0];

        $limitday = Carbon::today()->subDay($condition);
        DB::table('user_logs')->whereDate('created_at', '<=', $limitday)->delete();
    }

    // スケジュール設定取得
    public function scheduler(Request $req) {
        $array = $req->array;

        if (in_array(5, $array)) {
            $scheduler = DB::table('schedulers')->get();

            return $scheduler;
        } else {
            return redirect(route('dashboard'));
        }
    }

    // スケジュール設定1
    public function setting1(Request $req) {
        $array = $req->array;

        if (in_array(5, $array)) {
            $c_num = $req->input('c_num');
            $c_interval = str_replace('時', '', $req->input('c_interval'));
            $c_interval1 = str_replace('時', '', $req->input('c_interval1'));
            $c_interval2 = str_replace('時', '', $req->input('c_interval2'));

            DB::table('schedulers')->where('name', 'log_delete')->update([
                'num' => $c_num,
                'interval' => $c_interval,
                'interval1' => $c_interval1,
                'interval2' => $c_interval2,
                'intervalhour' => "",
            ]);

            return true;
        } else {
            $data = ['alert_message' => '不正な通信です'];
            return $data;
        }
    }

    // スケジュール設定2
    public function setting2(Request $req) {
        $array = $req->array;

        if (in_array(5, $array)) {
            $c_intervalHour = str_replace('分', '', $req->input('c_intervalHour'));

            DB::table('schedulers')->where('name', 'log_delete')->update([
                'num' => "",
                'interval' => "",
                'interval1' => "",
                'interval2' => "",
                'intervalhour' => $c_intervalHour,
            ]);

            return true;
        } else {
            $data = ['alert_message' => '不正な通信です'];
            return $data;
        }
    }

    // スケジュール設定3
    public function setting3(Request $req) {
        $array = $req->array;

        if (in_array(5, $array)) {
            $c_conditions = str_replace('日', '', $req->input('c_conditions'));

            DB::table('schedulers')->where('name', 'log_delete')->update([
                'conditions' => $c_conditions,
            ]);

            return true;
        } else {
            $data = ['alert_message' => '不正な通信です'];
            return $data;
        }
    }

    // ログタイトル選択リスト取得
    public function logList(Request $req) {
        $logList = Config('log_list.list');
        return $logList;
    }

    // 時間選択リスト取得
    public function hourList(Request $req) {
        $hourList = Config('hours.list');
        return $hourList;
    }

    // 期間選択リスト取得
    public function timeList(Request $req) {
        $timeList = Config('time.list');
        return $timeList;
    }

    // 保存日数選択リスト取得
    public function dayList(Request $req) {
        $dayList = Config('days.list');
        return $dayList;
    }
}
