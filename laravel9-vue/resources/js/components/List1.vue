<script setup>
import axios, { all, formToJSON } from 'axios';
import { ref, onMounted, watch } from 'vue';
import { VueCsvImportPlugin } from "vue-csv-import";
import { VueCsvToggleHeaders, VueCsvSubmit, VueCsvMap, VueCsvInput, VueCsvErrors, VueCsvImport } from 'vue-csv-import';
import { saveAs } from 'file-saver';
import Encoding from 'encoding-japanese';
// import Pagination from './Pagination.vue';

// ログインユーザー情報取得
const loginUseRole = ref("");
onMounted(async () => {
    try {
        const response = await axios.get('/profile');
        loginUseRole.value = response.data[0].role;
    } catch (error) {
        alert("NG"); // 後で修正
    }
});

// 権限リスト取得
const roleList = ref("");
onMounted(async () => {
    try {
        const response = await axios.get('/roleList');
        roleList.value = response.data;
    } catch (error) {
        alert("NG"); // 後で修正
    }
});

// 部署コードリスト取得
const deptnoList = ref("");
onMounted(async () => {
    try {
        const response = await axios.get('/list2Data');
        deptnoList.value = response.data.data;
        deptnoList.value.sort(function (a, b) {
            let cateA = a.sort;
            let cateB = b.sort;
            if (cateA < cateB) return -1;
            if (cateA > cateB) return 1;
            return 0;
        });
    } catch (error) {
        alert("NG"); // 後で修正
    }
});

// 一覧リスト取得
const allData   = ref("");    // 全データ
const data      = ref("");    // 変動データ
const dispData  = ref("");    // 表示用データ
const page      = ref(0);     // 表示されてるページ
const last_page = ref();      // 最終ページ
const category  = ref("id");  // 並びカテゴリ
const sort      = ref("asc"); // 並び順
const dispNum   = ref(5);     // １ページあたりの表示件数

// データ表示関数
function disp() {
    const disp = [];
    for (let key in data.value) {
        if (0 <= key && key < dispNum.value) {
            disp.push(data.value[key]);
        }
    }
    page.value = 0;
    dispData.value = disp;
    last_page.value = Math.ceil(data.value.length / dispNum.value);
}

// 全データ取得
onMounted(async () => {
    try {
        const response = await axios.get('/list1Data');
        allData.value = response.data.data;
        data.value = allData.value;
        disp();
    } catch (error) {
        alert("データ取得に失敗しました"); // 後で修正
    }
});

// ページ遷移
const pageChange = (b_page) => {
    const select = [];
    page.value = b_page - 1;
    for (let key in data.value) {
        if (page.value * dispNum.value <= key && key < dispNum.value * b_page) {
            select.push(data.value[key]);
        }
    }
    dispData.value = select;
}

// データ取得
const changeBtn2 = ref(true);
const changeBtn3 = ref(true);
const changeBtn4 = ref(true);
const changeBtn5 = ref(true);
const changeBtn6 = ref(true);
const changeBtn7 = ref(true);
const changeBtn8 = ref(true);
const changeBtn9 = ref(true);
const changeBtn10 = ref(true);
const searchName = ref("");
const searchSal_low = ref(0);
const searchSal_high = ref(99999);
function getData(val) {
    if (val !== "") {
        category.value = val.substring(0, val.indexOf(':'));
        const convert = val.substring(val.indexOf(':') + 1);
        if (convert == "▲") {
            sort.value = "asc";
        } else {
            sort.value = "desc";
        }
    }

    const select = [];
    for (let key in data.value) {
        if (data.value[key].ename.includes(searchName.value)
                            && data.value[key].sal >= searchSal_low.value
                            && data.value[key].sal < searchSal_high.value) {
            select.push(data.value[key]);
        }
    }
    if (sort.value == "asc") {
        switch (category.value) {
            case 'id':
                select.sort(function (a, b) {
                    let cateA = a.id.toUpperCase();
                    let cateB = b.id.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn2.value = !changeBtn2.value;
                break;
            case 'empno':
                select.sort(function (a, b) {
                    let cateA = a.empno;
                    let cateB = b.empno;
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn3.value = !changeBtn3.value;
                break;
            case 'ename':
                select.sort(function (a, b) {
                    let cateA = a.ename.toUpperCase();
                    let cateB = b.ename.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn4.value = !changeBtn4.value;
                break;
            case 'job':
                select.sort(function (a, b) {
                    let cateA = a.job.toUpperCase();
                    let cateB = b.job.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn5.value = !changeBtn5.value;
                break;
            case 'mgr':
                select.sort(function (a, b) {
                    let cateA = a.mgr;
                    let cateB = b.mgr;
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn6.value = !changeBtn6.value;
                break;
            case 'hiredate':
                select.sort(function (a, b) {
                    let cateA = a.hiredate.toUpperCase();
                    let cateB = b.hiredate.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn7.value = !changeBtn7.value;
                break;
            case 'sal':
                select.sort(function (a, b) {
                    let cateA = a.sal;
                    let cateB = b.sal;
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn8.value = !changeBtn8.value;
                break;
            case 'comm':
                select.sort(function (a, b) {
                    let cateA = a.comm;
                    let cateB = b.comm;
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn9.value = !changeBtn9.value;
                break;
            case 'deptno':
                select.sort(function (a, b) {
                    let cateA = a.deptno;
                    let cateB = b.deptno;
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn10.value = !changeBtn10.value;
                break;
        }
    } else {
        switch (category.value) {
            case 'id':
                select.sort(function (a, b) {
                    let cateA = a.id.toUpperCase();
                    let cateB = b.id.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn2.value = !changeBtn2.value;
                break;
            case 'empno':
                select.sort(function (a, b) {
                    let cateA = a.empno;
                    let cateB = b.empno;
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn3.value = !changeBtn3.value;
                break;
            case 'ename':
                select.sort(function (a, b) {
                    let cateA = a.ename.toUpperCase();
                    let cateB = b.ename.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn4.value = !changeBtn4.value;
                break;
            case 'job':
                select.sort(function (a, b) {
                    let cateA = a.job.toUpperCase();
                    let cateB = b.job.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn5.value = !changeBtn5.value;
                break;
            case 'mgr':
                select.sort(function (a, b) {
                    let cateA = a.mgr;
                    let cateB = b.mgr;
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn6.value = !changeBtn6.value;
                break;
            case 'hiredate':
                select.sort(function (a, b) {
                    let cateA = a.hiredate.toUpperCase();
                    let cateB = b.hiredate.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn7.value = !changeBtn7.value;
                break;
            case 'sal':
                select.sort(function (a, b) {
                    let cateA = a.sal;
                    let cateB = b.sal;
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn8.value = !changeBtn8.value;
                break;
            case 'comm':
                select.sort(function (a, b) {
                    let cateA = a.comm;
                    let cateB = b.comm;
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn9.value = !changeBtn9.value;
                break;
            case 'deptno':
                select.sort(function (a, b) {
                    let cateA = a.deptno;
                    let cateB = b.deptno;
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn10.value = !changeBtn10.value;
                break;
        }
    }
    data.value = select;
    disp();
    // searchName.value = "";
    // searchSal_low.value = 0;
    // searchSal_high.value = 99999;
}

// 表示リセット
function reset() {
    data.value = allData.value;
    disp();
    searchName.value = "";
    searchSal_low.value = 0;
    searchSal_high.value = 99999;
}

// 追加フォーム表示
const id = ref("");
const empno = ref("");
const ename = ref("");
const job = ref("");
const mgr = ref("");
const hiredate = ref("");
const sal = ref("");
const comm = ref("");
const deptno = ref("");
const roleName = ref("");
const c_id = ref("6001tanaka");
const c_empno = ref(6001);
const c_ename = ref("田中太郎");
const c_job = ref("営業");
const c_mgr = ref(8010);
const c_hiredate = ref("1990-05-05");
const c_sal = ref(1200);
const c_comm = ref(200);
const c_deptno = ref("");
const c_role = ref("");
const changeBtn = ref(true);
const forms = ref([]);
function insert() {
    let form_body = {};
    forms.value.push(form_body);
    changeBtn.value = !changeBtn.value;
};

function toggle() {
    changeBtn.value = !changeBtn.value;
    forms.value = [];
}

// 追加
function insert1() {
    axios
        .post('/insert1', {
            c_id: c_id.value,
            c_empno: c_empno.value,
            c_ename: c_ename.value,
            c_job: c_job.value,
            c_mgr: c_mgr.value,
            c_hiredate: c_hiredate.value,
            c_sal: c_sal.value,
            c_comm: c_comm.value,
            c_deptno: c_deptno.value,
        })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            }
        })
        .catch((error) => alert("通信に失敗しました"));
};

// 編集ダイアログ
const editDialog = ref(false);
function edit(index) {
    id.value = data.value[index + (page.value * dispNum.value)].id;
    empno.value = data.value[index + (page.value * dispNum.value)].empno;
    ename.value = data.value[index + (page.value * dispNum.value)].ename;
    job.value = data.value[index + (page.value * dispNum.value)].job;
    mgr.value = data.value[index + (page.value * dispNum.value)].mgr;
    hiredate.value = data.value[index + (page.value * dispNum.value)].hiredate;
    sal.value = data.value[index + (page.value * dispNum.value)].sal;
    comm.value = data.value[index + (page.value * dispNum.value)].comm;
    deptno.value = data.value[index + (page.value * dispNum.value)].deptno;
    roleName.value = data.value[index + (page.value * dispNum.value)].name;
    editDialog.value = true;
};

// 編集
function edit1() {
    axios
        .post('/edit1', {
            empno: empno.value,
            c_id: c_id.value,
            c_empno: c_empno.value,
            c_ename: c_ename.value,
            c_job: c_job.value,
            c_mgr: c_mgr.value,
            c_hiredate: c_hiredate.value,
            c_sal: c_sal.value,
            c_comm: c_comm.value,
            c_deptno: c_deptno.value,
            c_role: c_role.value.substring(0, c_role.value.indexOf(':'))
        })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            }
            editDialog.value = false;
        })
        .catch((error) => alert("通信に失敗しました"));
};

// 削除ダイアログ
const deleteDialog = ref(false);
function d_delete(index) {
    id.value = data.value[index + (page.value * 5)].id;
    empno.value = data.value[index + (page.value * 5)].empno;
    ename.value = data.value[index + (page.value * 5)].ename;
    job.value = data.value[index + (page.value * 5)].job;
    mgr.value = data.value[index + (page.value * 5)].mgr;
    hiredate.value = data.value[index + (page.value * 5)].hiredate;
    sal.value = data.value[index + (page.value * 5)].sal;
    comm.value = data.value[index + (page.value * 5)].comm;
    deptno.value = data.value[index + (page.value * 5)].deptno;
    roleName.value = data.value[index + (page.value * 5)].name;
    deleteDialog.value = true;
};

// 削除
function delete1() {
    axios
        .post('/delete1', { empno: empno.value })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            }
            deleteDialog.value = false;
        })
        .catch((error) => alert("通信に失敗しました"));
};

// CSVインポートダイアログ
const csv = ref([]);
const csvImportDialog = ref(false);
function csv_i() {
    csvImportDialog.value = true;
};

// CSVインポート
const iError = ref([]);
function empCsvIn() {
    axios
        .post('/empCsvIn', { csv: csv.value })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            } else {
                for (let i = 0; i < res.data.valA.length; i++) {
                    iError.value.push(res.data.valB[i] + "行目の" + res.data.valA[i] + "が正しくありません");
                }
            }
        })
        .catch((error) => alert("通信に失敗しました"));
};

// CSVエクスポート
function empCsvEx() {
    axios
        .post('/empCsvEx', {
            responseType: 'blob', searchName: searchName.value, searchSal_low: searchSal_low.value,
            searchSal_high: searchSal_high.value, category: category.value, sort: sort.value
        })
        .then((res) => {
            let csv = res.data;
            csv = new Encoding.stringToCode(csv)
            csv = Encoding.convert(csv, 'SJIS');
            csv = new Uint8Array(csv);
            const blob = new Blob([csv], { type: csv.type });
            const fileName = res.headers['content-disposition'].replace('attachment; filename=', '');
            saveAs(blob, fileName);
        })
        .catch((error) => alert("通信に失敗しました"));
};

// 画像変更ダイアログ
const imgDialog = ref(false);
const imgNum = ref("");
const check = ref(false);
function editImg(index, val) {
    file.value = "";
    changeImg.value = "";
    imagePath.value = "";
    imgError.value = "";
    imgDialog.value = true;
    empno.value = data.value[index + (page.value * 5)].empno;
    imgNum.value = val;
};

const file = ref("");
const changeImg = ref("");
const imagePath = ref("");   // プレビュー用の画像パス
const imgError = ref("");
const uploadImage = (event) => {
    file.value = event.target.files[0];
    const imgType = ref("");
    const imgSize = ref("");

    // ファイルの拡張子でバリデーション
    let type = file.value.type;
    if (type != 'image/jpg' && type != 'image/jpeg' && type != 'image/png') {
        imgType.value = false;
        imagePath.value = "";
        imgError.value = "ファイルが画像ではありません。対応拡張子(.jpg　.gif　.png)";
        check.value = false;
    } else {
        imgType.value = true;
        imgError.value = "";
        check.value = true;
    }

    // // ファイルのサイズでバリデーション未完成
    imgSize.value = true;

    // プレビュー表示
    if (imgType.value && imgSize.value) {
        const reader = new FileReader()
        reader.readAsDataURL(file.value)
        reader.onloadend = () => {
            changeImg.value = reader.result
            imagePath.value = URL.createObjectURL(file.value)
        }
    // } else {
    //     alert("画像の取得に失敗しました。");
    }
}

// 画像変更
const imgSet = () => {
    check.value = false;
    // ヘッダー定義
    const config = {
        headers: {
            'content-type': 'multipart/form-data'
        }
    };

    // Formデータ作成
    const formData = new FormData();
    formData.append("file", file.value);
    formData.append("name", file.value.name);
    formData.append("empno", empno.value);
    formData.append("imgNum", imgNum.value);

    axios
        .post("/imgUp", formData, config)
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            }
        })
        .catch((error) => alert("通信に失敗しました"));
};

</script>

<template>
<table class="borderless">
    <thead>
        <tr>
            <td colspan="9" class="borderless">
                <!-- 検索 -->
                <p style="text-align: left;">社員名、給与を最小・最大で指定して絞り込みができます。</p>
                社員名
                <input type="text" placeholder="社員名" v-model="searchName" class="searchName">
                給与
                <input type="text" placeholder="給与最低" v-model="searchSal_low" class="searchSal">～
                <input type="text" placeholder="給与最低" v-model="searchSal_high" class="searchSal">
                <v-btn height="30" class="searchBtn" @click.prevent="getData('')">検索</v-btn>
                <v-btn height="30" class="resetBtn" @click.prevent="reset">リセット</v-btn>
            </td>
            <td colspan="5" class="borderless">
                <!-- 表示件数変更 -->
                <p style="text-align: left;">１ページあたりの表示件数を変更できます。</p>
                表示件数：
                <input type="number" v-model="dispNum" class="dispNum" min="1" pattern="^[0-9]+$">
                <v-btn class="dispNumBtn" @click.prevent="disp">変更</v-btn>
            </td>
        </tr>
        <tr>
            <td colspan="14" class="borderless">
                <!-- CSVインポート -->
                <v-btn class="csvImport" @click.prevent="csv_i">CSVインポート</v-btn>

                <!-- CSVエクスポート -->
                <v-btn class="csvExport" @click.prevent="empCsvEx">CSVエクスポート</v-btn>
            </td>
        </tr>
        <tr>
            <td colspan="14" class="borderless">
                <!-- 追加 -->
                <div v-if="loginUseRole == 1 || loginUseRole == 3">
                    <v-btn v-if="changeBtn" class="insert" @click.prevent="insert">社員追加</v-btn>
                    <v-btn v-else class="insert" @click.prevent="toggle" style="margin-bottom: 14px;">戻す</v-btn>
                    <div v-if="forms != ''">
                        <p>新しく社員を追加できます。下記項目に入力後、追加ボタンを押してください。</p>
                        <table>
                            <tr>
                                <th>ユーザＩＤ</th>
                                <th>社員コード</th>
                                <th>社員名</th>
                                <th>職種</th>
                                <th>上司コード</th>
                                <th>入社日</th>
                                <th>給与</th>
                                <th>歩合</th>
                                <th>部署コード</th>
                                <th>役割</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" placeholder="ユーザID" v-model="c_id" class="resize">
                                </td>
                                <td>
                                    <input type="text" placeholder="社員コード" v-model="c_empno" class="resize">
                                </td>
                                <td>
                                    <input type="text" placeholder="社員名" v-model="c_ename" class="resize">
                                </td>
                                <td>
                                    <input type="text" placeholder="職種" v-model="c_job" class="resize">
                                </td>
                                <td>
                                    <input type="text" placeholder="上司コード" v-model="c_mgr" class="resize">
                                </td>
                                <td>
                                    <input type="text" placeholder="入社日" v-model="c_hiredate" class="resize">
                                </td>
                                <td>
                                    <input type="text" placeholder="給与" v-model="c_sal" class="resize">
                                </td>
                                <td>
                                    <input type="text" placeholder="歩合" v-model="c_comm" class="resize">
                                </td>
                                <td>
                                    <select v-model="c_deptno">
                                        <option disabled value="">値を選択</option>
                                        <option v-for="deptno in deptnoList">
                                            <label>{{ deptno.deptno }}</label>
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select v-model="c_role">
                                        <option disabled value="">値を選択</option>
                                        <option v-for="roles in roleList">
                                            <label>{{ roles.id }}:{{ roles.name }}</label>
                                        </option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <p>※初回ログイン時のパスワードはユーザＩＤでログインしてください。
                        ログイン後、メールアドレスを登録し、ログイン画面にてパスワード変更をお願いします。</p>
                    </div>
                    <v-btn v-if="forms != ''" @click.prevent="insert1" style="margin-bottom: 14px;">追加</v-btn>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                <button v-if="changeBtn2" @click.prevent="getData('id:▼')">ユーザＩＤ ▼</button>
                <button v-else @click.prevent="getData('id:▲')">ユーザＩＤ ▲</button>
            </th>
            <th>
                <button v-if="changeBtn3" @click.prevent="getData('empno:▼')">社員コード ▼</button>
                <button v-else @click.prevent="getData('empno:▲')">社員コード ▲</button>
            </th>
            <th>
                <button v-if="changeBtn4" @click.prevent="getData('ename:▼')">社員名 ▼</button>
                <button v-else @click.prevent="getData('ename:▲')">社員名 ▲</button>
            </th>
            <th>
                <button v-if="changeBtn5" @click.prevent="getData('job:▼')">職種 ▼</button>
                <button v-else @click.prevent="getData('job:▲')">職種 ▲</button>
            </th>
            <th>
                <button v-if="changeBtn6" @click.prevent="getData('mgr:▼')">上司コード ▼</button>
                <button v-else @click.prevent="getData('mgr:▲')">上司コード ▲</button>
            </th>
            <th>
                <button v-if="changeBtn7" @click.prevent="getData('hiredate:▼')">入社日 ▼</button>
                <button v-else @click.prevent="getData('hiredate:▲')">入社日 ▲</button>
            </th>
            <th>
                <button v-if="changeBtn8" @click.prevent="getData('sal:▼')">給与 ▼</button>
                <button v-else @click.prevent="getData('sal:▲')">給与 ▲</button>
            </th>
            <th>
                <button v-if="changeBtn9" @click.prevent="getData('comm:▼')">歩合 ▼</button>
                <button v-else @click.prevent="getData('comm:▲')">歩合 ▲</button>
            </th>
            <th>
                <button v-if="changeBtn10" @click.prevent="getData('deptno:▼')">部署コード ▼</button>
                <button v-else @click.prevent="getData('deptno:▲')">部署コード ▲</button>
            </th>
            <th width="100">画像1</th>
            <th width="100">画像2</th>
            <th>役割</th>
            <th v-if="loginUseRole == 1 || loginUseRole == 3">編集</th>
            <th v-if="loginUseRole == 1 || loginUseRole == 3">削除</th>
        </tr>
    </thead>
    <tbody v-if="data !== ''">
        <tr v-for="(datas, index) in dispData" :key="index">
            <td>{{ datas.id }}</td>
            <td>{{ datas.empno }}</td>
            <td>{{ datas.ename }}</td>
            <td>{{ datas.job }}</td>
            <td>{{ datas.mgr }}</td>
            <td>{{ datas.hiredate }}</td>
            <td>{{ datas.sal }}</td>
            <td>{{ datas.comm }}</td>
            <td>{{ datas.deptno }}</td>
            <td>
                <img v-if="datas.img1" :src="'data:image/png;base64,' + datas.img1" class="listMyImg">
                <!-- <img v-else src="storage/img/no_image.jpg" class="listMyImg"> -->
                <v-btn height="20" width="60" v-if="loginUseRole == 1 || loginUseRole == 3" @click.prevent="editImg(index, 'image1')">変更</v-btn>
            </td>
            <td>
                <img v-if="datas.img2" :src="datas.img2" class="listMyImg">
                <!-- <img v-else src="storage/img/no_image.jpg" class="listMyImg"> -->
                <v-btn height="20" width="60" v-if="loginUseRole == 1 || loginUseRole == 3" @click.prevent="editImg(index, 'image2')">変更</v-btn>
            </td>
            <td>{{ datas.name }}</td>
            <td v-if="loginUseRole == 1 || loginUseRole == 3">
                <v-btn height="26" width="60" class="edit1" @click.prevent="edit(index)">編集</v-btn>
            </td>
            <td v-if="loginUseRole == 1 || loginUseRole == 3">
                <v-btn height="26" width="60" class="delete1" @click.prevent="d_delete(index)">削除</v-btn>
            </td>
        </tr>
    </tbody>
    <div v-else style="text-align:center">データがありません</div>
</table>
<br>

<!-- ページネーション -->
<ul v-if="data !== ''" class="pagination">
    <button v-if="page != 0" class="no_active" @click.prevent="pageChange(page)">prev</button>
    <li v-for="b_page in last_page" :key="b_page" class="getPageClass">
        <button v-if="b_page == page + 1" class="active" @click.prevent="pageChange(b_page)">{{ b_page }}</button>
        <button v-else class="no_active" @click.prevent="pageChange(b_page)">{{ b_page }}</button>
    </li>
    <button v-if="page != last_page - 1 && last_page != 1" class="no_active" @click.prevent="pageChange(page+2)">next</button>
</ul>

<!-- <pagination :dispNum="dispNum" /> -->


<!-- 編集ダイアログ -->
<v-dialog v-model="editDialog" width="1300">
    <v-card>
        <v-card-title class="card-title">
            編集
        </v-card-title>
        <p>変更前を確認し、変更後に入力後、更新ボタンを押してください。</p>
        <table>
            <tr>
                <th></th>
                <th>ユーザＩＤ</th>
                <th>社員コード</th>
                <th>社員名</th>
                <th>職種</th>
                <th>上司コード</th>
                <th>入社日</th>
                <th>給与</th>
                <th>歩合</th>
                <th>部署コード</th>
                <th>役割</th>
            </tr>
            <tr>
                <td>変更前</td>
                <td>{{ id }}</td>
                <td>{{ empno }}</td>
                <td>{{ ename }}</td>
                <td>{{ job }}</td>
                <td>{{ mgr }}</td>
                <td>{{ hiredate }}</td>
                <td>{{ sal }}</td>
                <td>{{ comm }}</td>
                <td>{{ deptno }}</td>
                <td>{{ roleName }}</td>
            </tr>
            <tr>
                <td>変更後</td>
                <td>
                    <input type="text" v-model="c_id" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_empno" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_ename" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_job" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_mgr" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_hiredate" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_sal" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_comm" class="resize">
                </td>
                <td>
                    <select v-model="c_deptno">
                        <option disabled value="">値を選択</option>
                        <option v-for="deptno in deptnoList">
                            <label>{{ deptno.deptno }}</label>
                        </option>
                    </select>
                </td>
                <td>
                    <select v-model="c_role">
                        <option disabled value="">値を選択</option>
                        <option v-for="roles in roleList">
                            <label>{{ roles.id }}:{{ roles.name }}</label>
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        <v-divider class="border-line"></v-divider>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click.prevent="edit1">更新</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>

<!-- 削除ダイアログ -->
<v-dialog v-model="deleteDialog" width="800">
    <v-card>
        <v-card-title class="card-title">
            削除
        </v-card-title>
        <p>以下のデータを削除してよろしいでしょうか</p>
        <table>
            <tr>
                <th>ユーザＩＤ</th>
                <th>社員コード</th>
                <th>社員名</th>
                <th>職種</th>
                <th>上司コード</th>
                <th>入社日</th>
                <th>給与</th>
                <th>歩合</th>
                <th>部署コード</th>
                <th>役割</th>
            </tr>
            <tr>
                <td>
                    <p>{{ id }}</p>
                </td>
                <td>
                    <p>{{ empno }}</p>
                </td>
                <td>
                    <p>{{ ename }}</p>
                </td>
                <td>
                    <p>{{ job }}</p>
                </td>
                <td>
                    <p>{{ mgr }}</p>
                </td>
                <td>
                    <p>{{ hiredate }}</p>
                </td>
                <td>
                    <p>{{ sal }}</p>
                </td>
                <td>
                    <p>{{ comm }}</p>
                </td>
                <td>
                    <p>{{ deptno }}</p>
                </td>
                <td>
                    <p>{{ roleName }}</p>
                </td>
            </tr>
        </table>
        <v-divider class="border-line"></v-divider>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click.prevent="delete1">削除</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>

<!-- CSVインポートダイアログ -->
<v-dialog v-model="csvImportDialog" width="450">
    <v-card>
        <v-card-title class="card-title">
            CSVインポート
        </v-card-title>
        ファイルを選択し、項目を合わせインポートボタンを押してください。
        <br>
        ※CSVファイルは、UTF-8のみ対応します。
            <vue-csv-import v-model="csv"
                :fields="{
                    id: { required: false, label: 'ユーザID' },
                    empno: { required: true, label: '社員コード(必須)' },
                    ename: { required: false, label: '社員名' },
                    job: { required: false, label: '職種' },
                    mgr: { required: false, label: '上司コード' },
                    hiredate: { required: false, label: '入社日' },
                    sal: { required: false, label: '給与' },
                    comm: { required: false, label: '歩合' },
                    deptno: { required: false, label: '部署コード' },
                    role: { required: false, label: '役割' }
                }">
                <p v-if="iError != ''" class="error-msg">{{ iError }}</p>
                <vue-csv-input></vue-csv-input>
                <vue-csv-map></vue-csv-map>
            </vue-csv-import>
        <v-divider class="border-line"></v-divider>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click.prevent="empCsvIn">インポート</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>

<!-- 画像変更ダイアログ -->
<v-dialog v-model="imgDialog" width="400">
    <v-card>
        <v-card-title class="card-title">
            画像変更
        </v-card-title>
        ファイルを選択してください
        <input type="file" @change="uploadImage">
        <div class="preview" v-if="imagePath !== ''">
        <img :src="imagePath">
        </div>
        <div v-if="imgError !== ''" class="error-msg">{{ imgError }}</div>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn v-if="check === true" color="primary" @click.prevent="imgSet">変更</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>


</template>
