import {defineStore} from 'pinia'
import {createLog, deleteLog, fetchLogs, fetchStats, updateLog} from "@/services/timelog.service.js";

// This store is specialized in the front user time tracking
export const useTrackingStore = defineStore('tracking', {
    state: () => (
        {
            currentLog: null,
            loading: false,
            snackType: '', //error or success
            snackMessage: '',
            snackTitle: '',
            showSnack: false,
        }),
    actions: {
        addSnackMessage({type, title, message}) {
            this.snackType = type;
            this.snackTitle = title;
            this.snackMessage = message;
            this.showSnack = true;
        },
        async fetchTimeLog() {
            const res = await fetchLogs(this.currentLog.id);
        },
        async startTracking() {
            this.currentLog = (await this.decorateRequest(() => createLog({
                start: new Date()
            }))).data ?? null;
        },
        async stopTracking() {
            this.currentLog = (await this.decorateRequest(() => updateLog(this.currentLog.id, {
                start: this.currentLog.start,
                end: new Date()
            }))).data ?? null;
        },
        resetTracking() {
            this.currentLog = null;
        },
        async decorateRequest(callback) {
            this.loading = true;
            const res = await callback();
            this.loading = false;

            if (res?.success === true) {
                this.addSnackMessage({type: 'success' , title: 'Time Log', message: 'Successfull Log delete'})
            } else {
                this.addSnackMessage({type: 'error' , title: 'Time Log', message: 'Error has occured'})
            }

            return res;
        },
        async loadStats(from = null, to = null) {
            this.statistics = (await fetchStats(from, to)).data?.years ?? [];
        }

    },
})
