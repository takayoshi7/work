<script setup>
import { ref } from 'vue';

const props = defineProps({
    dispNum: String,
});
const dispNum = ref(props.dispNum);


</script>

<template>
    <p style="text-align:center">{{ props.dispNum }}</p>
</template>
