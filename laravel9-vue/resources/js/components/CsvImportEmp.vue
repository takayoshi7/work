<script setup>
import { ref } from 'vue';
import { VueCsvImportPlugin } from "vue-csv-import";
import { VueCsvToggleHeaders, VueCsvSubmit, VueCsvMap, VueCsvInput, VueCsvErrors, VueCsvImport } from 'vue-csv-import';

const csv = ref([]);

</script>

<template>
    <vue-csv-import v-model="csv"
        :fields="{
            name: { required: false, label: 'Name' },
            age: { required: true, label: 'Age' }
        }"
    >
    <vue-csv-toggle-headers></vue-csv-toggle-headers>
    <vue-csv-errors></vue-csv-errors>
    <vue-csv-input></vue-csv-input>
    <vue-csv-map></vue-csv-map>
    </vue-csv-import>
</template>
