<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';

// スケジュール情報取得
const num          = ref();   // 1日の実行回数
const interval     = ref();   // 1日1回の場合の時間
const interval1    = ref();   // 1日2回の場合の1回目の時間
const interval2    = ref();   // 1日2回の場合の2回目の時間
const intervalHour = ref();   // 実行時間
const conditions   = ref();   // 削除されるまでの日数
onMounted(async () => {
    try {
        const response     = await axios.get('/scheduler');
        num.value          = response.data[0].num;
        interval.value     = response.data[0].interval;
        interval1.value    = response.data[0].interval1;
        interval2.value    = response.data[0].interval2;
        intervalHour.value = response.data[0].intervalhour;
        conditions.value   = response.data[0].conditions;
    } catch (error) {
        data.value = error.name; // 後で修正
    }
});

// 時間選択リスト取得
const hourList = ref();
onMounted(async () => {
    try {
        const response = await axios.get('/hourList');
        hourList.value = response.data;
    } catch (error) {
        alert("NG"); // 後で修正
    }
});

// 期間選択リスト取得
const timeList = ref();
onMounted(async () => {
    try {
        const response = await axios.get('/timeList');
        timeList.value = response.data;
    } catch (error) {
        alert("NG"); // 後で修正
    }
});

// 削除日数選択リスト取得
const dayList = ref();
onMounted(async () => {
    try {
        const response = await axios.get('/dayList');
        dayList.value = response.data;
    } catch (error) {
        alert("NG"); // 後で修正
    }
});

// 実行間隔設定
const c_num = ref("");
const c_interval = ref("");
const c_interval1 = ref("");
const c_interval2 = ref("");
const c_intervalHour = ref("");
const setting1 = () => {
    axios
        .post('/setting1', {
            c_num: c_num.value,
            c_interval: c_interval.value,
            c_interval1: c_interval1.value,
            c_interval2: c_interval2.value
        })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            }
        })
        .catch((error) => alert("通信に失敗しました"));
};
const setting2 = () => {
    axios
        .post('/setting2', {
            c_intervalHour: c_intervalHour.value
        })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            }
        })
        .catch((error) => alert("通信に失敗しました"));
};

// 実行間隔設定
const c_conditions = ref("");
const setting3 = () => {
    axios
        .post('/setting3', {
            c_conditions: c_conditions.value
        })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            }
        })
        .catch((error) => alert("通信に失敗しました"));
};

</script>

<template>
    <div>
        <p class="greeting">ログ削除スケジュール</p>
    </div>
    <br>
    <table>
        <th colspan="3">実行間隔</th>
        <tr>
            <td width="250" valign="top">
                <p>現在の設定</p>
                <br>
                <div v-if="num !== ''">
                    <p v-if="num != 0">回数<br>1日：
                    <strong>{{ num }}回</strong></p>
                    <br>
                    <p v-if="num != 0">実行時間</p>
                    <p v-if="num == 1"><strong>{{ interval }}時</strong></p>
                    <p v-if="num == 2"><strong>{{ interval1 }}時　</strong>＆<strong>　{{ interval2 }}時</strong></p>
                </div>
                <div v-else>
                    <p>実行期間</p>
                    <p><strong>{{ intervalHour }}分ごと</strong></p>
                </div>
            </td>
            <td width="250" valign="top">
                <p>１日の回数と時間で選択</p>
                <br>
                <div>
                    <dl>
                        <dt>回数(1回or2回)</dt>
                        <dd>
                            <select v-model="c_num">
                                <option disabled value="">選択</option>
                                <option><label>1</label></option>
                                <option><label>2</label></option>
                            </select>
                        </dd>
                    </dl>
                    <br>
                    <dl>
                        <dd v-if="c_num == 1">
                            <dt>時間</dt>
                            <select v-model="c_interval">
                                <option disabled value="">選択</option>
                                <option v-for="list in hourList" :key="list">
                                    <label>{{ list }}</label>
                                </option>
                            </select>
                        <br>
                        <v-btn color="teal" @click="setting1">設定</v-btn>
                        </dd>
                        <dd v-else-if="c_num == 2">
                            <dt>時間</dt>
                            <select v-model="c_interval1">
                                <option disabled value="">選択</option>
                                <option v-for="list in hourList" :key="list">
                                    <label>{{ list }}</label>
                                </option>
                            </select>
                            　＆　
                            <select v-model="c_interval2">
                                <option disabled value="">選択</option>
                                <option v-for="list in hourList" :key="list">
                                    <label>{{ list }}</label>
                                </option>
                            </select>
                            <br>
                            <v-btn color="teal" @click="setting1">設定</v-btn>
                        </dd>
                        <dd v-else>
                        </dd>
                    </dl>
                </div>
            </td>
            <td width="250" valign="top">
                <p>期間で選択</p>
                <br>
                <div>
                    <dl>
                        <dt>期間</dt>
                        <select v-model="c_intervalHour">
                            <option disabled value="">選択</option>
                            <option v-for="list in timeList" :key="list">
                                <label>{{ list }}</label>
                            </option>
                        </select>ごと
                    </dl>
                    <br>
                    <v-btn v-if="c_intervalHour != ''" color="teal" @click="setting2">設定</v-btn>
                </div>
            </td>
        </tr>
    </table>
    <br><br>
    <table>
        <th colspan="2">保存日数</th>
        <tr>
            <td width="250">
                <p>現在の設定</p><br>
                <p v-if="conditions !== ''">作成から<br><strong>{{ conditions }}日</strong><br>過ぎたら削除</p>
            </td>
            <td width="500">
                <dl>
                    <dt>日数</dt>
                    <select v-model="c_conditions">
                        <option disabled value="">選択</option>
                        <option v-for="list in dayList" :key="list">
                            <label>{{ list }}</label>
                        </option>
                    </select>
                </dl>
                <br>
                <v-btn v-if="c_conditions !== ''" color="teal" @click="setting3">設定</v-btn>
            </td>
        </tr>
    </table>

</template>
