<template>
    <v-card>
        <v-card-title>
            Time Logs Reports
        </v-card-title>
        <v-card-text>
            <v-row aria-roledescription="Filter for the work log report">
                <v-col cols="12" md="6">
                    <v-text-field type="datetime-local" :label="'from'" v-model="from"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field type="datetime-local" :label="'to'" v-model="to"></v-text-field>
                </v-col>
            </v-row>
            <v-row aria-roledescription="Select the year">
                <v-col>
                    <v-select label="Year" :items="years" v-model="selectedYear">
                    </v-select>
                </v-col>
            </v-row>
            <v-row>
                <v-col>
                    <a :href="exportLink" download>
                        <v-btn icon="mdi-download-circle" color="primary">
                        </v-btn>
                    </a>
                </v-col>
            </v-row>
            <h2>Daily View</h2>
            <Bar :data="data" :options="options" :key="loaded"/>

            <h2>Monthly View</h2>
            <Bar :data="monthlyData" :options="options" :key="loadedMonthly"/>

        </v-card-text>
    </v-card>
</template>

<script>
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'
import { Bar } from 'vue-chartjs'
import {useAppStore} from "@/stores/app.store.js";

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

export default {
    name: 'TimeLogReportView',
    components: {
        Bar
    },
    data() {
        return {
            from: null,
            to: null,
            years: [],
            selectedYear: '',
            loaded: 0,
            loadedMonthly: 0,
            data: {
                labels: [],
                datasets: [{ data: [], label: 'Worked hours'}],
            },
            monthlyData: {
                labels: [],
                datasets: [{ data: [], label: 'Worked hours'}],
            },
            options: {
                responsive: true,
                indexAxis: 'y',
            }
        }
    },
    computed: {
        exportLink() {
            let url = '/api/timelog/export/csv?page=1&pageSize=1000';
            if (this.from) {
                url += `&from=${this.from}`
            }

            if (this.to) {
                url += `&to=${this.to}`
            }

            return url;
        },
    },
    methods: {
        async loadStatistics() {
            this.loaded++;
            this.loadedMonthly++;
            await useAppStore().loadStats(this.from, this.to);

            this.years = Object.keys(useAppStore().statistics);

            // Check if the selected year can still be applied to the received results

            if (!this.years.includes(this.selectedYear)) {
                this.selectedYear = [this.years[0]];
            }

            this.reinitializeDataWithNewYear();
            // Force Chart rerender
            this.loaded++;
            this.loadedMonthly++;
        },
        reinitializeDataWithNewYear() {
            this.data.labels = Object.keys(useAppStore().statistics[this.selectedYear].days);
            this.data.datasets = [
                { data: Object.values(useAppStore().statistics[this.selectedYear].days), label: 'Worked hours'}
            ];
            // Force Chart rerender
            this.loaded++;
            this.loadedMonthly++;
        },
        reinitializeMonthlyDataWithNewYear() {
            this.monthlyData.labels = Object.keys(useAppStore().statistics[this.selectedYear].months);
            this.monthlyData.datasets = [
                { data: Object.values(useAppStore().statistics[this.selectedYear].months), label: 'Worked hours'}
            ];
            // Force Chart rerender
            this.loaded++;
            this.loadedMonthly++;
        }
    },
    async mounted() {
        await this.loadStatistics();

        this.years = Object.keys(useAppStore().statistics);
        this.selectedYear = [this.years[0]];
    },
    watch: {
        from(newVal, oldVal) {
            if (newVal === oldVal) {
                return;
            }

            this.loadStatistics();
        },
        to(newVal, oldVal) {
            if (newVal === oldVal) {
                return;
            }

            this.loadStatistics();
        },
        selectedYear(newVal, oldVal) {
            if (newVal === oldVal) {
                return;
            }

            this.reinitializeDataWithNewYear();
            this.reinitializeMonthlyDataWithNewYear();
        }
    }
}
</script>

<style scoped>

</style>