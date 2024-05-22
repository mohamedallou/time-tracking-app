<template>
    <v-card variant="outlined">
        <v-card-title>
            Time Logs
        </v-card-title>
        <v-card-text>
            <v-expansion-panels>
                <v-expansion-panel v-for="timeLog in logs"
                >
                    <v-expansion-panel-title>
                        <v-row justify="space-between">
                            <v-col cols="8">
                                Log # {{timeLog.id}}
                            </v-col>
                            <v-col cols="2">
                                <v-btn icon="mdi-trash-can" variant="outlined" @click.stop="removeLog(timeLog.id)" class="mr-5"></v-btn>
                                <v-btn icon="mdi-content-save-all" variant="outlined" @click.stop="updateLog(timeLog.id, timeLog)">
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-expansion-panel-title>
                    <v-expansion-panel-text>
                        <v-text-field
                            :label="'from'"
                            v-model="timeLog.start"
                            type="datetime-local"
                        >
                        </v-text-field>
                        <v-text-field :label="'to'" v-model="timeLog.end" type="datetime-local">
                        </v-text-field>
                    </v-expansion-panel-text>
                </v-expansion-panel>
            </v-expansion-panels>
            <v-row class="mt-10">
                <v-col>
                    <v-btn icon="mdi-plus" color="primary" @click="dialog = true">
                    </v-btn>
                </v-col>
            </v-row>
            <v-row>
                <v-col>
                    <v-pagination :length="pageCount" v-model="currentPage"></v-pagination>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>
    <v-dialog v-model="dialog">
        <v-card title="Create">
            <v-card-text>
                <v-text-field
                    :label="'from'"
                    v-model="newTimeLogStart"
                    type="datetime-local"
                >
                </v-text-field>
                <v-text-field :label="'to'" v-model="newTimeLogEnd" type="datetime-local">
                </v-text-field>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    text="Cancel"
                    @click="dialog = false"
                ></v-btn>
                <v-btn
                    text="Create"
                    @click="() => {
                        dialog = false;
                        this.createLog();
                    }"
                ></v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import {useAppStore} from "@/stores/app.store.js";

export default {
    name: "TimeLogOverview",
    data() {
        return {
            dialog: false,
            newTimeLogStart: null,
            newTimeLogEnd: null,
            currentPage: 1,
        }
    },
    computed: {
        logs() {
            return useAppStore().timeLogs
        },
        pageCount() {
            return Math.ceil(useAppStore().totalLogs / useAppStore().pageSize);
        }
    },
    methods: {
        useAppStore,
        updateLog(id, payload) {
            useAppStore().updateLog(id, payload);
        },
        removeLog(id) {
            useAppStore().removeLog(id);
        },
        createLog() {
            useAppStore().createLog({start: this.newTimeLogStart, end: this.newTimeLogEnd});
            this.newTimeLogStart = '';
            this.newTimeLogEnd = '';
        }
    },
    mounted() {
        useAppStore().fetchTimeLogs();
        this.currentPage = useAppStore().currentPage;
    },
    watch: {
        currentPage(newVal, oldVal) {
            if (newVal === oldVal) {
                return;
            }

            useAppStore().toPage(newVal);
        }
    }
}
</script>

<style scoped>

</style>