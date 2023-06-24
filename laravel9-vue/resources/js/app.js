import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler';

import router from './router'                       // vue-router追加
import 'vuetify/lib/styles/main.sass'               // 追加
import { createVuetify } from 'vuetify'             // 追加
import App from './components/App.vue'
// import Dashboard from './components/Dashboard.vue';
// import List1 from './components/List1.vue';
// import List2 from './components/List2.vue';
// import Log from './components/Log.vue';
// import Schedule from './components/Schedule.vue';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();


//bladeのdivID:appに作成したApp（ソースの上でDashboard.vueをimport）を表示する
// createApp(App).mount("#app");

const vuetify = createVuetify();

const app = createApp(App);
app.use(router);
app.use(vuetify);
app.mount("#app");

// const app2 = createApp(List1);
// app2.use(vuetify);
// app2.mount("#app2");

// const app3 = createApp(List2);
// app3.use(vuetify);
// app3.mount("#app3");

// const app4 = createApp(Log);
// app4.use(vuetify);
// app4.mount("#app4");

// const app5 = createApp(Schedule);
// app5.use(vuetify);
// app5.mount("#app5");
