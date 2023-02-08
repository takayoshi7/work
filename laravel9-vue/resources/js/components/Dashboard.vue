<script setup>
import axios from 'axios';
import { ref, onMounted, watch } from 'vue';
import { useField, useForm } from 'vee-validate';
import * as yup from 'yup';

// 電話番号正規表現
const zipRegExp = /^\d{7}$/;
const zipRegExp2 = /^\d{3}\-?\d{4}$/;
const phoneRegExp = /^\d{10,11}$/;

const schema = yup.object({
    name: yup.string().required('必須の項目です。'),
    email: yup.string().required('必須の項目です。').email('メールアドレスの形式ではありません。'),
    zip: yup.string().matches(zipRegExp2, { message: '有効な郵便番号を入力してください。' }),
    phone: yup.string().matches(phoneRegExp, { message: '有効な電話番号を入力してください。' }),
});

const { errors } =useForm({
    validationSchema: schema,
});


// ログインユーザー情報取得
const data = ref([]);
const empno = ref("");
const name = ref("");
const img1 = ref("");
const img2 = ref("");
const email = ref("");
const post_code = ref("");
const address1 = ref("");
const address2 = ref("");
const phone_number = ref("");

// ログインユーザー情報取得
onMounted(async () => {
    try {
        const response = await axios.get('/profile');
        data.value = response.data[0];
        empno.value = response.data[0].empno;
        if (!response.data[0].ename) {
            name.value = response.data[0].id;
        } else {
            name.value = response.data[0].ename;
        }
        img1.value = "data:image/png;base64," + response.data[0].img1;
        img2.value = response.data[0].img2;
        email.value = response.data[0].email;
        post_code.value = response.data[0].post_code;
        address1.value = response.data[0].address1;
        address2.value = response.data[0].address2;
        phone_number.value = response.data[0].phone_number;
    } catch (error) {
        data.value = error.name; // 後で修正
    }
});

// 名前変更
const { value: rename, handleChange: nameHandleChange, meta: metaName } = useField('name');
const renameSet = () => {
    axios
        .post('/enameChange', { empno: empno.value, rename: rename.value })
        .then((res) =>
            // name.value = rename.value, renameDialog.value = false)
            location.reload())
        .catch((error) => alert("通信に失敗しました"));
};

const file = ref("");
const changeImg = ref("");
const imagePath1 = ref("");   // プレビュー用の画像パス
const imagePath2 = ref("");   // プレビュー用の画像パス
const img1Error = ref("");
const img2Error = ref("");
const imgType = ref("");
const imgSize = ref("");
// 画像ドラッグ&ドロップ
const isEnter = ref(false);
const dragEnter = () => {
    isEnter.value = true;
}
const dragLeave = () => {
    isEnter.value = false;
}
const dropFile = (val) => {
    isEnter.value = false;
    imgNum.value = val;
    file.value = event.dataTransfer.files[0];
    imgVali();
    preview();
}

// 画像ファイル選択
const uploadImage = (event) => {
    file.value = event.target.files[0];
    imgVali();
    preview();
}

// 画像バリデーション
function imgVali() {
    // ファイルの拡張子でバリデーション
    let type = file.value.type;
    if (type != 'image/jpg' && type != 'image/jpeg' && type != 'image/png') {
        imgType.value = false;
        imagePath1.value = "";
        imagePath2.value = "";
        if (imgNum.value === "image1") {
            img1Error.value = "ファイルが画像ではありません。対応拡張子(.jpg　.gif　.png)";
            img2Error.value = "";
        } else if (imgNum.value === "image2") {
            img2Error.value = "ファイルが画像ではありません。対応拡張子(.jpg　.gif　.png)";
            img1Error.value = "";
        }
    } else {
        imgType.value = true;
        img1Error.value = "";
        img2Error.value = "";
    }
    // // ファイルのサイズでバリデーション未完成
    imgSize.value = true;
}

// 画像プレビュー
function preview() {
    if (imgType.value && imgSize.value) {
        const reader = new FileReader()
        reader.readAsDataURL(file.value)
        reader.onloadend = () => {
            changeImg.value = reader.result
            if (imgNum.value === "image1") {
                imagePath1.value = URL.createObjectURL(file.value);
                imagePath2.value = "";
            } else if (imgNum.value === "image2") {
                imagePath2.value = URL.createObjectURL(file.value);
                imagePath1.value = "";
            }
        }
        // } else {
        //     alert("画像の取得に失敗しました。");
    }
}

// 選択した画像番号取得
const imgNum = ref("");
const selectImg = (event) => {
    imgNum.value = event;
}

// 画像変更
const img2Set = () => {
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

// 画像削除
const imgDelete = (val) => {
    axios
        .post("/imgDelete", { empno: empno.value, num: val })
        .then((res) => {
            if (res.data == 1) {
                location.reload();
            }
        })
        .catch((error) => alert("通信に失敗しました"));
}

// メールアドレス変更
const { value: change_email, handleChange: mailHandleChange, meta: metaMail } = useField('email');
const emailSet = () => {
    axios
        .post('/emailChange', { empno: empno.value, change_email: change_email.value })
        .then((res) => {
            if (res.data == "") {
                location.reload();
                // email.value = change_email.value;
                // change_emailDialog.value = false;
            } else {
                alert(res.data.message);
            }
        })
        .catch((error) => alert("通信に失敗しました"));
};

// 郵便番号から住所取得
const { value: change_post_code, handleChange: zipHandleChange, meta: metaZip } = useField('zip');
const getAddress = () => {
    let post_code = change_post_code.value.replace(/-/g, "");
    if (post_code.match(zipRegExp)) {
        axios
            .post('/getAddress', { change_post_code: post_code })
            .then((res) => {
                if (res.data.length == 1) {
                    change_post_code.value = post_code;
                    change_address1.value = res.data[0].pref + res.data[0].city + res.data[0].town;
                } else if (res.data.length > 1) {
                    change_post_code.value = post_code;
                    zip_select(res.data);
                } else {
                    alert(res.data.message);
                }
            })
            .catch((error) => alert("通信に失敗しました"));
    }
};

// 住所変更
const change_address1 = ref("");
const change_address2 = ref("");
const addressSet = () => {
    if (change_address1) {
        axios
            .post('/addressChange', {
                empno: empno.value,
                change_post_code: change_post_code.value,
                change_address1: change_address1.value,
                change_address2: change_address2.value
            })
            .then((res) => location.reload())
            //                  post_code.value = change_post_code.value,
            //                 address1.value = change_address1.value,
            //                 address2.value = change_address2.value,
            //                 change_post_code.value = "",
            //                 change_address1.value = "",
            //                 change_address2.value = ""
            // )
            .catch((error) => alert("通信に失敗しました"));
    }
};

// 電話番号変更
const { value: change_phone_number, handleChange: phoneHandleChange, meta: metaPhone } = useField('phone');
const phone_numberSet = () => {
    if (change_phone_number.value) {
        let phone_number2 = change_phone_number.value.replace(/-/g, "");
        axios
            .post('/phoneChange', { empno: empno.value, change_phone_number: phone_number2 })
            .then((res) => location.reload())
            // phone_number.value = phone_number2)
            .catch((error) => alert("通信に失敗しました"));
    }
};

// 名前変更ダイアログ
const renameDialog = ref(false);
const editEname = () => {
    renameDialog.value = true;
};

// メールアドレス変更ダイアログ
const change_emailDialog = ref(false);
const editEmail = () => {
    change_emailDialog.value = true;
};

// 住所選択ダイアログ
const zip_selectDialog = ref(false);
const zip = ref("");
const zip_select = (res) => {
    zip.value = res;
    zip_selectDialog.value = true;
};

// 選択した住所を入力欄に反映
const selectZip = ref("");
const zipSet = () => {
    change_address1.value = selectZip.value;
    zip_selectDialog.value = false;
}

</script>

<template>
    <div class="abc">
        <div class="nameclass">
            <p class="greeting">{{ name }}さんようこそ</p><br>
            <p class="info">【変更時は下のボタンから変更してください】</p>
            <v-btn @click="editEname">名前変更</v-btn>
        </div>
        <div class="imgclass">
            <div class="imgArea">
                <img v-if="img1" :src="img1" class="d_img">
                <!-- <img v-else src="storage/img/no_image.jpg" class="d_img"> -->
                <v-btn v-if="img1" @click="imgDelete('image1')">画像削除</v-btn>
                <div class="drop-zone" @dragenter="dragEnter" @dragleave="dragLeave" @dragover.prevent @drop.prevent="dropFile('image1')" :class="{enter: isEnter}">
                    画像をドラッグ＆ドロップ<br>もしくは<br>
                    <label class="label-btn">
                        ファイルを選択
                    <input type="file" style="display:none" @change="uploadImage" @click="selectImg('image1')">
                    </label>
                </div>
                <div class="preview" v-if="imagePath1 !== ''">
                <img :src="imagePath1">
                <v-btn height="30" width="100" color="teal" rounded @click="img2Set">変更</v-btn>
                </div>
                <div v-if="img1Error !== ''" class="error-msg">{{ img1Error }}</div>
            </div>
            <div class="imgArea">
                <img v-if="img2" :src="img2" class="d_img">
                <!-- <img v-else src="storage/img/no_image.jpg" class="d_img"> -->
                <v-btn v-if="img2" @click="imgDelete('image2')">画像削除</v-btn>
                <div class="drop-zone" @dragenter="dragEnter" @dragleave="dragLeave" @dragover.prevent @drop.prevent="dropFile('image2')" :class="{enter: isEnter}">
                    画像をドラッグ＆ドロップ<br>もしくは<br>
                    <label class="label-btn">
                        ファイルを選択
                    <input type="file" style="display:none" @change="uploadImage" @click="selectImg('image2')">
                    </label>
                </div>
                <div class="preview" v-if="imagePath2 !== ''">
                <img :src="imagePath2">
                <v-btn height="30" width="100" color="teal" rounded @click="img2Set">変更</v-btn>
                </div>
                <div v-if="img2Error !== ''" class="error-msg">{{ img2Error }}</div>
            </div>
        </div>

        <div class="emailclass">
            <b>メールアドレス：</b><br>
            <b v-if="email">{{ email }}</b>
            <b v-else>未登録</b><br>
            <v-btn @click="editEmail">変更</v-btn>
        </div>

        <div class="addressclass">
            <b>住所：<br>〒{{ post_code }}<br>{{ address1 }}{{ address2 }}</b>
            <br><br>
            <p class="info">【変更時は下記入力後、変更ボタンを押してください】</p>
            <input type="text" :value="change_post_code" :class="{ required: errors.zip }" class="input-post_code" @blur="zipHandleChange" placeholder="郵便番号">
            <v-btn height="26" width="80" color="blue" rounded class="zip-btn" @click.prevent="getAddress">住所取得</v-btn>
            <br>
            <span class="error-msg">{{ errors.zip }}</span>
            <input type="text" v-model="change_address1" class="input-address" placeholder="住所1">
            <input type="text" v-model="change_address2" class="input-address" placeholder="住所2">
        </div>
        <div id="addressbutton">
            <v-btn v-if="change_address1 != ''" @click.prevent="addressSet">変更</v-btn>
        </div>

        <div class="telclass">
            <b>電話番号：{{ phone_number }}</b>
            <br><br>
            <p class="info">【変更時は下記入力後、変更ボタンを押してください】</p>
            <p class="info">※ハイフンなし</p>
            <input type="text" :value="change_phone_number" :class="{ required: errors.phone }" class="input-phone_number" @blur="phoneHandleChange" placeholder="電話番号">
            <br>
            <span class="error-msg">{{ errors.phone }}</span>
            <v-btn v-if="!errors.phone && change_phone_number != null" @click.prevent="phone_numberSet">変更</v-btn>
        </div>
    </div>

    <!-- 名前変更ダイアログ -->
    <v-dialog v-model="renameDialog" width="500">
        <v-card>
            <v-card-title class="card-title">
                名前変更
            </v-card-title>
            <div class="change_text">
                <input type="text" :value="rename" :class="{ required: metaName.dirty && !metaName.valid }" class="input-name" @blur="nameHandleChange" placeholder="氏名">
            </div>
            <span class="error-msg">{{ errors.name }}</span>
            <v-divider class="border-line"></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn :disabled="!metaName.valid" color="primary" @click.prevent="renameSet">更新</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- メールアドレス変更ダイアログ -->
    <v-dialog v-model="change_emailDialog" width="500">
        <v-card>
            <v-card-title class="card-title">
                メールアドレス変更
            </v-card-title>
            <div class="change_text">
                <input type="text" :value="change_email" :class="{ required: metaMail.dirty && !metaMail.valid }" class="input-email" @blur="mailHandleChange" placeholder="メールアドレス">
            </div>
            <span class="error-msg">{{ errors.email }}</span>
            <v-divider class="border-line"></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn :disabled="!metaMail.valid" color="primary" @click.prevent="emailSet">更新</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- 郵便番号重複ダイアログ -->
    <v-dialog v-model="zip_selectDialog" width="500">
        <v-card>
            <v-card-title class="card-title">
                住所を選択してください
            </v-card-title>
            <div class="select">○選択中　[{{ selectZip }}]</div>
            <v-divider class="border-line"></v-divider>
            <ul class="scroll">
                <li v-for="zips in zip" :key="zips">
                    <label class="check">
                    <input type="radio" :value="zips.pref + zips.city + zips.town" v-model="selectZip">　{{ zips.pref }}{{ zips.city }}{{ zips.town }}</label>
                </li>
            </ul>
            <!-- Vuetify使用の場合 -->
            <!-- <v-radio-group v-model="selectZip" class="scroll">
                <v-radio v-for="zips in zip"
                    :key="zips"
                    :label="zips.pref + zips.city + zips.town"
                    :value="zips.pref + zips.city + zips.town">
                </v-radio>
            </v-radio-group> -->
            <v-divider class="border-line"></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click.prevent="zipSet">選択</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
