import {defineStore} from 'pinia'
import {createLog, deleteLog, fetchLogs, fetchStats, updateLog} from "@/services/timelog.service.js";

export const useAppStore = defineStore('app', {
    state: () => (
        {
            menuPoints: [
                {
                    path: `/admin/timelogs-list`,
                    title: 'TimeLogs',
                },
                {
                    path: `/admin/reports`,
                    title: 'Reports',
                },
            ],
            currentPage: 1,
            totalLogs: 0,
            statistics: [],
            pageSize: 10,
            timeLogs: [],
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
        async fetchTimeLogs() {
            const res = await fetchLogs(this.pageSize, this.currentPage);
            this.timeLogs = res?.data ?? [];
            this.totalLogs = res?.meta.total ?? 0;
            this.currentPage = res?.meta.page ?? 0;
            this.pageSize = res?.meta.pageSize ?? 0;
        },
        toPage(page) {
            this.currentPage = page;
            this.fetchTimeLogs();
        },
        async updateLog(id, payload) {
            this.decorateRequest(() => updateLog(id, payload));
        },
        async removeLog(id) {
            this.decorateRequest(() => deleteLog(id))
        },
        async createLog(payload) {
            this.decorateRequest(() => createLog(payload))
        },
        async decorateRequest(callback) {
            this.loading = true;
            const res = await callback();
            this.loading = false;

            if (res?.success === true) {
                this.addSnackMessage({type: 'success' , title: 'TimeLog', message: 'Successfull Log delete'})
            } else {
                this.addSnackMessage({type: 'error' , title: 'TimeLog', message: res.detail ?? 'Error has occured'})
            }

            this.fetchTimeLogs();
            return res;
        },
        async loadStats(from = null, to = null) {
            this.statistics = (await fetchStats(from, to)).data?.years ?? [];
        }

    },
    getters: {
        getMenuPoints: (state) => {
            return state.menuPoints;
        }
    }
})
