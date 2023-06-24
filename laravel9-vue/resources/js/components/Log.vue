<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { saveAs } from 'file-saver';
import Encoding from 'encoding-japanese';

// 一覧リスト取得
const allData = ref("");        // 全データ
const data = ref("");           // 変動データ
const dispData = ref("");       // 表示用データ
const page = ref(0);            // 表示されてるページ
const last_page = ref();        // 最終ページ
const category = ref("access_time"); // 並びカテゴリ
const sort = ref("desc");        // 並び順
const dispNum = ref(10);         // １ページあたりの表示件数

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

// ログデータ取得
onMounted(async () => {
    try {
        const response = await axios.get('/logData');
        allData.value = response.data;
        data.value = allData.value;
        last_page.value = Math.ceil(data.value.length / dispNum.value);
        let sortNum = [];
        for (let i = 0; i < allData.value.length; i++) {
            sortNum[i] = allData.value[i].sort;
        }
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

// 時間選択リスト取得
const logList = ref();
onMounted(async () => {
    try {
        const response = await axios.get('/logList');
        logList.value = response.data;
    } catch (error) {
        alert("NG"); // 後で修正
    }
});

// 絞り込み検索ダイアログ
const searchDialog = ref(false);
const searchLog = () => {
    searchTitle.value = "";
    searchWord.value = "";
    firstDay.value = "";
    finalDay.value = "";
    firstTime.value = "00:00";
    finalTime.value = "23:59";
    searchDialog.value = true;
};

// データ取得
const changeBtn1 = ref(true);
const changeBtn2 = ref(true);
const changeBtn3 = ref(true);
const changeBtn4 = ref(true);
const changeBtn5 = ref(true);
const changeBtn6 = ref(true);
const changeBtn7 = ref(true);
const searchTitle = ref("");
const searchWord = ref("");
const firstDay = ref("");
const finalDay = ref("");
const firstTime = ref("00:00");
const finalTime = ref("23:59");
function getData(val) {
    if (val != "") {
        category.value = val.substring(0, val.indexOf(':'));
        const convert = val.substring(val.indexOf(':') + 1);
        if (convert == "▲") {
            sort.value = "asc";
        } else {
            sort.value = "desc";
        }
    }

    firstDay.value = firstDay.value + " " + firstTime.value + ":00";
    finalDay.value = finalDay.value + " " + finalTime.value + ":00";

    const select = [];
    for (let key in data.value) {
        if (searchTitle.value === "") {
            select.push(data.value[key]);
        } else if (searchTitle.value == "アクセスタイム") {
            if (data.value[key].access_time >= firstDay.value && data.value[key].access_time <= finalDay.value) {
                select.push(data.value[key]);
            }
        } else {
            switch (searchTitle.value) {
                case 'ログインID':
                    if (data.value[key].user_id.includes(searchWord.value)) {
                        select.push(data.value[key]);
                    }
                    break;
                case 'IPアドレス':
                    if (data.value[key].ip_address.includes(searchWord.value)) {
                        select.push(data.value[key]);
                    }
                    break;
                case 'ユーザーエージェント':
                    if (data.value[key].user_agent.includes(searchWord.value)) {
                        select.push(data.value[key]);
                    }
                    break;
                case 'セッションID':
                    if (data.value[key].session_id.includes(searchWord.value)) {
                        select.push(data.value[key]);
                    }
                    break;
                case 'アクセスURL':
                    if (data.value[key].access_url.includes(searchWord.value)) {
                        select.push(data.value[key]);
                    }
                    break;
                case '実行操作':
                    if (data.value[key].operation.includes(searchWord.value)) {
                        select.push(data.value[key]);
                    }
                    break;
            }
        }
    }
    if (sort.value == "asc") {
        switch (category.value) {
            case 'access_time':
                select.sort(function (a, b) {
                    let cateA = a.access_time.toUpperCase();
                    let cateB = b.access_time.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn1.value = !changeBtn1.value;
                break;
            case 'user_id':
                select.sort(function (a, b) {
                    let cateA = a.user_id.toUpperCase();
                    let cateB = b.user_id.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn2.value = !changeBtn2.value;
                break;
            case 'ip_address':
                select.sort(function (a, b) {
                    let cateA = a.ip_address.toUpperCase();
                    let cateB = b.ip_address.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn3.value = !changeBtn3.value;
                break;
            case 'user_agent':
                select.sort(function (a, b) {
                    let cateA = a.user_agent.toUpperCase();
                    let cateB = b.user_agent.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn4.value = !changeBtn4.value;
                break;
            case 'session_id':
                select.sort(function (a, b) {
                    let cateA = a.session_id.toUpperCase();
                    let cateB = b.session_id.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn5.value = !changeBtn5.value;
                break;
            case 'access_url':
                select.sort(function (a, b) {
                    let cateA = a.access_url.toUpperCase();
                    let cateB = b.access_url.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn6.value = !changeBtn6.value;
                break;
            case 'operation':
                select.sort(function (a, b) {
                    let cateA = a.operation.toUpperCase();
                    let cateB = b.operation.toUpperCase();
                    if (cateA < cateB) return -1;
                    if (cateA > cateB) return 1;
                    return 0;
                });
                changeBtn7.value = !changeBtn7.value;
                break;
        }
    } else {
        switch (category.value) {
            case 'access_time':
                select.sort(function (a, b) {
                    let cateA = a.access_time.toUpperCase();
                    let cateB = b.access_time.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn1.value = !changeBtn1.value;
                break;
            case 'user_id':
                select.sort(function (a, b) {
                    let cateA = a.user_id.toUpperCase();
                    let cateB = b.user_id.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn2.value = !changeBtn2.value;
                break;
            case 'ip_address':
                select.sort(function (a, b) {
                    let cateA = a.ip_address.toUpperCase();
                    let cateB = b.ip_address.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn3.value = !changeBtn3.value;
                break;
            case 'user_agent':
                select.sort(function (a, b) {
                    let cateA = a.user_agent.toUpperCase();
                    let cateB = b.user_agent.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn4.value = !changeBtn4.value;
                break;
            case 'session_id':
                select.sort(function (a, b) {
                    let cateA = a.session_id.toUpperCase();
                    let cateB = b.session_id.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn5.value = !changeBtn5.value;
                break;
            case 'access_url':
                select.sort(function (a, b) {
                    let cateA = a.access_url.toUpperCase();
                    let cateB = b.access_url.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn6.value = !changeBtn6.value;
                break;
            case 'operation':
                select.sort(function (a, b) {
                    let cateA = a.operation.toUpperCase();
                    let cateB = b.operation.toUpperCase();
                    if (cateA > cateB) return -1;
                    if (cateA < cateB) return 1;
                    return 0;
                });
                changeBtn7.value = !changeBtn7.value;
                break;
        }
    }
    data.value = select;
    disp();
    searchDialog.value = false;
}

// 表示リセット
function reset() {
    data.value = allData.value;
    disp();
    searchTitle.value = "";
    searchWord.value = "";
    firstDay.value = "";
    finalDay.value = "";
    firstTime.value = "";
    finalTime.value = "";
}

// CSVエクスポート
function logCsvEx() {
    let day1 = "";
    let day2 = "";
    if (firstDay != "") {
        day1 = firstDay.value + ' ' + firstTime.value + ':00';
        day2 = finalDay.value + ' ' + finalTime.value + ':00';
    } else {
        day1 = "";
        day2 = "";
    }
    axios
        .post('/logCsvEx', { responseType: 'blob', searchTitle: searchTitle.value, searchWord: searchWord.value, day1: day1, day2: day2, category: category.value, sort: sort.value })
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

// 文字列省略1
function slice(text) {
    return text.length > 40 ? text.slice(0, 40) + "…" : text;
};

// 文字列全表示1
const strDisp = ref(true);
function allDisp() {
    strDisp.value = !strDisp.value;
}

// 文字列省略2
function slice2(text) {
    return text.length > 20 ? text.slice(0, 20) + "…" : text;
};

// 文字列全表示2
const strDisp2 = ref(true);
function allDisp2() {
    strDisp2.value = !strDisp2.value;
}


</script>

<template>
    <table class="borderless">
        <thead>
            <tr>
                <td colspan="3" class="borderless">
                    <!-- 検索 -->
                    <v-btn height="30" class="searchBtn" @click.prevent="searchLog">絞り込み検索</v-btn>
                    <v-btn height="30" class="resetBtn" @click.prevent="reset">リセット</v-btn>
                </td>
                <td class="borderless">
                    <!-- CSVエクスポート -->
                    <v-btn class="csvExport" @click.prevent="logCsvEx">CSVエクスポート</v-btn>
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
                <th>
                    <button v-if="changeBtn1" @click.prevent="getData('access_time:▼')">アクセスタイム ▼</button>
                    <button v-else @click.prevent="getData('access_time:▲')">アクセスタイム ▲</button>
                </th>
                <th>
                    <button v-if="changeBtn2" @click.prevent="getData('user_id:▼')">ログインID ▼</button>
                    <button v-else @click.prevent="getData('user_id:▲')">ログインID ▲</button>
                </th>
                <th>
                    <button v-if="changeBtn3" @click.prevent="getData('ip_address:▼')">IPアドレス ▼</button>
                    <button v-else @click.prevent="getData('ip_address:▲')">IPアドレス ▲</button>
                </th>
                <th>
                    <button v-if="changeBtn4" @click.prevent="getData('user_agent:▼')">ユーザーエージェント ▼</button>
                    <button v-else @click.prevent="getData('user_agent:▲')">ユーザーエージェント ▲</button>
                </th>
                <th>
                    <button v-if="changeBtn5" @click.prevent="getData('session_id:▼')">セッションID ▼</button>
                    <button v-else @click.prevent="getData('session_id:▲')">セッションID ▲</button>
                </th>
                <th>
                    <button v-if="changeBtn6" @click.prevent="getData('access_url:▼')">アクセスURL ▼</button>
                    <button v-else @click.prevent="getData('access_url:▲')">アクセスURL ▲</button>
                </th>
                <th>
                    <button v-if="changeBtn7" @click.prevent="getData('operation:▼')">実行操作 ▼</button>
                    <button v-else @click.prevent="getData('operation:▲')">実行操作 ▲</button>
                </th>
            </tr>
        </thead>
        <tbody v-if="data !== ''">
            <tr v-for="(datas, index) in dispData" :key="index">
                <td>{{ datas.access_time }}</td>
                <td>{{ datas.user_id }}</td>
                <td>{{ datas.ip_address }}</td>
                <td v-if="strDisp" @click.prevent="allDisp">{{ slice(datas.user_agent) }}</td>
                <td v-else @click.prevent="allDisp">{{ datas.user_agent }}</td>
                <td v-if="strDisp2" @click.prevent="allDisp2">{{ slice2(datas.session_id) }}</td>
                <td v-else @click.prevent="allDisp2">{{ datas.session_id }}</td>
                <td>{{ datas.access_url }}</td>
                <td>{{ datas.operation }}</td>
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
        <button v-if="page != last_page - 1 && last_page != 1" class="no_active"
            @click.prevent="pageChange(page+2)">next</button>
    </ul>

    <!-- <ul v-if="data !== ''" class="pagination">
        <button v-if="page != 0" class="no_active" @click.prevent="pageChange(page)">prev</button>
        <button v-if="page == 0" class="active" @click.prevent="pageChange(1)">1</button>
        <button v-else class="no_active" @click.prevent="pageChange(1)">1</button>
        <p v-if="page >= 3">･･･</p>
        <button v-if="page > 1" class="no_active" @click.prevent="pageChange(page)">{{ page }}</button>
        <button v-if="page == page && page != 0 && page + 1 != last_page" class="active" @click.prevent="pageChange(page + 1)">{{ page + 1 }}</button>
        <button v-if="page < last_page - 2" class="no_active" @click.prevent="pageChange(page + 2)">{{ page + 2 }}</button>
        <p v-if="page <= page + 2 && page < last_page - 3">･･･</p>
        <button v-if="page + 1 == last_page" class="active" @click.prevent="pageChange(last_page)">{{ last_page }}</button>
        <button v-else class="no_active" @click.prevent="pageChange(last_page)">{{ last_page }}</button>
        <button v-if="page != last_page - 1 && last_page != 1" class="no_active" @click.prevent="pageChange(page + 2)">next</button>
    </ul> -->

    <!-- 絞り込み検索ダイアログ -->
    <v-dialog v-model="searchDialog" width="400">
        <v-card>
            <v-card-title class="card-title">
                絞り込み検索
            </v-card-title>
            <div style="margin-left: 40px;">
            <dl>
                <dd>
                    <dt class="select">項目選択</dt>
                    <select v-model="searchTitle">
                        <option disabled value="">選択</option>
                        <option v-for="list in logList">
                            <label>{{ list }}</label>
                        </option>
                    </select>
                </dd>
            </dl>
            <div id="logsearcherror"></div>
            <br>
            <dl v-if="searchTitle !== 'アクセスタイム' && searchTitle !== ''">
                <dt class="select">検索ワード</dt>
                <input type="text" v-model="searchWord" class="searchWord">
            </dl>
            <dl v-else-if="searchTitle === 'アクセスタイム'">
                <dt class="select">月日(必須入力)</dt>
                <input type="date" v-model="firstDay">～<input type="date" v-model="finalDay"><br>
                <dt class="select">時間(任意入力)</dt>
                <input type="time" v-model="firstTime">～<input type="time" v-model="finalTime">
            </dl>
            <div id="logsearcherror2"></div>
            </div>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click.prevent="getData('')">検索</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>


</template>
