<template>
    <v-row justify="space-evenly" class="text-h4">
        <v-col cols="2" class="hours">
            {{hoursFormatted}}
        </v-col>
        <v-col cols="2">
            :
        </v-col>
        <v-col cols="2" class="minutes">
            {{minutesFormatted}}
        </v-col>
        <v-col cols="2">
            :
        </v-col>
        <v-col cols="2" class="seconds">
            {{secondsFormatted}}
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: "StopWatch",
    data() {
        return {
            hours: 0,
            minutes: 0,
            seconds: 0,
            intervalId: null,
            stopped: true,
        }
    },
    computed: {
        hoursFormatted() {
            return this.hours.toString().padStart(2, '0');
        },
        minutesFormatted() {
            return this.minutes.toString().padStart(2, '0');
        },
        secondsFormatted() {
            return this.seconds.toString().padStart(2, '0');
        }
    },
    methods: {
        runClock() {
            this.seconds++
            if (this.seconds >= 60)  {
                this.seconds = 0;
                this.minutes++;
            }

            if (this.minutes >= 60) {
                this.minutes = 0;
                this.hours++;
            }
        },
        stopClock() {
            clearInterval(this.intervalId);
        },
        startClock() {
            this.resetClock();
            this.intervalId = setInterval(() => {
                this.runClock();
            }, 1000)
        },
        resetClock() {
            this.seconds = 0;
            this.minutes = 0;
            this.hours = 0;
        }
    },
    mounted() {
    },
    beforeUnmount() {
        this.stopClock()
    }
}
</script>

<style scoped>

</style>