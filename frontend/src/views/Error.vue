<template>
    <v-container fluid>
        <MessageBox type="error" :message="message"></MessageBox>
    </v-container>
</template>

<script>
import MessageBox from "@/components/MessageBox.vue";

export default {
    name: "Error",
    components: {MessageBox},
    computed: {
        message() {
            const message = new URL(window.location).searchParams.get('message');
            return message.replaceAll('"', '');
        }
    }
}
</script>

<style scoped>

</style>