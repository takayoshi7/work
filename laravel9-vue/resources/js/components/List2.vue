<script setup>
import axios, { all, formToJSON } from 'axios';
import { ref, onMounted, watch } from 'vue';
import { VueCsvImportPlugin } from "vue-csv-import";
import { VueCsvToggleHeaders, VueCsvSubmit, VueCsvMap, VueCsvInput, VueCsvErrors, VueCsvImport } from 'vue-csv-import';
import { saveAs } from 'file-saver';
import Encoding from 'encoding-japanese';

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

// 一覧リスト取得
const allData = ref("");        // 全データ
const data = ref("");           // 変動データ
const dispData = ref("");       // 表示用データ
const page = ref(0);            // 表示されてるページ
const last_page = ref();        // 最終ページ
const category = ref("deptno"); // 並びカテゴリ
const sort = ref("asc");        // 並び順
const dispNum = ref(3);         // １ページあたりの表示件数
const maxSortNum = ref();       // 並び順の最大番号

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
        const response = await axios.get('/list2Data');
        allData.value = response.data.data;
        data.value = allData.value;
        let sortNum = [];
        for (let i = 0; i < allData.value.length; i++) {
            sortNum[i] = allData.value[i].sort;
        }
        maxSortNum.value = Math.max.apply(null, sortNum);
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
const searchName = ref("");
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
        if (data.value[key].dname.includes(searchName.value)) {
            select.push(data.value[key]);
        }
    }
    if (sort.value == "asc") {
        switch (category.value) {
            case 'deptno':
                select.sort(function (a, b) {
                    let cateA = a.deptno;
                    let cateB = b.deptno;
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn2.value = !changeBtn2.value;
                break;
            case 'dname':
                select.sort(function (a, b) {
                    let cateA = a.dname.toUpperCase();
                    let cateB = b.dname.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn3.value = !changeBtn3.value;
                break;
            case 'loc':
                select.sort(function (a, b) {
                    let cateA = a.loc.toUpperCase();
                    let cateB = b.loc.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn4.value = !changeBtn4.value;
                break;
            case 'sortNum':
                select.sort(function (a, b) {
                    let cateA = a.sort;
                    let cateB = b.sort;
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn5.value = !changeBtn5.value;
                break;
        }
    } else {
        switch (category.value) {
            case 'deptno':
                select.sort(function (a, b) {
                    let cateA = a.deptno;
                    let cateB = b.deptno;
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn2.value = !changeBtn2.value;
                break;
            case 'dname':
                select.sort(function (a, b) {
                    let cateA = a.dname.toUpperCase();
                    let cateB = b.dname.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn3.value = !changeBtn3.value;
                break;
            case 'loc':
                select.sort(function (a, b) {
                    let cateA = a.loc.toUpperCase();
                    let cateB = b.loc.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn4.value = !changeBtn4.value;
                break;
            case 'sortNum':
                select.sort(function (a, b) {
                    let cateA = a.sort;
                    let cateB = b.sort;
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn5.value = !changeBtn5.value;
                break;
        }
    }
    data.value = select;
    disp();
    // searchName.value = "";
}

// 表示リセット
function reset() {
    data.value = allData.value;
    disp();
    searchName.value = "";
}

// 追加フォーム表示
const deptno = ref("");
const dname = ref("");
const loc = ref("");
const d_sort = ref("");
const c_deptno = ref("80");
const c_dname = ref("事務");
const c_loc = ref("北海道");
const c_sort = ref();
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
function insert2() {
    axios
        .post('/insert2', {
            c_deptno: c_deptno.value,
            c_dname: c_dname.value,
            c_loc: c_loc.value,
            maxSortNum: maxSortNum.value
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
    deptno.value = data.value[index + (page.value * dispNum.value)].deptno;
    dname.value = data.value[index + (page.value * dispNum.value)].dname;
    loc.value = data.value[index + (page.value * dispNum.value)].loc;
    d_sort.value = data.value[index + (page.value * dispNum.value)].sort;
    editDialog.value = true;
};

// 編集
function edit2() {
    axios
        .post('/edit2', {
            deptno: deptno.value,
            c_deptno: c_deptno.value,
            c_dname: c_dname.value,
            c_loc: c_loc.value,
            c_sort: c_sort.value,
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
    deptno.value = data.value[index + (page.value * dispNum.value)].deptno;
    dname.value = data.value[index + (page.value * dispNum.value)].dname;
    loc.value = data.value[index + (page.value * dispNum.value)].loc;
    d_sort.value = data.value[index + (page.value * dispNum.value)].sort;
    deleteDialog.value = true;
};

// 削除
function delete2() {
    axios
        .post('/delete2', { deptno: deptno.value })
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

// CSVインポート　UTF-8のみ
const iError = ref([]);
function deptCsvIn(event) {
    // const file = event.target.files[0];
    // const reader = new FileReader();
    // let csvData = [];

    // reader.onload = () => {
    //     const lines = reader.result.split("\n");
    //     for (let i = 0; i < lines.length; i++) {
    //         if (lines[i] != "") {
    //             csvData.push(lines[i].split(","));
    //         }
    //     }
    //     for (let z = 0; z < csvData.length; z++) {
    //         const worker = {
    //             deptno: csvData[z][0],
    //             dname: csvData[z][1],
    //             loc: csvData[z][2],
    //             sort: csvData[z][3].replace("\r", "")
    //         };
    //         csv.value.push(worker);
    //     };
    // }
    // reader.readAsText(file);

    axios
        .post('/deptCsvIn', { csv: csv.value })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            } else {
                console.log(res.data);
                for (let i = 0; i < res.data.valA.length; i++) {
                    iError.value.push(res.data.valB[i] + "行目の" + res.data.valA[i] + "が正しくありません");
                }
            }
        })
        .catch((error) => alert("通信に失敗しました"));
};

// CSVエクスポート
function deptCsvEx() {
    axios
        .post('/deptCsvEx', {responseType: 'blob', searchName: searchName.value, category: category.value, sort: sort.value})
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

// ドラッグ・ドロップで並び替え
const dragIndex = ref(null);
const dragstart = (index) => {
    dragIndex.value = index;
};
const dragEnter = (index) => {
    if (index === dragIndex) return;
    const deleteElement = dispData.value.splice(dragIndex.value, 1)[0];
    dispData.value.splice(index, 0, deleteElement);
    dragIndex.value = index;
}
const dragEnd = () => {
    const deptnoList = [];
    for (let i = 0; i < dispData.value.length; i++) {
        deptnoList[i] = dispData.value[i].deptno;
    }
    axios
        .post('/sortNumChange', { deptnoList: deptnoList, page: page.value, dispNum: dispNum.value })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            }
        })
        .catch((error) => alert("通信に失敗しました"));
    dragIndex.value = null;
};
</script>

<template>
<table class="borderless">
    <thead>
        <tr>
            <td colspan="3" class="borderless">
                <!-- 検索 -->
                <p style="text-align: left;">部署名で絞り込みができます。</p>
                部署名
                <input type="text" placeholder="部署名" v-model="searchName" class="searchName">
                <v-btn height="30" class="searchBtn" @click.prevent="getData('')">検索</v-btn>
                <v-btn height="30" class="resetBtn" @click.prevent="reset">リセット</v-btn>
            </td>
            <td colspan="3" class="borderless">
                <!-- 表示件数変更 -->
                <p style="text-align: left;">１ページあたりの表示件数を変更できます。</p>
                表示件数：
                <input type="number" v-model="dispNum" class="dispNum" min="1" pattern="^[0-9]+$">
                <v-btn class="dispNumBtn" @click.prevent="disp">変更</v-btn>
            </td>
        </tr>
        <tr>
            <td colspan="6" class="borderless">
                <!-- CSVインポート -->
                <v-btn class="csvImport" @click.prevent="csv_i">CSVインポート</v-btn>

                <!-- CSVエクスポート -->
                <v-btn class="csvExport" @click.prevent="deptCsvEx">CSVエクスポート</v-btn>
            </td>
        </tr>
        <tr>
            <td colspan="6" class="borderless">
                <!-- 追加 -->
                <div v-if="loginUseRole == 1 || loginUseRole == 3">
                    <v-btn v-if="changeBtn" class="insert" @click.prevent="insert">部署追加</v-btn>
                    <v-btn v-else class="insert" @click.prevent="toggle" style="margin-bottom: 14px;">戻す</v-btn>
                    <div v-if="forms != ''">
                        <p>新しく部署を追加できます。下記項目に入力後、追加ボタンを押してください。</p>
                        <p>※並び順は一番最後に追加されます。</p>
                        <table>
                            <tr>
                                <th>部署コード</th>
                                <th>部署名</th>
                                <th>勤務地</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" placeholder="部署コード" v-model="c_deptno" class="resize">
                                </td>
                                <td>
                                    <input type="text" placeholder="部署名" v-model="c_dname" class="resize">
                                </td>
                                <td>
                                    <input type="text" placeholder="勤務地" v-model="c_loc" class="resize">
                                </td>
                            </tr>
                        </table>
                    </div>
                    <v-btn v-if="forms != ''" @click.prevent="insert2" style="margin-bottom: 14px;">追加</v-btn>
                </div>
            </td>
        </tr>
        <tr>
            <th>
                <button v-if="changeBtn2" @click.prevent="getData('deptno:▼')">部署コード ▼</button>
                <button v-else @click.prevent="getData('deptno:▲')">部署コード ▲</button>
            </th>
            <th>
                <button v-if="changeBtn3" @click.prevent="getData('dname:▼')">部署名 ▼</button>
                <button v-else @click.prevent="getData('dname:▲')">部署名 ▲</button>
            </th>
            <th>
                <button v-if="changeBtn4" @click.prevent="getData('loc:▼')">勤務地 ▼</button>
                <button v-else @click.prevent="getData('loc:▲')">勤務地 ▲</button>
            </th>
            <th>
                <button v-if="changeBtn5" @click.prevent="getData('sortNum:▼')">並び順 ▼</button>
                <button v-else @click.prevent="getData('sortNum:▲')">並び順 ▲</button>
            </th>
            <th v-if="loginUseRole == 1">編集</th>
            <th v-if="loginUseRole == 1">削除</th>
        </tr>
    </thead>
    <tbody v-if="data !== ''">
        <tr v-for="(datas, index) in dispData" :key="index"
            :draggable="true" @dragstart="dragstart(index)" @dragenter="dragEnter(index)" @dragover.prevent @dragend="dragEnd"
            v-bind:class="[index === dragIndex ? 'dragging' : '']">
            <td>{{ datas.deptno }}</td>
            <td>{{ datas.dname }}</td>
            <td>{{ datas.loc }}</td>
            <td>{{ datas.sort }}</td>
            <td v-if="loginUseRole == 1">
                <v-btn height="26" width="60" class="edit1" @click.prevent="edit(index)">編集</v-btn>
            </td>
            <td v-if="loginUseRole == 1">
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
                <th>部署コード</th>
                <th>部署名</th>
                <th>勤務地</th>
                <th>並び順</th>
            </tr>
            <tr>
                <td>変更前</td>
                <td>{{ deptno }}</td>
                <td>{{ dname }}</td>
                <td>{{ loc }}</td>
                <td>{{ d_sort }}</td>
            </tr>
            <tr>
                <td>変更後</td>
                <td>
                    <input type="text" v-model="c_deptno" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_dname" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_loc" class="resize">
                </td>
                <td>
                    <input type="text" v-model="c_sort" class="resize">
                </td>
            </tr>
        </table>
        <v-divider class="border-line"></v-divider>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click.prevent="edit2">更新</v-btn>
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
                <th>部署コード</th>
                <th>部署名</th>
                <th>勤務地</th>
                <th>並び順</th>
            </tr>
            <tr>
                <td>{{ deptno }}</td>
                <td>{{ dname }}</td>
                <td>{{ loc }}</td>
                <td>{{ d_sort }}</td>
            </tr>
        </table>
        <v-divider class="border-line"></v-divider>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click.prevent="delete2">削除</v-btn>
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
        <!-- <input type="file" @change="deptCsvIn"> -->
        <vue-csv-import v-model="csv" :fields="{
            deptno: { required: true, label: '部署コード(必須)' },
            dname: { required: false, label: '部署名' },
            loc: { required: false, label: '勤務地' },
            sort: { required: false, label: '並び順' }
        }">
        <vue-csv-input></vue-csv-input>
        <vue-csv-map></vue-csv-map>
        </vue-csv-import>
        <!-- {{ csv }} -->
        <p v-if="iError != ''" class="error-msg">{{ iError }}</p>
        <v-divider class="border-line"></v-divider>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" @click.prevent="deptCsvIn">インポート</v-btn>
        </v-card-actions>
    </v-card>
</v-dialog>

</template>
