<template>
    <v-alert :text="message" :type="type" :title="$t('message.notification')"></v-alert>
</template>

<script>
export default {
    name: "MessageBox",
    props: {
        message: {
            required: true,
            type: String
        },
        type: {
            required: true,
            type: String
        },
    }
}
</script>

<style scoped>

</style>